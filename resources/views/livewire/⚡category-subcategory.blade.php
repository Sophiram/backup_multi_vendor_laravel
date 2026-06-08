<?php

// namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\SubCategory;
use Livewire\Component;

new class extends Component {
    public $categories = [];
    public $selectedCategory;
    public $subcategories = [];

    public function mount()
    {
        $this->categories = Category::all();
    }

    public function updatedSelectedCategory($categoryId)
    {
        $this->subcategories = SubCategory::where('category_id', $categoryId)->get();
    }
};
?>

<div>
    <label  for="category_id" class="fw-bold mb-1 mt-1">Category</label>
    <select class="form-control" name="category_id" wire:model.live="selectedCategory">
        <option value="">Select A Category</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
        @endforeach
    </select>

    {{-- <label class="form-label mt-3">Sub Category</label> --}}
    <label  for="subcategory_id" class="fw-bold mb-1 mt-3">Sub Category</label>
    <select class="form-control" name="subcategory_id">
        <option value="">Select A Sub Category</option>
        @foreach ($subcategories as $subcategory)
            <option value="{{ $subcategory->id }}">{{ $subcategory->subcategory_name }}</option>
        @endforeach
    </select>
</div>
