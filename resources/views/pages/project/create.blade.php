@extends('layouts.base')

@section('title', 'Create Project - ' . env('APP_NAME'))

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
                        <li class="breadcrumb-item active">Create</li>
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

                    <h4 class="header-title mb-3">Create new project by filling this form</h4>

                    <div id="rootwizard">
                        <ul class="nav nav-pills nav-justified form-wizard-header mb-3">
                            <li class="nav-item" data-target-form="#accountForm">
                                <a href="#first" data-bs-toggle="tab" data-toggle="tab" class="nav-link active">
                                    <span class="number">1</span>
                                    <span class="d-none d-sm-inline">About</span>
                                </a>
                            </li>
                            <li class="nav-item" data-target-form="#profileForm">
                                <a href="#second" data-bs-toggle="tab" data-toggle="tab" class="nav-link">
                                    <span class="number">2</span>
                                    <span class="d-none d-sm-inline">Options</span>
                                </a>
                            </li>
                            <li class="nav-item" data-target-form="#otherForm">
                                <a href="#third" data-bs-toggle="tab" data-toggle="tab" class="nav-link">
                                    <span class="number">3</span>
                                    <span class="d-none d-sm-inline">Finish</span>
                                </a>
                            </li>
                        </ul>
                        <form id="accountForm" action="{{ route('projects.store') }}" method="post"
                            class="form-horizontal">
                            @csrf
                            <div class="tab-content mb-0 b-0">

                                <div class="tab-pane active" id="first">

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row mb-3">
                                                <div class="col-lg-8 col-sm-12">
                                                    <label class="mb-1" for="title">Project Name</label>
                                                    <div class="col-12">
                                                        <input type="text" value="{{ old('title') }}"
                                                            class="form-control" id="title" name="title" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-12">
                                                    <label for="status" class="form-label">Status</label>
                                                    <select class="form-select" id="statusProject" name="status">
                                                        <option value="unlaunched" selected>Unlaunched</option>
                                                        <option value="pending">Pending</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-8 col-sm-12">
                                                    <label class="mb-1" for="description">
                                                        Description</label>
                                                    <div class="col-md-12">
                                                        <textarea rows="5" id="description" name="description" class="form-control" required>
                                                            {{ old('description') }}
                                                        </textarea>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-lg-4">
                                                    <label for="logo">Logo</label>
                                                    <div class="col-md-12">
                                                        <input type="file" id="logo" name="logo"
                                                            class="form-control" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-lg-4 col-sm-12">
                                                    <label for="confirm3">Date Start</label>
                                                    <div class="col-md-12">
                                                        <input type="date" id="confirm3" name="confirm3"
                                                            class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-sm-12">
                                                    <label for="confirm3">Date End</label>
                                                    <div class="col-md-12">
                                                        <input type="date" id="confirm3" name="confirm3"
                                                            class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-12">
                                                    <label for="confirm3">Creator</label>
                                                    <div class="col-md-12">
                                                        <input type="text" value="{{ auth()->user()->name }}"
                                                            class="form-control" disabled>
                                                    </div>
                                                </div>


                                            </div>
                                        </div> <!-- end col -->
                                    </div> <!-- end row -->
                                </div>
                                <!-- end tab pane -->

                                <div class="tab-pane fade" id="second">
                                    <form id="profileForm" method="post" action="#" class="form-horizontal">
                                        <div class="row">
                                            <div class="col-12">
                                                <h4 class="header-title mb-3">Project Details</h4>

                                                <div id="snow-editor" style="height: 300px;">
                                                    <h3><span class="ql-size-large">Hello World!</span></h3>
                                                    <p><br></p>
                                                    <h3>This is an simple editable area.</h3>
                                                    <p><br></p>
                                                    <ul>
                                                        <li>
                                                            Select a text to reveal the toolbar.
                                                        </li>
                                                        <li>
                                                            Edit rich document on-the-fly, so elastic!
                                                        </li>
                                                    </ul>
                                                    <p><br></p>
                                                    <p>
                                                        End of simple area
                                                    </p>
                                                </div> <!-- end Snow-editor-->
                                            </div>
                                            <!-- end col -->
                                        </div>
                                        <!-- end row -->

                                </div>
                                <!-- end tab pane -->

                                <div class="tab-pane fade" id="third">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4 class="header-title">Dropzone File Upload</h4>
                                                    <p class="sub-header">
                                                        DropzoneJS is an open source library that provides drag’n’drop file
                                                        uploads with image previews.
                                                    </p>

                                                    <form action="https://coderthemes.com/" method="post"
                                                        class="dropzone" id="myAwesomeDropzone" data-plugin="dropzone"
                                                        data-previews-container="#file-previews"
                                                        data-upload-preview-template="#uploadPreviewTemplate">
                                                        <div class="fallback">
                                                            <input name="file" type="file" multiple />
                                                        </div>

                                                        <div class="dz-message needsclick">
                                                            <i class="h1 text-muted ri-upload-cloud-2-line"></i>
                                                            <h3>Drop files here or click to upload.</h3>
                                                            <span class="text-muted font-13">(This is just a demo dropzone.
                                                                Selected files are
                                                                <strong>not</strong> actually uploaded.)</span>
                                                        </div>
                                                    </form>

                                                    <!-- Preview -->
                                                    <div class="dropzone-previews mt-3" id="file-previews"></div>

                                                </div> <!-- end card-body-->
                                            </div> <!-- end card-->
                                        </div><!-- end col -->

                                    </div>
                                    <!-- end row -->


                                    <ul class="pager wizard mb-0 list-inline mt-2">
                                        <li class="next list-inline-item float-end">
                                            <button type="button" class="btn btn-primary">Submit</button>
                                        </li>
                                    </ul>
                                </div>
                                <!-- end tab pane -->
                        </form>


                    </div> <!-- tab-content -->
                </div> <!-- end #rootwizard-->

            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div>
    </div>
@endsection
