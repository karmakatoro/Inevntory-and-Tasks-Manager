@extends('layouts.base')

@section('title', 'Product : ' . $product->name . ' - ' . env('APP_NAME'))

@section('content')
    <!-- start page title -->
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
                        <li class="breadcrumb-item active">{{ Str::limit($product->name, 50, '...') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="row justify-content-center">
                                <div class="col-xl-8">

                                    <div id="product-carousel" class="carousel slide product-detail-carousel"
                                        data-bs-ride="carousel">

                                        <div class="carousel-inner">
                                            @foreach (json_decode($product->gallery) as $img)
                                                <div class="carousel-item @if ($loop->iteration == 1) active @endif">
                                                    <div>
                                                        <img src="{{ asset($img->path) }}" alt="product-img"
                                                            class="img-fluid">
                                                    </div>
                                                </div>
                                                @if ($loop->iteration == 3)
                                                @break
                                            @endif
                                        @endforeach

                                    </div>
                                    <ol class="carousel-indicators product-carousel-indicators mt-2">
                                        @foreach (json_decode($product->gallery) as $index => $row)
                                            <li data-bs-target="#product-carousel"
                                                data-bs-slide-to="{{ $index }}"
                                                @if ($index == 0) class="active" @endif>
                                                <img src="{{ asset($row->path) }}" alt="product-img"
                                                    class="img-fluid product-nav-img">
                                            </li>
                                            @if ($loop->iteration == 3)
                                            @break
                                        @endif
                                    @endforeach
                                </ol>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-7">
                    <div>
                        <div><a href="#" class="text-primary">{{ $product->product_category->name }}</a></div>
                        <h4 class="mb-1">{{ $product->name }}
                            <a href="javascript: void(0);" class="text-muted"><i
                                    class="mdi mdi-square-edit-outline ms-2"></i></a>
                        </h4>
                        <div class="mt-3">
                            <h4>Price : <span class="text-muted me-2">$ {{ $product->price }}</span>
                            </h4>
                        </div>
                        <hr />

                        <div>
                            <p>
                                {{ $product->description }}
                            </p>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
    </div>
</div>
</div>
<!-- end row -->


@endsection
