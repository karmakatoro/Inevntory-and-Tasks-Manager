<div class="modal fade" id="task-report-modal" tabindex="-1" role="dialog" aria-labelledby="users-modalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="users-modalTitle">Share the file</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="errorsDiv"
                    class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade mb-xl-0"
                    style="display:none;" role="alert">
                    Errors</strong><br>
                    <div class="errorsList"></div>
                </div>
                <form class="needs-validation was-validated" method="POST" action="{{ route('share-opts') }}"
                    id="requestShare" novalidate="">
                    <input type="hidden" name="id" id="taskFileId">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12">
                            <label for="gender" class="form-label">Who can have acces to this file?</label>
                            <select class="form-select" name="share_opt" id="gender">
                                <option selected>Only me</option>
                                <option value="selected">Selected people</option>
                            </select>
                        </div>
                        <div class="col-lg-12 col-sm-12 mt-3">
                            <p class="mb-1 fw-medium mt-3 mt-md-0">Assign to users</p>
                            <select class="form-control select2-share" name="share[]" data-toggle="select2"
                                multiple="multiple" data-placeholder="Choose ...">
                                {{-- <optgroup label="Alaskan/Hawaiian Time Zone"> --}}
                                @foreach ($assigns as $project_task)
                                    <option data-image="{{ asset('storage/users/' . $project_task->user->photo) }}"
                                        value="{{ $project_task->user->id }}">
                                        {{ $project_task->user->name }}
                                    </option>
                                @endforeach
                                {{-- </optgroup> --}}
                            </select>
                            @if ($errors->has('share'))
                                <p class="text-pink mt-2">
                                    {{ $errors->first('share') }}
                                </p>
                            @endif

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="btnSave" class="btn btn-primary">Save changes</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $('.select2-share').select2({
            dropdownParent: $('#task-report-modal'),
            width: '100%',
            templateResult: function(option) {
                if (!option.id) {
                    return option.text;
                }
                var imageUrl = $(option.element).data('image');
                var $option = $(
                    '<div><img src="' + imageUrl +
                    '" class="img-avatar" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 8px;" />' +
                    option.text + '</div>'
                );
                return $option;
            },
            templateSelection: function(option) {
                if (!option.id) {
                    return option.text;
                }
                var imageUrl = $(option.element).data('image');
                var $selectedOption = $(
                    '<div><img src="' + imageUrl +
                    '" class="img-avatar" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 8px;" />' +
                    option.text + '</div>'
                );
                return $selectedOption;
            }
        });

        $(document).on("click", "#btnSave", function(e) {
            e.preventDefault();
            var form = $("#requestShare")

            var submitBtn = $("#btnSave");
            var singleId = $("#taskFileId");
            submitBtn.html(
                `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Loading...`
            );
            submitBtn.prop("disabled", true);

            $.ajax({
                url: form.attr("action"),
                method: form.attr("method"),
                data: form.serialize(),
                success: function(response) {

                    if (response.status == true) {
                        Swal.fire({
                            title: "Done!",
                            text: response.message,
                            icon: "success",
                            confirmButtonColor: "#1abc9c",
                        });
                        singleId.val("0");
                        $(form).trigger("reset");
                        $("#task-report-modal").modal("hide");
                    } else if (response.status == false) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: response.message,
                            confirmButtonColor: "#3bafda",
                        });
                    }
                    submitBtn.html("Save changes");
                    submitBtn.prop("disabled", false);
                },
                error: function(xhr, status, error) {
                    var errors = xhr.responseJSON.errors;
                    var errorString = "";
                    $.each(errors, function(key, value) {
                        errorString += value[0] + "<br>";
                    });
                    $(".errorsList").html(errorString);
                    $("#errorsDiv").css("display", "");
                    $("#errorsDiv").addClass("show");
                    submitBtn.html("Save changes");
                    submitBtn.prop("disabled", false);
                },
            });

        });
    })
</script>
