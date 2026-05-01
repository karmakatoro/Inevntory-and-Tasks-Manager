@extends('layouts.base')

@section('title', 'Edit Product - ' . env('APP_NAME'))

@section('content')
    <!-- price page title -->
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
    </div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Products</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.sales') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('products.index') }}">Products</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="header-title mb-3">Update the product by filling this form</h4>
                        </div>
                        <div class="col-4 text-end">
                            <span class="badge bg-primary p-1">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                    <form action="{{ route('products.update', ['product' => $product->id]) }}" enctype="multipart/form-data"
                        method="post" class="form-horizontal">
                        @if (session()->has('error'))
                            <div class="alert alert-danger solid alert-dismissible fade show mt-3">
                                <svg viewBox="0 0 24 24" width="24 " height="24" stroke="currentColor"
                                    stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                    class="me-2">
                                    <polygon
                                        points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2">
                                    </polygon>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                <strong>Error!</strong> {{ session()->get('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                                </button>
                            </div>
                        @endif
                        @method('put')
                        @csrf
                        <div class="col-12">
                            <div class="row mb-3">
                                <div class="col-lg-8 col-sm-12">
                                    <label class="mb-1" for="name">Product Name</label>
                                    <div class="col-12">
                                        <input type="text" value="{{ $product->name }}" class="form-control"
                                            id="name" name="name" required>
                                        @if ($errors->has('name'))
                                            <p class="text-pink mt-2">
                                                {{ $errors->first('title') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <label for="statusProduct" class="form-label">Status</label>
                                    <select class="form-select" id="statusProduct" name="status">
                                        <option value="on" @if ($product->status == 'on') selected @endif>Activated
                                        </option>
                                        <option value="off" @if ($product->status == 'off') selected @endif>Deactivated
                                        </option>
                                    </select>
                                    @if ($errors->has('status'))
                                        <p class="text-pink mt-2">
                                            {{ $errors->first('status') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-8 col-sm-12">
                                    <label class="mb-1" for="description">
                                        Description</label>
                                    <div class="col-md-12">
                                        <textarea rows="5" id="description" name="description" class="form-control" required>
                                                {{ $product->description }}
                                            </textarea>
                                        @if ($errors->has('description'))
                                            <p class="text-pink mt-2">
                                                {{ $errors->first('description') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-4">
                                    <label for="photo">Photo</label>
                                    <div class="col-md-12">
                                        <input type="file" id="photo" name="photo" class="form-control"
                                            accept="image/jpeg,image/png,image/jpg">
                                    </div>
                                    <div class="col-12">
                                    </div>
                                    @if ($errors->has('photo'))
                                        <p class="text-pink mt-2">
                                            {{ $errors->first('photo') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-4 col-sm-12">
                                    <label for="price">Price</label>
                                    <div class="col-md-12">
                                        <input type="integer" id="price" value="{{ $product->price }}" name="price"
                                            class="form-control" required>
                                        @if ($errors->has('price'))
                                            <p class="text-pink mt-2">
                                                {{ $errors->first('price') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <label for="product_category_id">Product Category</label>
                                    <select class="form-control" id="product_category_id" name="product_category_id"
                                        data-toggle="select2" required>
                                        <option>Select</option>
                                        @foreach ($categories as $product_category)
                                            <option value="{{ $product_category->id }}"
                                                @if ($product->product_category_id == $product_category->id) selected @endif>
                                                {{ $product_category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('product_category_id'))
                                        <p class="text-pink mt-2">
                                            {{ $errors->first('product_category_id') }}
                                        </p>
                                    @endif
                                </div>

                                <div class="col-lg-4 col-sm-12">
                                    <label for="subcategories">Subcategories</label>
                                    <div class="col-md-12">
                                        @php
                                            // On prépare la liste pour éviter l'erreur "Undefined variable"
// On gère le cas où c'est déjà un array (si tu as mis le cast)
                                            // ou une string JSON (si tu ne l'as pas mis)
                                            $selected_subs = is_array($product->subcategories)
                                                ? $product->subcategories
                                                : json_decode($product->subcategories, true) ?? [];
                                        @endphp

                                        <select class="form-control select2-multiple" name="subcategories[]"
                                            id="subcategories" data-toggle="select2" multiple="multiple"
                                            data-placeholder="Choose ...">
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat->id }}" @selected(in_array($cat->id, $selected_subs))>
                                                    {{ $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('subcategories'))
                                            <p class="text-pink mt-2">
                                                {{ $errors->first('subcategories') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-3 text-end">
                                    <button type="submit" class="btn btn-primary">Update Product</button>
                                </div>
                            </div>
                        </div> <!-- end col -->

                    </form>
                </div> <!-- end card-body -->
            </div> <!-- end card-->
        </div>
    </div>
@endsection
