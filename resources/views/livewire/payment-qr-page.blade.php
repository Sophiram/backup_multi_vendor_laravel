<?php

use Livewire\Volt\Component;
use App\Models\Order;
use App\Services\KhqrService;

new class extends Component {
    public $order;
    public $qrCodeString;
    public $md5;
    public $remainingTime = 300;
    public $isPaid = false;

    public function mount(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($order->payment && $order->payment->status === 'paid') {
            $this->isPaid = true;
        }

        $this->order = $order;

        $service = new KhqrService();
        $result = $service->generateQr($this->order->total_amount, 'USD', $this->order->order_number);

        if ($result['success']) {
            $this->qrCodeString = $result['qr'];
            $this->md5 = $result['md5'];
        }
    }

    public function checkPaymentStatus()
    {
        // បើបង់លុយរួចហើយ មិនបាច់រត់ Polling ទៀតទេ
        if ($this->isPaid) {
            return;
        }
        $this->order->refresh();
        $this->order->load('payment');

        if ($this->order->payment && $this->order->payment->status === 'paid') {
            $this->isPaid = true;
            $this->dispatch('payment-success');
            return;
        }

        if ($this->remainingTime <= 0) {
            session()->flash('error', 'Payment timed out. Please try again.');
            return;
        }
        $this->remainingTime -= 3;

        $service = new KhqrService();
        $result = $service->checkPayment($this->md5);

        \Illuminate\Support\Facades\Log::info('Payment Result:', $result);

        if ($result['paid']) {
            $this->order->update([
                'status' => 'processing',
                'updated_at' => now(),
            ]);

            \App\Models\Payment::where('order_id', $this->order->id)->update([
                'status' => 'paid',
                'updated_at' => now(),
            ]);

            $this->isPaid = true;
            $this->dispatch('payment-success');
        }
    }

    public function cancelOrder()
    {
        $this->order->update([
            'status' => 'cancelled',
        ]);

        if ($this->order->payment) {
            $this->order->payment->update([
                'status' => 'cancelled', // ឬទុកជា pending ធម្មតាក៏បាន
            ]);
        }
        session()->flash('message', 'Order has been cancelled.');
        return $this->redirectRoute('home');
    }
}; ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100 bg-light py-5">

    <style>
        .payment-card {
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
            border: none;
            overflow: hidden;
            background: #ffffff;
            max-width: 450px;
            width: 100%;
        }

        .qr-wrapper {
            position: relative;
            padding: 16px;
            background: #f8fafc;
            border: 2px dashed #e2e8f0;
            border-radius: 20px;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .qr-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(to right, transparent, #10b981, transparent);
            animation: scan 2s linear infinite;
        }

        @keyframes scan {
            0% {
                top: 0;
            }

            50% {
                top: 100%;
            }

            100% {
                top: 0;
            }
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #e6f4ea;
            color: #137333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 2.5rem;
            animation: scaleUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
        }

        @keyframes scaleUp {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .btn-custom-cancel {
            border-radius: 12px;
            font-weight: 500;
            padding: 10px 24px;
            transition: all 0.2s;
            border: 1px solid #f1f5f9;
            background: #f8fafc;
            color: #64748b;
        }

        .btn-custom-cancel:hover {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fee2e2;
        }
    </style>

    <div class="payment-card p-4 p-md-5 text-center">

        @if (!$isPaid)
            {{-- ==================== ផ្ទាំងបង្ហាញ QR CODE សម្រាប់បង់ប្រាក់ (ACTIVE STATE) ==================== --}}
            <div x-data="{
                remaining: {{ $remainingTime }},
                init() {
                    let interval = setInterval(() => {
                        if (this.remaining > 0) {
                            this.remaining--;
                        } else {
                            clearInterval(interval);
                        }
                    }, 1000);
                }
            }">
                <div class="mb-4">
                    <h4 class="fw-bold text-dark mb-1">KHQR Payment</h4>
                    <p class="text-muted small">Scan the QR code below using any banking app</p>
                </div>

                <div class="p-3 mb-4 rounded-4 text-start" style="background: #f8fafc;">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Order Number</span>
                        <span class="fw-bold text-dark small">#{{ $order->order_number }}</span>
                    </div>
                    <div
                        class="d-flex justify-content-between align-items-center pt-2 border-top border-2 border-white">
                        <span class="text-muted small">Total Amount</span>
                        <span class="fw-bold text-primary fs-5">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>

                @if ($qrCodeString)
                    {{-- ប្រសិនបើនៅសល់ម៉ោង ឱ្យវារត់ Polling តែបើអស់ម៉ោង ឱ្យវាឈប់រត់តែម្តង --}}
                    <div @if ($remainingTime > 0) wire:poll.3s="checkPaymentStatus" @endif
                        wire:key="qr-section">
                        <div class="qr-wrapper mb-4">
                            {!! QrCode::size(220)->margin(1)->generate($qrCodeString) !!}
                        </div>

                        <div class="mb-2">
                            <p class="text-secondary small d-flex align-items-center justify-content-center gap-2"
                                x-show="remaining > 0">
                                <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                                Waiting for payment...
                            </p>

                            <p class="text-danger fw-bold small m-0" x-show="remaining > 0">
                                Expires in:
                                <span class="font-monospace" x-text="Math.floor(remaining / 60)"></span>:<span
                                    class="font-monospace" x-text="String(remaining % 60).padStart(2, '0')"></span>
                            </p>

                            <p class="text-danger fw-bold m-0" x-show="remaining <= 0">⏱️ Payment timed out!</p>
                        </div>

                        <button wire:click="cancelOrder"
                            class="btn btn-custom-cancel w-100 mt-3 d-flex align-items-center justify-content-center gap-2">
                            Cancel Order
                        </button>
                    </div>
                @else
                    <div class="alert alert-danger rounded-4 py-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Unable to generate QR Code.
                    </div>
                    <a href="{{ route('home') }}" class="btn btn-secondary w-100 rounded-3">Go Back</a>
                @endif
            </div>
        @else
            {{-- ==================== ផ្ទាំងសារជោគជ័យ ==================== --}}
            <div class="py-3">
                <div class="success-icon shadow-sm">
                    ✓
                </div>

                <h3 class="fw-bold text-dark mb-2">Payment Received!</h3>
                <p class="text-muted small px-3">Thank you! Your transaction was successfully processed and your order
                    is now confirmed.</p>

                <div class="border rounded-4 p-3 my-4 bg-light bg-opacity-50 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Order Invoice</span>
                        <span class="text-dark fw-medium small">#{{ $order->order_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Amount Paid</span>
                        <span class="text-success fw-bold small">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Status</span>
                        <span
                            class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 small">Success</span>
                    </div>
                </div>

                <a href="{{ route('receipt', ['order' => $order->id]) }}"
                    class="btn btn-outline-primary w-100 py-2.5 mb-2 shadow-sm"
                    style="border-radius: 12px; font-weight: 500;">
                    View Receipt
                </a>

                <a href="{{ route('home') }}" class="btn btn-primary w-100 py-2.5 shadow-sm"
                    style="border-radius: 12px; font-weight: 500;">
                    Continue to Homepage
                </a>
            </div>
        @endif

    </div>
</div>
