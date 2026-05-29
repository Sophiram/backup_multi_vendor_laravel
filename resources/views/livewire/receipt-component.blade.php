<?php

use Livewire\Volt\Component;
use App\Models\Order;

new class extends Component {
    public Order $order; // ប្រកាស Type ជា Model Order

    public function mount(Order $order)
    {
        // ឥឡូវនេះ $order គឺជា Object ដែល Laravel រកឃើញពី Database រួចហើយ
        // អ្នកអាច Load relation បន្ថែមបាន៖
        $this->order = $order->load(['items.product', 'payment']);
    }
}; ?>

<div>
    <!-- ហៅ Blade Template មកបង្ហាញ -->
    @include('livewire.receipt-view')
</div>
