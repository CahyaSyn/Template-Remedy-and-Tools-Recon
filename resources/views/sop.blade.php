@extends('layouts.app')
@section('title', 'SOP')
@section('content')
    <section class="content py-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title">All list of SOP</h3>
                                </div>
                                <div>
                                    <a class="btn btn-default" href="javascript:void(0)" id="createSop">
                                        <i class="fas fa-plus"></i> Add SOP
                                    </a>
                                    <div class="modal fade" id="ajaxSop">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="modalHeading">Add New SOP</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="form-sop" action="" method="POST">
                                                        <input type="hidden" name="sop_id" id="sop_id">
                                                        <div class="form-group mb-3">
                                                            <label for="sop_name">SOP Name</label>
                                                            <input type="text" class="form-control" id="sop_name" name="sop_name" placeholder="Enter SOP name">
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label for="sop_link">Link</label>
                                                            <textarea id="sop_link" name="sop_link" class="form-control" rows="3"></textarea>
                                                        </div>
                                                        <div class="modal-footer justify-content-between">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="tbsop" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>PIC Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>PIC Name</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="modal fade" id="ajaxSopDelete">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-body text-center">
                                            <form id="form-delete" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <p>Are you sure want to delete this data?</p>
                                                <div class="d-flex justify-content-center">
                                                    <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">No</button>
                                                    <button type="submit" id="deleteBtn" class="btn btn-primary">Yes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        $(function() {
            bsCustomFileInput.init();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#tbsop').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sop.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'sop_name',
                        name: 'sop_name'
                    },
                    {
                        data: 'option',
                        name: 'option',
                        orderable: false,
                        searchable: false
                    },
                ],
                responsive: true,
                autoWidth: false,
                order: [
                    [0, 'asc']
                ]
            });

            $('#createSop').click(function() {
                $('#saveBtn').val("create-sop");
                $('#sop_id').val('');
                $('#form-sop').trigger("reset");
                $('#modalHeading').html("Add New SOP");
                $('#ajaxSop').modal('show');
            });

            $('body').on('click', '.editSop', function() {
                var sop_id = $(this).data('id');
                $.get("{{ route('sop.index') }}" + '/' + sop_id + '/edit', function(data) {
                    $('#modalHeading').html("Edit SOP");
                    $('#saveBtn').val("edit-sop");
                    $('#ajaxSop').modal('show');
                    $('#sop_id').val(data.sop_id);
                    $('#sop_name').val(data.sop_name);
                    $('#sop_link').val(data.sop_link);
                })
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $('#form-sop').serialize(),
                    url: "{{ route('sop.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        $('#form-sop').trigger("reset");
                        $('#ajaxSop').modal('hide');
                        table.draw();
                        toastr.success('Data saved successfully.', 'Success', {
                            timeOut: 5000
                        });
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        table.draw();
                        toastr.error('Data not saved.', 'Error', {
                            timeOut: 5000
                        });
                    }
                });
            });

            $('body').on('click', '.deleteSop', function() {
                var sop_id = $(this).data("id");
                $('#form-delete').attr('action', "{{ url('sop') }}" + '/' + sop_id);
                $('#ajaxSopDelete').modal('show');
            });

            // when click link button, then open link in new tab
            $('body').on('click', '.linkSop', function() {
                var sop_id = $(this).data("id");
                $.get("{{ route('sop.index') }}" + '/' + sop_id + '/edit', function(data) {
                    window.open(data.sop_link, '_blank');
                })
            });
        });
    </script>
@endsection
