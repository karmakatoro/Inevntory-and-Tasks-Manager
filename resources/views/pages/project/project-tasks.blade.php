<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <!-- cta -->
                        <div class="row">
                            <div class="col-sm-3">
                                <a href="{{ route('tasks.create') }}?id={{ $project->id }}&name={{ Str::slug($project->title) }}"
                                    class="btn btn-primary waves-effect waves-light"><i class='fe-plus me-1'></i>Add New
                                    Task</a>
                            </div>
                            <div class="col-sm-9">
                                <div class="float-sm-end mt-3 mt-sm-0">
                                    <div class="d-flex align-items-start flex-wrap">
                                        <div class="mb-3 mb-sm-0 me-sm-2">
                                            <form>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" placeholder="Search...">
                                                </div>
                                            </form>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-light dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-filter-variant"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#">Due Date</a>
                                                <a class="dropdown-item" href="#">Added Date</a>
                                                <a class="dropdown-item" href="#">Assignee</a>
                                                <a class="dropdown-item" href="#">To day</a>
                                                <a class="dropdown-item" href="#">Tomorrow</a>
                                                <a class="dropdown-item" href="#">This week</a>
                                                <a class="dropdown-item" href="#">This Month</a>
                                                <a class="dropdown-item" href="#">Yesterday</a>
                                                <a class="dropdown-item" href="#">Last Week</a>
                                                <a class="dropdown-item" href="#">Last Month</a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="custom-accordion">
                            <div class="mt-4">
                                <h5 class="position-relative mb-0"><a href="#taskcollapse1" class="text-dark d-block"
                                        data-bs-toggle="collapse">Pending Tasks <span class="text-muted">(08)</span> <i
                                            class="mdi mdi-chevron-down accordion-arrow"></i></a>
                                </h5>
                                <div class="collapse show" id="taskcollapse1">
                                    <div class="table-responsive mt-3">
                                        <table class="table table-centered table-nowrap table-borderless table-sm">
                                            <thead class="table-light">
                                                <tr class="">
                                                    <th scope="col">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="tasktodayCheck">
                                                            <label class="form-check-label" for="tasktodayCheck">Task
                                                                ID</label>
                                                        </div>
                                                    </th>
                                                    <th scope="col">Tasks</th>
                                                    <th scope="col">Assign to</th>
                                                    <th scope="col">Due Date</th>
                                                    <th scope="col">Task priority</th>
                                                    <th scope="col" style="width: 85px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="tasktodayCheck04">
                                                            <label class="form-check-label"
                                                                for="tasktodayCheck04">#MN2042</label>
                                                        </div>
                                                    </td>
                                                    <td>Write a release note</td>
                                                    <td>
                                                        <div>
                                                            <img src="assets/images/users/avatar-5.jpg" alt="image"
                                                                class="avatar-sm img-thumbnail rounded-circle"
                                                                title="Darnell McCormick" />
                                                        </div>
                                                    </td>
                                                    <td>Today 06pm</td>
                                                    <td><span class="badge badge-soft-success p-1">Low</span>
                                                    </td>
                                                    <td>
                                                        <ul class="list-inline table-action m-0">
                                                            <li class="list-inline-item">
                                                                <a href="javascript:void(0);" class="action-icon px-1">
                                                                    <i class="mdi mdi-square-edit-outline"></i></a>
                                                            </li>
                                                            <li class="list-inline-item">
                                                                <div class="dropdown">
                                                                    <a class="action-icon px-1 dropdown-toggle"
                                                                        href="#" data-bs-toggle="dropdown"
                                                                        aria-haspopup="true" aria-expanded="false">
                                                                        <i class="mdi mdi-dots-vertical"></i>
                                                                    </a>

                                                                    <div class="dropdown-menu dropdown-menu-end">
                                                                        <a class="dropdown-item"
                                                                            href="#">Action</a>
                                                                        <a class="dropdown-item"
                                                                            href="#">Another
                                                                            action</a>
                                                                        <a class="dropdown-item"
                                                                            href="#">Something else
                                                                            here</a>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h5 class="position-relative mb-0"><a href="#taskcollapse2" class="text-dark d-block"
                                        data-bs-toggle="collapse">Completed Tasks<span class="text-muted">(05)</span>
                                        <i class="mdi mdi-chevron-down accordion-arrow"></i></a>
                                </h5>
                                <div class="collapse show" id="taskcollapse2">
                                    <div class="table-responsive mt-3">
                                        <table class="table table-centered table-nowrap table-borderless table-sm">
                                            <thead class="table-light">
                                                <tr class="">
                                                    <th scope="col">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="taskupcomingCheck">
                                                            <label class="form-check-label"
                                                                for="taskupcomingCheck">Task
                                                                ID</label>
                                                        </div>
                                                    </th>
                                                    <th scope="col">Tasks</th>
                                                    <th scope="col">Assign to</th>
                                                    <th scope="col">Due Date</th>
                                                    <th scope="col">Task priority</th>
                                                    <th scope="col" style="width: 85px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="taskupcomingCheck03">
                                                            <label class="form-check-label"
                                                                for="taskupcomingCheck03">#MN2048</label>
                                                        </div>
                                                    </td>
                                                    <td>Code HTML email template</td>
                                                    <td>
                                                        <div>
                                                            <img src="assets/images/users/avatar-7.jpg" alt="image"
                                                                class="avatar-sm img-thumbnail rounded-circle"
                                                                title="Adrian Key" />
                                                        </div>
                                                    </td>
                                                    <td>June 08, 2020</td>
                                                    <td><span class="badge badge-soft-danger p-1">High</span>
                                                    </td>
                                                    <td>
                                                        <ul class="list-inline table-action m-0">
                                                            <li class="list-inline-item">
                                                                <a href="javascript:void(0);"
                                                                    class="action-icon px-1">
                                                                    <i class="mdi mdi-square-edit-outline"></i></a>
                                                            </li>
                                                            <li class="list-inline-item">
                                                                <div class="dropdown">
                                                                    <a class="action-icon px-1 dropdown-toggle"
                                                                        href="#" data-bs-toggle="dropdown"
                                                                        aria-haspopup="true" aria-expanded="false">
                                                                        <i class="mdi mdi-dots-vertical"></i>
                                                                    </a>

                                                                    <div class="dropdown-menu dropdown-menu-end">
                                                                        <a class="dropdown-item"
                                                                            href="#">Action</a>
                                                                        <a class="dropdown-item"
                                                                            href="#">Another
                                                                            action</a>
                                                                        <a class="dropdown-item"
                                                                            href="#">Something else
                                                                            here</a>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h5 class="position-relative mb-0"><a href="#taskcollapse3" class="text-dark d-block"
                                        data-bs-toggle="collapse">Tasks To Do<span class="text-muted">(03)</span> <i
                                            class="mdi mdi-chevron-down accordion-arrow"></i></a>
                                </h5>
                                <div class="collapse show" id="taskcollapse3">
                                    <div class="table-responsive mt-3">
                                        <table class="table table-centered table-nowrap table-borderless table-sm">
                                            <thead class="table-light">
                                                <tr class="">
                                                    <th scope="col">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="taskotherCheck">
                                                            <label class="form-check-label" for="taskotherCheck">Task
                                                                ID</label>
                                                        </div>
                                                    </th>
                                                    <th scope="col">Tasks</th>
                                                    <th scope="col">Assign to</th>
                                                    <th scope="col">Due Date</th>
                                                    <th scope="col">Task priority</th>
                                                    <th scope="col" style="width: 85px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="taskotherCheck01">
                                                            <label class="form-check-label"
                                                                for="taskotherCheck01">#MN2054</label>
                                                        </div>
                                                    </td>
                                                    <td>Brand logo design</td>
                                                    <td>
                                                        <div>
                                                            <img src="assets/images/users/avatar-9.jpg" alt="image"
                                                                class="avatar-sm img-thumbnail rounded-circle"
                                                                title="Donald Mealy" />
                                                        </div>
                                                    </td>
                                                    <td>June 04, 2020</td>
                                                    <td><span class="badge badge-soft-danger p-1">High</span>
                                                    </td>
                                                    <td>
                                                        <ul class="list-inline table-action m-0">
                                                            <li class="list-inline-item">
                                                                <a href="javascript:void(0);"
                                                                    class="action-icon px-1">
                                                                    <i class="mdi mdi-square-edit-outline"></i></a>
                                                            </li>
                                                            <li class="list-inline-item">
                                                                <div class="dropdown">
                                                                    <a class="action-icon px-1 dropdown-toggle"
                                                                        href="#" data-bs-toggle="dropdown"
                                                                        aria-haspopup="true" aria-expanded="false">
                                                                        <i class="mdi mdi-dots-vertical"></i>
                                                                    </a>

                                                                    <div class="dropdown-menu dropdown-menu-end">
                                                                        <a class="dropdown-item"
                                                                            href="#">Action</a>
                                                                        <a class="dropdown-item"
                                                                            href="#">Another
                                                                            action</a>
                                                                        <a class="dropdown-item"
                                                                            href="#">Something else
                                                                            here</a>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->
