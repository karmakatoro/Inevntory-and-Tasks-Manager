<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if ($task->status != 'compteted')
                    <div class="fileupload btn btn-success waves-effect waves-light mb-3">
                        <span><i class="mdi mdi-cloud-upload me-1"></i> Upload Files</span>
                        <form action="{{ route('tasks_report.store') }}" id="requestTaskFile" method="POST"
                            enctype="multipart/form-data">
                            <input type="hidden" name="task_id" value="{{ $task->id }}">
                            <input type="file" name="files[]" id="uploadTaskFile" class="upload" multiple>
                        </form>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-centered  table-nowrap mb-0" id="tasks-files-dt"
                        data-url="{{ route('tasks.show', ['task' => $task->id]) }}">
                        <thead class="table-light">
                            <tr>
                                <th class="col" style="width: 20px;">
                                    <div class="form-check font-16 mb-0">
                                        <input id="checkAllRows" class="form-check-input" type="checkbox"
                                            id="customerlist">
                                        <label class="form-check-label" for="customerlist">&nbsp;</label>
                                    </div>
                                </th>
                                <th scope="col">File Name</th>
                                <th scope="col">Date Modified</th>
                                <th scope="col">Size</th>
                                <th scope="col">Author</th>
                                <th scope="col" class="text-center" style="width: 125px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>
<!-- end row -->
@include('pages.tasks.modal-tasks-report');
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        currentDt = $("#tasks-files-dt").DataTable({
            autoWidth: false,
            order: [0, "ASC"],
            processing: true,
            serverSide: true,
            searchDelay: 1000,
            paging: true,
            ajax: {
                url: $(this).attr('data-url'),
            },
            iDisplayLength: "10",
            columns: [{
                    data: "checkbox",
                    name: "checkbox",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "name",
                    name: "name",
                    className: "text-900 sort pe-1 align-middle white-space-nowrap",
                },
                {
                    data: "updated_at",
                    name: "updated_at",
                    className: "text-900 sort pe-1 align-middle white-space-nowrap",
                },
                {
                    data: "size",
                    name: "size",
                    className: "text-900 sort pe-1 align-middle white-space-nowrap",
                },
                {
                    data: "author",
                    name: "author",
                    className: "text-900 sort pe-1 align-middle white-space-nowrap",
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false,
                },
            ],
            lengthMenu: [10, 25, 50, 100],
        });
        $(document).on('change', '#uploadTaskFile', function(e) {
            e.preventDefault();
            var form = $("#requestTaskFile");
            const fd = new FormData(form[0]);
            $.ajax({
                url: form.attr("action"),
                method: form.attr("method"),
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        Swal.fire({
                            title: "Done!",
                            text: response.message,
                            icon: "success",
                            confirmButtonColor: "#1abc9c",
                        });
                        $(form).trigger("reset");
                        currentDt.ajax.reload();
                    } else if (response.status == false) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: response.message,
                            confirmButtonColor: "#3bafda",
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "An error occure while uploading files",
                        confirmButtonColor: "#3bafda",
                    });
                },
            });

        })
        $(document).on('click', '.share-btn', function(e) {
            e.preventDefault();
            $("#requestShare")[0].reset();
            $("#taskFileId").val($(this).attr('data-id'));
            $("#task-report-modal").modal('show');
        });

        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            let url = $(this).attr('data-url');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#1abc9c",
                cancelButtonColor: "#f1556c",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'delete',
                        success: function(response) {
                            if (response.status == true) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#1abc9c",
                                });
                                currentDt.ajax.reload();
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: response.message,
                                    confirmButtonColor: "#3bafda",
                                });
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {

                            if (jqXHR.status === 403) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Acces Denied!",
                                    confirmButtonColor: "#3bafda",
                                    footer: '<strong>Error code :</strong> 403',
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "An error occured",
                                    confirmButtonColor: "#3bafda",
                                });
                            }
                        }
                    });
                }
            });

        });
    });
</script>
