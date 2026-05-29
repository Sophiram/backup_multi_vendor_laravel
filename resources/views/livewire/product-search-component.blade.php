<?php

use Livewire\Component;
use App\Models\Product; // 💡 ត្រូវប្រាកដថាបាន Import Product Model ចូល

new class extends Component {
    // បង្កើត Property សម្រាប់ចាប់យកពាក្យគន្លឹះដែលអ្នកប្រើប្រាស់វាយ
    public $search = '';

    public function render()
    {
        $searchResults = [];

        // ប្រសិនបើមានការវាយអក្សរចាប់ពី ២ ខ្ទង់ឡើងទៅ ទើបចាប់ផ្តើម Search ក្នុង Database
        if (strlen($this->search) >= 2) {
            $searchResults = Product::where('product_name', 'like', '%' . $this->search . '%')
                ->take(5) // កម្រិតយកតែ ៥ ផលិតផលដំបូងដើម្បីរក្សា ល្បឿនលឿន (Performance)
                ->get();
        }

        return view('livewire.product-search-component', [
            'products' => $searchResults
        ]);
    }
};
?>

<div class="w-100 position-relative">
    <form action="" class="w-100 m-0 p-0" onsubmit="event.preventDefault();">
        <div class="search-input-container">
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Search for products, brands or stores...">

            <button class="search-submit-btn" type="button">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </form>

    {{-- 📦 ផ្ទាំងបង្ហាញលទ្ធផលស្វែងរក (Dropdown Search Results) --}}
    @if(strlen($search) >= 2)
        <div class="position-absolute w-100 bg-white shadow-lg border rounded-4 mt-2 p-2" style="z-index: 2000; max-height: 380px; overflow-y: auto;">

            @if(count($products) > 0)
                <div class="px-3 py-1 text-muted small fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    Products Found ({{ count($products) }})
                </div>
                <hr class="my-1 text-black-50">

                @foreach($products as $product)
                    {{-- 🔗 លីងទៅកាន់ទំព័រលម្អិតរបស់ផលិតផល (សូមដូរ route ទៅតាមគម្រោងរបស់អ្នក) --}}
                    <a href="/products/{{ $product->id }}" class="d-flex align-items-center gap-3 p-2 rounded-3 text-decoration-none hover-search-item" style="transition: background 0.2s;">
                        {{-- រូបភាពផលិតផល --}}
                        <img src="{{ asset('storage/' . $product->product_image) }}"
                             alt="{{ $product->product_name }}"
                             class="rounded-2 object-fit-cover"
                             style="width: 45px; height: 45px;"
                             onerror="this.src='https://placehold.co/45'">

                        {{-- ឈ្មោះ និង តម្លៃផលិតផល --}}
                        <div class="d-flex flex-column">
                            <span class="fw-semibold text-dark small">{{ $product->product_name }}</span>
                            <span class="text-success fw-bold small">${{ number_format($product->product_price, 2) }}</span>
                        </div>
                    </a>
                @endforeach
            @else
                {{-- ករណីរកមិនឃើញផលិតផល --}}
                <div class="text-center py-4 text-muted small">
                    <i class="fa-regular fa-face-frown d-block mb-2 fs-4"></i>
                    No products found for "<span class="fw-bold">{{ $search }}</span>"
                </div>
            @endif

        </div>
    @endif
</div>
