@extends('layouts.base')

@section('title', 'Edit ' . $project->title . ' - ' . env('APP_NAME'))

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
                        <h4 class="header-title mb-3">Create new project by filling this form</h4>
                    </div>
                    <div class="col-4 text-end">
                        <span class="badge bg-primary p-1">{{ auth()->user()->name }}</span>
                    </div>
                </div>
                <form action="{{ route('projects.update',['project'=> $project->id]) }}" enctype="multipart/form-data"
                    method="post" class="form-horizontal">
                    @if (session()->has('error'))
                    <div class="alert alert-danger solid alert-dismissible fade show mt-3">
                        <svg viewBox="0 0 24 24" width="24 " height="24" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                            <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2">
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
                                <label class="mb-1" for="title">Project Name</label>
                                <div class="col-12">
                                    <input type="text" value="{{ $project->title }}" class="form-control" id="title"
                                        name="title" required>
                                    @if ($errors->has('title'))
                                    <p class="text-pink mt-2">
                                        {{ $errors->first('title') }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="statusProject" name="status">
                                    <option value="unlaunched" @if($project->status=='unlaunched') selected
                                        @endif>Unlaunched</option>
                                    <option value="pending" @if($project->status=='pending') selected
                                        @endif>Pending</option>
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
                                <div class="row">
                                    <div class="col-lg-12 col-sm-12 mb-3">
                                        <label class="mb-1" for="description">
                                            Description</label>
                                        <div class="col-md-12">
                                            <textarea rows="5" id="description" name="description" class="form-control"
                                                required>
                                                        {{ $project->description }}
                                                    </textarea>
                                            @if ($errors->has('description'))
                                            <p class="text-pink mt-2">
                                                {{ $errors->first('description') }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <label for="start">Date Start</label>
                                        <div class="col-md-12">
                                            <input type="date" value="{{$project->start}}" id="start" name="start"
                                                class="form-control" required>
                                            @if ($errors->has('start'))
                                            <p class="text-pink mt-2">
                                                {{ $errors->first('start') }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-sm-12">
                                        <label for="end">Date End</label>
                                        <div class="col-md-12">
                                            <input type="date" value="{{$project->end}}" id="end" name="end"
                                                class="form-control" required>
                                            @if ($errors->has('end'))
                                            <p class="text-pink mt-2">
                                                {{ $errors->first('end') }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-12 col-lg-4">
                                <label for="logo">Logo</label>
                                <div class="col-md-12">
                                    <input type="file" id="logo" name="logo" class="form-control mb-2">
                                    <img src="{{asset($project->logo)}}"
                                        style="width: 100%; height:160px;object-fit:cover" alt="">
                                </div>
                                <div class="col-12">

                                </div>
                                @if ($errors->has('logo'))
                                <p class="text-pink mt-2">
                                    {{ $errors->first('logo') }}
                                </p>
                                @endif
                            </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-12 mt-3 text-end">
                                <button type="submit" class="btn btn-primary">Modifier Project</button>
                            </div>
                        </div>
                    </div> <!-- end col -->

                </form>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div>
</div>
@endsection