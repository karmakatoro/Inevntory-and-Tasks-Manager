@extends('layouts.base')

@section('title', 'Project : ' . $project->title . ' - ' . env('APP_NAME'))

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Projects</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.sales') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('projects.index') }}">Projects</a>
                        </li>
                        <li class="breadcrumb-item active">{{ Str::limit($project->title, 50, '...') }}</li>
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

                                    <div id="product-carousel" class="carousel slide product-detail-carousel">
                                        <img src="{{ asset($project->logo) }}" alt=""
                                            style="height: 100%;width:100%;object-fit:cover;">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-lg-7">
                            <div>
                                <div><a href="#" class="text-primary">Project</a></div>
                                <h4 class="mb-1">
                                    {{ $project->title }}
                                </h4>
                                <div class="mt-3">
                                    <h4>Owner : {{ $project->user->name }}
                                    </h4>
                                </div>
                                <div class="mt-3">
                                    <h4>Status : {{ Str::ucfirst($project->status) }}
                                    </h4>
                                </div>
                                <div class="mt-3">
                                    <h4>Date Start :
                                        {{ \Carbon\Carbon::parse($project->start)->locale('en_EN')->isoFormat('DD
                                                                                                                                                            MMMM YYYY') }}
                                    </h4>
                                </div>
                                <div class="mt-3">
                                    <h4>Date End :
                                        {{ \Carbon\Carbon::parse($project->end)->locale('en_EN')->isoFormat('DD
                                                                                                                                                            MMMM YYYY') }}
                                </div>
                                <hr />

                                <div>
                                    <p>
                                        {{ $project->description }}
                                    </p>


                                    {{-- <div>
                                    <div>
                                        <a type="button" class="btn btn-success waves-effect waves-light">
                                            <span class="btn-label"><i
                                                    class="mdi mdi-clipboard-list-outline"></i></span>Add tasks
                                        </a>
                                        <a type="button" class="btn btn-primary waves-effect waves-light">
                                            <span class="btn-label"><i class="mdi mdi-file"></i></span>Project Files
                                        </a>
                                    </div>
                                </div> --}}
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
            </div>
            @include('pages.project.project-tasks')
        </div>
    </div>
    <!-- end row -->

@endsection
