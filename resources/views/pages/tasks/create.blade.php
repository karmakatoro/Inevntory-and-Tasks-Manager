@extends('layouts.base')

@section('title', 'Add task to ' . $project->title . ' project - ' . env('APP_NAME'))

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
                        <li class="breadcrumb-item active">Add Task</li>
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
                            <h4 class="header-title mb-3">Add task to the project
                                "{{ Str::limit($project->title, 100, '...') }}"
                            </h4>
                        </div>
                        <div class="col-4 text-end">
                            <span class="badge bg-primary p-1">{{ $project->user->name }}</span>
                        </div>
                    </div>
                    <form action="{{ route('tasks.store') }}" enctype="multipart/form-data" method="post"
                        class="form-horizontal">
                        @if (session()->has('success'))
                            <div class="alert alert-success solid alert-dismissible fade show mt-3">
                                <svg viewBox="0 0 24 24" width="24 " height="24" stroke="currentColor"
                                    stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                    class="me-2">
                                    <polygon
                                        points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2">
                                    </polygon>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                <strong>Success!</strong> {{ session()->get('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                                </button>
                            </div>
                        @endif
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
                        @csrf
                        <input type="hidden" name="project_id" value="{{ request()->id }}">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12 mb-3">
                                            <label class="mb-1" for="title">Task Name</label>
                                            <div class="col-12">
                                                <input type="text" value="{{ old('name') }}" class="form-control"
                                                    id="name" name="name" required>
                                                @if ($errors->has('name'))
                                                    <p class="text-pink mt-2">
                                                        {{ $errors->first('name') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-12 mb-3">
                                            <label for="priority" class="form-label">Level</label>
                                            <select class="form-select" id="priority" name="priority">
                                                <option value="low" selected>Low</option>
                                                <option value="medium">Medium</option>
                                                <option value="high" selected>High</option>
                                                <option value="very high">Very High</option>
                                            </select>
                                            @if ($errors->has('priority'))
                                                <p class="text-pink mt-2">
                                                    {{ $errors->first('priority') }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="col-lg-3 col-sm-12 mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="statusProject" name="status">
                                                <option value="todo" selected>To do</option>
                                                <option value="pending">Pending</option>
                                            </select>
                                            @if ($errors->has('status'))
                                                <p class="text-pink mt-2">
                                                    {{ $errors->first('status') }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="col-lg-3 col-sm-12 mb-3">
                                            <label for="deadline" class="form-label">Task Deadline</label>
                                            <div class="col-md-12">
                                                <input type="date" id="deadline" value="{{ old('deadline') }}"
                                                    name="deadline" class="form-control" required>
                                                @if ($errors->has('deadline'))
                                                    <p class="text-pink mt-2">
                                                        {{ $errors->first('deadline') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-sm-12 mb-3">
                                            <p class="mb-1 fw-medium">Depend on an othe task</p>

                                            <select class="form-control" name="depenend" data-toggle="select2">
                                                <option>Select</option>
                                                {{-- <optgroup label="Alaskan/Hawaiian Time Zone"> --}}
                                                @foreach ($tasks as $task)
                                                    <option value="{{ $task->id }}">{{ $task->name }}</option>
                                                @endforeach
                                                {{-- </optgroup> --}}
                                            </select>
                                            @if ($errors->has('depenend'))
                                                <p class="text-pink mt-2">
                                                    {{ $errors->first('depenend') }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="col-lg-3 col-sm-12 mb-3">
                                            <p class="mb-1 fw-medium mt-3 mt-md-0">Assign to users</p>
                                            <select class="form-control select2-multiple" name="users_assigned[]"
                                                data-toggle="select2" multiple="multiple" data-placeholder="Choose ...">
                                                {{-- <optgroup label="Alaskan/Hawaiian Time Zone"> --}}
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                                {{-- </optgroup> --}}
                                            </select>
                                            @if ($errors->has('depenend'))
                                                <p class="text-pink mt-2">
                                                    {{ $errors->first('depenend') }}
                                                </p>
                                            @endif
                                        </div> <!-- end col -->
                                        <div class="col-lg-3 col-sm-12 mb-3">
                                            <label for="files" class="form-label">Task Files</label>
                                            <div class="col-md-12">
                                                <input type="file" id="files" name="files[]" class="form-control"
                                                    multiple>
                                                @if ($errors->has('files'))
                                                    <p class="text-pink mt-2">
                                                        {{ $errors->first('files') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-3">
                                            <label class="mb-1" for="description">
                                                Task Description</label>
                                            <div class="col-md-12">
                                                <textarea rows="5" id="description" name="description" class="form-control" required>
                                                        {{ old('description') }}
                                                    </textarea>
                                                @if ($errors->has('description'))
                                                    <p class="text-pink mt-2">
                                                        {{ $errors->first('description') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-12 text-end">
                                    <div class="col-lg-12 mt-3 text-end">
                                        <button type="submit" class="btn btn-primary">Create Task</button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end col -->

                    </form>
                </div> <!-- end card-body -->
            </div> <!-- end card-->
        </div>
    </div>
@endsection
