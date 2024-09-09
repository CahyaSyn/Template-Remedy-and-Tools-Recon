@extends('layouts.app')
@section('title', 'KEDB')
@section('content')
    <section class="content py-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title">Import New KIP Kedb</h3>
                                </div>
                                <a class="btn btn-default" href="javascript:void(0)" id="createKedbKip">
                                    <i class="fas fa-plus"></i> Add Kedb KIP Manual
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="formKipCsv" method="POST" enctype="multipart/form-data">
                                <div class="form-group mb-3">
                                    <label for="">File</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file_kedb_kip" name="file_kedb_kip">
                                        <label class="custom-file-label" for="file">Choose file</label>
                                    </div>
                                </div>
                                <button type="submit" id="importKipBtn" class="btn btn-primary">Import</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title">Import Old Kedb</h3>
                                </div>
                                <div>
                                    <a class="btn btn-default" href="javascript:void(0)" id="createOldKedb">
                                        <i class="fas fa-plus"></i> Add Old Kedb Manual
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="formOldKedbCsv" method="POST" enctype="multipart/form-data">
                                <div class="form-group mb-3">
                                    <label for="">File</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file_kedb" name="file_kedb">
                                        <label class="custom-file-label" for="file">Choose file</label>
                                    </div>
                                </div>
                                <button type="submit" id="importOldKedbBtn" class="btn btn-primary">Import</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title">Notes</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col col-md-auto">
                                    <p class="mb-0 text-muted" style="font-size: 14px">Format CSV KIP Version :</p>
                                    <p class="text-warning" style="font-size: 14px">Parent_kedb<span class="text-white">|</span>Child_kedb<span class="text-white">|</span>Apps<span
                                              class="text-white">|</span>Old_kedb<span class="text-white">|</span>New_symtom_kedb<span class="text-white">|</span>New_specific_symtom_kedb<span
                                              class="text-white">|</span>Kedb_finalisasi<span class="text-white">|</span>Action<span class="text-white">|</span>Responsibility_action<span
                                              class="text-white">|</span>Sop
                                    </p>
                                </div>
                                <div class="col col-md-auto">
                                    <p class="mb-0 text-muted" style="font-size: 14px">Format CSV Old Version :</p>
                                    <p class="text-warning" style="font-size: 14px">Just Old Kedb Format</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title">All list of kedb</h3>
                                </div>
                                <div class="d-flex">
                                    <div class="mr-2">
                                        <a href="{{ route('kedb.exportcsv') }}" class="btn btn-success">Export Kedb</a>
                                    </div>
                                    <div>
                                        <form action="{{ route('kedb.clear') }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete All Kedb</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="tbkedbs" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>KEDB</th>
                                        <th>Application</th>
                                        <th>Action</th>
                                        <th>Symtom_KEDB</th>
                                        <th>Specific_Symtom_KEDB</th>
                                        <th>Responsibility</th>
                                        <th>Parent</th>
                                        <th>Child</th>
                                        <th>Sop_Link</th>
                                        <th>Option</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>KEDB</th>
                                        <th>Application</th>
                                        <th>Action</th>
                                        <th>Symtom_KEDB</th>
                                        <th>Specific_Symtom_KEDB</th>
                                        <th>Responsibility</th>
                                        <th>Parent</th>
                                        <th>Child</th>
                                        <th>Sop_Link</th>
                                        <th>Option</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="modal fade" id="ajaxKedbKip">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="modalheadingKip">Add Kedb</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="form" id="form-kedbkip">
                                                <div class="row">
                                                    <div class="col">
                                                        <input type="hidden" name="kedb_id" id="kedb_id">
                                                        <div class="form-group mb-3">
                                                            <label for="kedb_parent_id">Parent Kedb</label>
                                                            <select class="custom-select" name="kedb_parent_id" id="kedb_parent_id">

                                                            </select>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="kedb_child_id">Child Kedb</label>
                                                            <select class="custom-select" name="kedb_child_id" id="kedb_child_id">

                                                            </select>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="app_id">Application Name</label>
                                                            <select class="custom-select app_id" name="app_id" id="app_id">

                                                            </select>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="old_kedb">Old Kedb</label>
                                                            <input type="text" class="form-control" id="old_kedb" name="old_kedb">
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label for="new_symtom_kedb">New Symtom Kedb</label>
                                                            <input type="text" class="form-control" id="new_symtom_kedb" name="new_symtom_kedb">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group mb-3">
                                                            <label for="new_specific_symtom_kedb">New Specific Symtom Kedb</label>
                                                            <input type="text" class="form-control" id="new_specific_symtom_kedb" name="new_specific_symtom_kedb">
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="kedb_finalisasi">Kedb Finalisasi</label>
                                                            <input type="text" class="form-control" id="kedb_finalisasi" name="kedb_finalisasi">
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label for="action">Action</label>
                                                            <input type="text" class="form-control" id="action" name="action">
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label for="responsibility_action">Responsibility Action</label>
                                                            <input type="text" class="form-control" id="responsibility_action" name="responsibility_action">
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label for="sop">SOP</label>
                                                            <input type="text" class="form-control" id="sop" name="sop">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                    <button type="submit" id="saveBtnKip" class="btn btn-primary" value="create">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="ajaxKedbOld">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="modalheadingOld">Add Kedb</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="form" id="form-kedbold">
                                                <div class="row">
                                                    <div class="col">
                                                        <input type="hidden" name="kedb_id" id="kedb_id">
                                                        <div class="form-group mb-3">
                                                            <label for="app_id">Application Name</label>
                                                            <select class="custom-select app_id" name="app_id" id="app_id">

                                                            </select>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label for="kedb_finalisasi">Kedb</label>
                                                            <input type="text" class="form-control" id="kedb_finalisasi" name="kedb_finalisasi">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                    <button type="submit" id="saveBtnOld" class="btn btn-primary" value="create">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="ajaxKedbDelete">
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

            var table = $('#tbkedbs')
                .DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('kedb.index') }}",
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'kedb_finalisasi',
                            name: 'kedb_finalisasi'
                        },
                        {
                            data: 'app_name',
                            name: 'app_name'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        },
                        {
                            data: 'new_symtom_kedb',
                            name: 'new_symtom_kedb'
                        },
                        {
                            data: 'new_specific_symtom_kedb',
                            name: 'new_specific_symtom_kedb',
                        },
                        {
                            data: 'responsibility_action',
                            name: 'responsibility_action',
                        },
                        {
                            data: 'kedb_parent_name',
                            name: 'kedb_parent_name'
                        },
                        {
                            data: 'kedb_child_name',
                            name: 'kedb_child_name',
                        },
                        {
                            data: 'sop',
                            name: 'sop',
                        },
                        {
                            data: 'option',
                            name: 'option',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    responsive: true,
                    autoWidth: false,
                    order: [
                        [3, 'desc']
                    ]
                });

            $('#createKedbKip').click(function() {
                $('#saveBtnKip').val('create-kedbkip');
                $('#form-kedbkip').trigger('reset');
                $('#modalheadingKip').html('Add Kedb Kip');
                $('#ajaxKedbKip').modal('show');
            });

            $('#createOldKedb').click(function() {
                $('#saveBtnOld').val('create-kedbold');
                $('#form-kedbold').trigger('reset');
                $('#modalheadingOld').html('Add Old Kedb');
                $('#ajaxKedbOld').modal('show');
            });

            $.ajax({
                url: "{{ route('kedb.getapp') }}",
                type: 'GET',
                success: function(data) {
                    $('.app_id').empty();
                    $('.app_id').append('<option value="">Select Application</option>');
                    $.each(data, function(index, app) {
                        $('.app_id').append('<option value="' + app.app_id + '">' + app.app_name + '</option>');
                    });
                }
            });

            $.ajax({
                url: "{{ route('kedb.getparent') }}",
                type: 'GET',
                success: function(data) {
                    $('#kedb_parent_id').empty();
                    $('#kedb_parent_id').append('<option value="">Select Parent Kedb</option>');
                    $.each(data, function(index, parent) {
                        $('#kedb_parent_id').append('<option value="' + parent.kedb_parent_id + '">' + parent.kedb_parent_name + '</option>');
                    });
                }
            });

            $.ajax({
                url: "{{ route('kedb.getchild') }}",
                type: 'GET',
                success: function(data) {
                    $('#kedb_child_id').empty();
                    $('#kedb_child_id').append('<option value="">Select Child Kedb</option>');
                    $.each(data, function(index, child) {
                        $('#kedb_child_id').append('<option value="' + child.kedb_child_id + '">' + child.kedb_child_name + '</option>');
                    });
                }
            });

            $('body').on('click', '.editKedb', function() {
                var kedb_id = $(this).data('id');
                $.ajax({
                    url: "{{ route('kedb.index') }}" + '/' + kedb_id + '/edit',
                    type: 'GET',
                    success: function(response) {
                        $('#form-kedbkip').attr('action', "{{ route('kedb.index') }}" + '/' + kedb_id);
                        $('#kedb_id').val(response.kedb_id);
                        $('#old_kedb').val(response.old_kedb);
                        $('#new_symtom_kedb').val(response.new_symtom_kedb);
                        $('#new_specific_symtom_kedb').val(response.new_specific_symtom_kedb);
                        $('#kedb_finalisasi').val(response.kedb_finalisasi);
                        $('#action').val(response.action);
                        $('#responsibility_action').val(response.responsibility_action);
                        $('#sop').val(response.sop);

                        $.ajax({
                            url: "{{ route('kedb.getapp') }}",
                            type: 'GET',
                            success: function(data) {
                                $('#app_id').empty();
                                $('#app_id').append('<option value="">Select Application</option>');
                                $.each(data, function(index, app) {
                                    $('#app_id').append('<option value="' + app.app_id + '">' + app.app_name + '</option>');
                                });
                                $('#app_id').val(response.app_id);
                            }
                        });

                        $.ajax({
                            url: "{{ route('kedb.getparent') }}",
                            type: 'GET',
                            success: function(data) {
                                $('#kedb_parent_id').empty();
                                $('#kedb_parent_id').append('<option value="">Select Parent Kedb</option>');
                                $.each(data, function(index, parent) {
                                    $('#kedb_parent_id').append('<option value="' + parent.kedb_parent_id + '">' + parent.kedb_parent_name + '</option>');
                                });
                                $('#kedb_parent_id').val(response.kedb_parent_id);
                            }
                        });

                        $.ajax({
                            url: "{{ route('kedb.getchild') }}",
                            type: 'GET',
                            success: function(data) {
                                $('#kedb_child_id').empty();
                                $('#kedb_child_id').append('<option value="">Select Child Kedb</option>');
                                $.each(data, function(index, child) {
                                    $('#kedb_child_id').append('<option value="' + child.kedb_child_id + '">' + child.kedb_child_name + '</option>');
                                });
                                $('#kedb_child_id').val(response.kedb_child_id);
                            }
                        });

                        $('#ajaxKedbKip').modal('show');
                    },

                    error: function(response) {
                        console.log('Error:', response);
                    }
                });
            });

            $('#saveBtnKip').click(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $('#form-kedbkip').serialize(),
                    url: "{{ route('kedb.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        $('#form-kedbkip').trigger("reset");
                        $('#ajaxKedbKip').modal('hide');
                        table.draw();
                        toastr.success('Data added successfully.', 'Success', {
                            timeOut: 5000
                        });
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        $('#saveBtnKip').html('Save Changes');
                        toastr.error('Data not added.', 'Error', {
                            timeOut: 5000
                        });
                    }
                });
            });

            $('#saveBtnOld').click(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $('#form-kedbold').serialize(),
                    url: "{{ route('kedb.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        $('#form-kedbold').trigger("reset");
                        $('#ajaxKedbOld').modal('hide');
                        table.draw();
                        toastr.success('Data added successfully.', 'Success', {
                            timeOut: 5000
                        });
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        $('#saveBtnOld').html('Save Changes');
                        toastr.error('Data not added.', 'Error', {
                            timeOut: 5000
                        });
                    }
                });
            });

            $('#formKipCsv').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('kedb.importkedbkipcsv') }}",
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log('Success:', response.count_kedb_exist);
                        console.log('Success:', response.count_kedb_not_exist);
                        var count_kedb_exist = response.count_kedb_exist;
                        var count_kedb_not_exist = response.count_kedb_not_exist;
                        if (count_kedb_exist > 0) {
                            toastr.info('Data already exist, ' + count_kedb_exist + ' data not imported.', 'Info', {
                                timeOut: 5000
                            });
                        }
                        if (count_kedb_not_exist > 0) {
                            toastr.success('Data imported successfully, ' + count_kedb_not_exist + ' data imported.', 'Success', {
                                timeOut: 5000
                            });
                        }
                        table.draw();
                    },
                    error: function(response) {
                        console.log('Error:', response);
                    }
                });
            });

            $('#formOldKedbCsv').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('kedb.importoldkedbcsv') }}",
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log('Success:', response.app_name);
                        console.log('Success kedb name exist:', response.kedb_name_exist);
                        console.log('Success exist:', response.count_oldkedb_exist);
                        console.log('Success not exist:', response.count_oldkedb_not_exist);
                        var count_oldkedb_exist = response.count_oldkedb_exist;
                        var count_oldkedb_not_exist = response.count_oldkedb_not_exist;
                        if (count_oldkedb_exist > 0) {
                            toastr.info('Data already exist, ' + count_oldkedb_exist + ' data not imported.', 'Info', {
                                timeOut: 5000
                            });
                        }
                        if (count_oldkedb_not_exist > 0) {
                            toastr.success('Data imported successfully, ' + count_oldkedb_not_exist + ' data imported.', 'Success', {
                                timeOut: 5000
                            });
                        }
                        table.draw();
                    },
                    error: function(response) {
                        console.log('Error:', response);
                    }
                });
            });

            $('body').on('click', '.deleteKedb', function() {
                var kedb_id = $(this).data("id");
                $('#form-delete').attr('action', "{{ route('kedb.index') }}" + '/' + kedb_id);
                $('#ajaxKedbDelete').modal('show');
            });
        });
    </script>
@endsection
