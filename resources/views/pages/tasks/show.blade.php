@extends('layouts.base')

@section('title', 'Details for ' . $task->name . ' task - ' . env('APP_NAME'))

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
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('projects.show', ['project' => $task->project_id]) }}">{{ $task->project_id }}</a>
                        </li>
                        <li class="breadcrumb-item active">Task Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class='mdi mdi-dots-horizontal font-18'></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="#" class="dropdown-item action-attach">
                                <i class='mdi mdi-attachment me-1'></i>Attachment
                            </a>

                            <!-- item-->
                            <a href="{{ route('tasks.edit', ['task' => $task->id]) }}" class="dropdown-item">
                                <i class='mdi mdi-pencil-outline me-1'></i>Edit
                            </a>
                            <!-- item-->
                            <a href="#" class="dropdown-item action-completed">
                                <i class='ri-task-line me-1'></i>Mark as Completed
                            </a>
                            <div class="dropdown-divider"></div>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item text-danger">
                                <i class='mdi mdi-delete-outline me-1'></i>Delete
                            </a>
                        </div>
                    </div>
                    <p class="text-primary">{{ $task->project->title }}</p>
                    <h4 class="mb-1">{{ $task->name }}</h4>

                    <div class="text-muted">
                        <div class="row">
                            <div class="col-lg-4 col-sm-6">
                                <div class="d-flex align-items-start mt-3">
                                    <div class="me-2 align-self-center">
                                        <i class="ri-hashtag h2 m-0 text-muted"></i>
                                    </div>
                                    <div class="flex-1 overflow-hidden">
                                        <p class="mb-1">Task ID</p>
                                        <h5 class="mt-0 text-truncate">
                                            #TSK-{{ '0' . $task->id . '0' . $task->project_id . '0' . $task->project->user_id }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="d-flex align-items-start mt-3">
                                    <div class="me-2 align-self-center">
                                        <img src="{{ asset('storage/users/' . $task->user->photo) }}" alt=""
                                            class="avatar-sm rounded-circle">
                                    </div>
                                    <div class="flex-1 overflow-hidden">
                                        <p class="mb-1">Created by</p>
                                        <h5 class="mt-0 text-truncate">
                                            {{ $task->user->name }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="d-flex align-items-start mt-3">
                                    <div class="me-2 align-self-center">
                                        <i class="ri-calendar-event-line h2 m-0 text-muted"></i>
                                    </div>
                                    <div class="flex-1 overflow-hidden">
                                        <p class="mb-1">Due Date</p>
                                        <h5 class="mt-0 text-truncate">
                                            @if (\Carbon\Carbon::parse($task->deadline)->lt(Carbon\Carbon::today()))
                                                <span
                                                    class="badge badge-soft-danger">{{ \Carbon\Carbon::parse($task->deadline)->locale('en_EN')->isoFormat('DD MMMM YYYY') }}</span>
                                            @else
                                                <span
                                                    class="badge badge-soft-success">{{ \Carbon\Carbon::parse($task->deadline)->locale('en_EN')->isoFormat('DD MMMM YYYY') }}</span>
                                            @endif
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div>
                            <h5>Description:</h5>
                            <p class="text-muted">
                                {{ $task->description }}
                            </p>
                            @if (count($assigns) > 0)
                                <div class="mt-3">
                                    <h5>Assign to :</h5>
                                    <div>
                                        @foreach ($assigns as $project_task)
                                            <a href="#"
                                                class="badge badge-soft-primary p-1 m-1">{{ $project_task->user->name }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="header-title mb-3">Attachments</h5>
                    <div class="col-12">
                        <div class="mt-4 mt-md-0">
                            <div class="card border mb-0">
                                <div class="p-2">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="avatar-sm">
                                                <span class="avatar-title bg-secondary rounded text-light">
                                                    .MP4
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col ps-0">
                                            <a href="javascript:void(0);"
                                                class="text-muted fw-semibold">Admin-bug-report.mp4</a>
                                            <p class="mb-0 font-12">7.05 MB</p>
                                        </div>
                                        <div class="col-auto">
                                            <!-- Button -->
                                            <a href="javascript:void(0);" class="btn btn-link font-16 text-muted">
                                                <i class="mdi mdi-delete-outline text-danger"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            @include('pages.tasks.tasks-files')
        </div>
    </div>
    <!-- end row -->
@endsection
