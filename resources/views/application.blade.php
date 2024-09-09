@extends('layouts.app')
@section('title', 'Application')
@section('content')
    <section class="content py-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title">Project Application List</h3>
                                </div>
                                <div>
                                    <a type="button" class="btn btn-default" href="javascript:void(0)" id="createApp">
                                        <i class="fas fa-plus"></i> Add Application
                                    </a>
                                    <div class="modal fade" id="ajaxApp">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="modalHeading">Add New Application</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="form-app" action="" method="POST">
                                                        <input type="hidden" name="app_id" id="app_id">
                                                        <div class="form-group mb-3">
                                                            <label for="app_name">Application Name</label>
                                                            <input type="text" class="form-control" id="app_name" name="app_name" placeholder="Enter application name">
                                                        </div>
                                                        <div class="justify-content-between">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save
                                                                changes</button>
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
                            <table id="tbapps" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Appication Name</th>
                                        <th>Option</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Appication Name</th>
                                        <th>Option</th>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="modal fade" id="ajaxAppDelete">
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

            var table = $('#tbapps').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('application.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'app_name',
                        name: 'app_name'
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
                    [1, 'asc']
                ]
            });

            $('#createApp').click(function() {
                $('#saveBtn').val("create-app");
                $('#app_id').val('');
                $('#form-app').trigger("reset");
                $('#modalHeading').html("Add New Application");
                $('#ajaxApp').modal('show');
            });

            $('body').on('click', '.editApp', function() {
                var app_id = $(this).data('id');
                $.get("{{ route('application.index') }}" + '/' + app_id + '/edit', function(data) {
                    $('#modalHeading').html("Edit Application");
                    $('#saveBtn').val("edit-app");
                    $('#ajaxApp').modal('show');
                    $('#app_id').val(data.app_id);
                    $('#app_name').val(data.app_name);
                })
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $('#form-app').serialize(),
                    url: "{{ route('application.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        $('#form-app').trigger("reset");
                        $('#ajaxApp').modal('hide');
                        table.draw();
                        toastr.success('Application saved successfully.', 'Success', {
                            timeOut: 3000
                        });
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        toastr.error('Application not saved.', 'Error', {
                            timeOut: 3000
                        });
                    }
                });
            });

            $('body').on('click', '.deleteApp', function() {
                var app_id = $(this).data("id");
                $('#form-delete').attr('action', "{{ url('application') }}" + '/' + app_id);
                $('#ajaxAppDelete').modal('show');
            });
        });
    </script>
@endsection
