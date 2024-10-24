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

            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class='mdi mdi-dots-horizontal font-18'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class='mdi mdi-attachment me-1'></i>Attachment
                            </a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class='mdi mdi-pencil-outline me-1'></i>Edit
                            </a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class='mdi mdi-content-copy me-1'></i>Mark as Duplicate
                            </a>
                            <div class="dropdown-divider"></div>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item text-danger">
                                <i class='mdi mdi-delete-outline me-1'></i>Delete
                            </a>
                        </div> <!-- end dropdown menu-->
                    </div> <!-- end dropdown-->
                    <h5 class="header-title mb-3">Attachments</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div>
                                <form action="https://coderthemes.com/" method="post" class="dropzone"
                                    id="myAwesomeDropzone" data-plugin="dropzone" data-previews-container="#file-previews"
                                    data-upload-preview-template="#uploadPreviewTemplate">
                                    <div class="fallback">
                                        <input name="file" type="file" />
                                    </div>

                                    <div class="dz-message needsclick">
                                        <i class="h2 text-muted ri-upload-2-line d-inline-block"></i>
                                        <h4>Drop files here or click to upload.</h4>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mt-4 mt-md-0">
                                <div class="card border mb-2">
                                    <div class="p-2">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="avatar-sm">
                                                    <span class="avatar-title badge-soft-primary text-primary rounded">
                                                        ZIP
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col ps-0">
                                                <a href="javascript:void(0);"
                                                    class="text-muted fw-semibold">Minton-sketch-design.zip</a>
                                                <p class="mb-0 font-12">2.3 MB</p>
                                            </div>
                                            <div class="col-auto">
                                                <!-- Button -->
                                                <a href="javascript:void(0);" class="btn btn-link font-16 text-muted">
                                                    <i class="ri-download-2-line"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                                                    <i class="ri-download-2-line"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview -->
                                <div class="dropzone-previews mt-2" id="file-previews"></div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">

                    <div class="float-end">
                        <select class="form-select form-select-sm">
                            <option selected="">Recent</option>
                            <option value="1">Most Helpful</option>
                            <option value="2">High to Low</option>
                            <option value="3">Low to High</option>
                        </select>
                    </div> <!-- end dropdown-->

                    <h4 class="mb-4 mt-0 font-16">Comments (51)</h4>

                    <div class="clerfix"></div>

                    <div class="d-flex">
                        <img class="me-2 rounded-circle" src="assets/images/users/avatar-3.jpg"
                            alt="Generic placeholder image" height="32">
                        <div class="flex-1">
                            <h5 class="mt-0">Barry Gould <small class="text-muted fw-normal float-end">5 hours
                                    ago</small></h5>
                            Nice work, makes me think of The Money Pit.

                            <br />
                            <a href="javascript: void(0);" class="text-muted font-13 d-inline-block mt-2"><i
                                    class="mdi mdi-reply"></i> Reply</a>

                            <div class="d-flex align-items-start mt-3">
                                <a class="pe-2" href="#">
                                    <img src="assets/images/users/avatar-4.jpg" class="rounded-circle"
                                        alt="Generic placeholder image" height="32">
                                </a>
                                <div class="flex-1">
                                    <h5 class="mt-0">Louis Hill <small class="text-muted fw-normal float-end">3 hours
                                            ago</small></h5>
                                    i'm in the middle of a timelapse animation myself! (Very different though.) Awesome
                                    stuff.

                                    <br />
                                    <a href="javascript: void(0);" class="text-muted font-13 d-inline-block mt-2">
                                        <i class="mdi mdi-reply"></i> Reply
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mt-3">
                        <img class="me-2 rounded-circle" src="assets/images/users/avatar-5.jpg"
                            alt="Generic placeholder image" height="32">
                        <div class="flex-1">
                            <h5 class="mt-0">Aaron Wilson <small class="text-muted fw-normal float-end">1 day
                                    ago</small></h5>
                            It would be very nice to have.

                            <br />
                            <a href="javascript: void(0);" class="text-muted font-13 d-inline-block mt-2"><i
                                    class="mdi mdi-reply"></i> Reply</a>
                        </div>
                    </div>

                    <div class="text-center mt-2">
                        <a href="javascript:void(0);" class="text-danger"><i class="mdi mdi-spin mdi-loading me-1"></i>
                            Load more </a>
                    </div>

                    <div class="border rounded mt-4">
                        <form action="#" class="comment-area-box">
                            <textarea rows="3" class="form-control border-0 resize-none" placeholder="Your comment..."></textarea>
                            <div class="p-2 bg-light d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="#" class="btn btn-sm px-1 btn-light"><i
                                            class='mdi mdi-upload'></i></a>
                                    <a href="#" class="btn btn-sm px-1 btn-light"><i class='mdi mdi-at'></i></a>
                                </div>
                                <button type="submit" class="btn btn-sm btn-success"><i
                                        class="fe-send me-1"></i>Submit</button>
                            </div>
                        </form>
                    </div> <!-- end .border-->

                </div> <!-- end card-body-->
            </div>
            <!-- end card-->
        </div>
    </div>
    <!-- end row -->
@endsection
