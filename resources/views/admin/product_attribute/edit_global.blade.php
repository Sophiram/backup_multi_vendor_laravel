@extends('admin.layouts.layout')

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <h3 class="fw-bold">Edit Global Attribute</h3>
        <div class="card p-4">
            <form action="{{ route('admin.attribute.update', $attribute->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label>Attribute Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $attribute->name }}">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@endsection
