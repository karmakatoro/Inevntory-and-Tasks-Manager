$(document).ready(function () {
    "use strict";
    $("#products-datatable").DataTable({
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>",
            },
            info: "Showing customers _START_ to _END_ of _TOTAL_",
            lengthMenu:
                'Display <select class=\'form-select form-select-sm mx-1\'><option value="10">10</option><option value="20">20</option><option value="-1">All</option></select> users',
        },
        pageLength: 10,
        columns: [
            {
                orderable: !1,
                render: function (e, l, a, o) {
                    return (
                        "display" === l &&
                            (e =
                                '<div class="form-check font-16 mb-0"><input class="form-check-input dt-checkboxes" type="checkbox"><label class="form-check-label">&nbsp;</label></div>'),
                        e
                    );
                },
                checkboxes: {
                    selectRow: !0,
                    selectAllRender:
                        '<div class="form-check font-16 mb-0"><input class="form-check-input dt-checkboxes" type="checkbox"><label class="form-check-label">&nbsp;</label></div>',
                },
            },
            { orderable: !0 },
            { orderable: !0 },
            { orderable: !0 },
            { orderable: !0 },
            { orderable: !0 },
            { orderable: !0 },
            { orderable: !1 },
        ],
        select: { style: "multi" },
        order: [[5, "asc"]],
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass(
                "pagination-rounded"
            );
        },
    });
});
