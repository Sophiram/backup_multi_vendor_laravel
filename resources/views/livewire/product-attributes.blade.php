<?php

use Livewire\Volt\Component;
use App\Models\Attribute;
use App\Models\AttributeValue;

new class extends Component {
    public $availableAttributes = [];
    public $rows = [];

    public function mount()
    {
        $this->availableAttributes = Attribute::all();
        $this->addRow();
    }

    public function addRow()
    {
        $this->rows[] = [
            'attribute_id' => '',
            'attribute_value_id' => '',
            'additional_price' => '',
            'values' => []
        ];
    }

    public function removeRow($index)
    {
        if (count($this->rows) > 1) {
            unset($this->rows[$index]);
            $this->rows = array_values($this->rows);
        }
    }

    public function updated($key, $value)
    {
        // ពិនិត្យមើលការផ្លាស់ប្តូររបស់ attribute_id (ឧ. "rows.0.attribute_id")
        if (str_contains($key, '.attribute_id')) {
            $index = explode('.', $key)[1];

            if ($value) {
                $this->rows[$index]['values'] = AttributeValue::where('attribute_id', $value)->get()->toArray();
            } else {
                $this->rows[$index]['values'] = [];
                $this->rows[$index]['attribute_value_id'] = '';
            }
        }
    }
}; ?>

<div class="card border shadow-sm my-3 rounded-3">
    <!-- Header -->
    <div class="card-header bg-white border-bottom py-2 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark small"><i class="bi bi-sliders2-vertical me-2"></i>Product Attributes</h6>
        <button type="button" wire:click="addRow" class="btn btn-sm btn-primary fw-bold" style="font-size: 11px;">
            <i class="bi bi-plus-circle me-1"></i>Add
        </button>
    </div>

    <!-- Body -->
    <div class="card-body p-3 bg-light">
        @foreach($rows as $index => $row)
            <div class="row g-2 align-items-end mb-3 pb-2 border-bottom" wire:key="attr-row-{{ $index }}">

                <!-- Attribute Type -->
                <div class="col-md-4">
                    <label class="form-label mb-1 fw-bold text-secondary small">Attribute</label>
                    <select wire:model.live="rows.{{ $index }}.attribute_id" class="form-select form-select-sm shadow-none">
                        <option value="">-- Select Type --</option>
                        @foreach($availableAttributes as $attr)
                            <option value="{{ $attr->id }}">{{ $attr->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Value -->
                <div class="col-md-3">
                    <label class="form-label mb-1 fw-bold text-secondary small">Value</label>
                    <select wire:model="rows.{{ $index }}.attribute_value_id" class="form-select form-select-sm shadow-none" {{ empty($row['values']) ? 'disabled' : '' }}>
                        <option value="">-- Select Value --</option>
                        @foreach($row['values'] as $val)
                            <option value="{{ $val['id'] }}">{{ $val['value'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Price -->
                <div class="col-md-3">
                    <label class="form-label mb-1 fw-bold text-secondary small">Price</label>
                    <input type="number" step="0.01" wire:model="rows.{{ $index }}.additional_price" class="form-control form-control-sm shadow-none" placeholder="0.00">
                </div>

                <!-- Remove -->
                <div class="col-md-2">
                    <button type="button" wire:click="removeRow({{ $index }})" class="btn btn-sm btn-outline-danger w-100">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
