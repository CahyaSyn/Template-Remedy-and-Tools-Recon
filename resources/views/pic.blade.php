@extends('layouts.app')
@section('title', 'Pic')
@section('content')
    <section class="content py-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title">Import User CSV</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="formUserCsv" method="POST" enctype="multipart/form-data">
                                <div class="form-group mb-3">
                                    <label for="">File</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file_user_csv" name="file_user_csv">
                                        <label class="custom-file-label" for="file">Choose file</label>
                                        <p class="mb-0 mt-2 text-muted" style="font-size: 14px">Format CSV :</p>
                                        <p class="text-warning" style="font-size: 12px">Name<span class="text-white">|</span>Password<span class="text-white">|</span>Email_tsel<span
                                                  class="text-white">|</span>No_hp<span class="text-white">|</span>No_wa<span class="text-white">|</span>Email_solusi<span
                                                  class="text-white">|</span>Gmail<span class="text-white">|</span>Role<span class="text-white">|</span>Site_office<span
                                                  class="text-white">|</span>Hire_date
                                        </p>
                                    </div>
                                </div>
                                <button type="submit" id="importBtn" class="btn btn-primary">Import</button>
                            </form>
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
                                    <h3 class="card-title">All list of pic</h3>
                                </div>
                                <div>
                                    <a class="btn btn-default" href="javascript:void(0)" id="createUser">
                                        <i class="fas fa-plus"></i> Add pic
                                    </a>
                                    <div class="modal fade" id="ajaxUser">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="modalHeading">Add New PIC</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="form-pic" action="" method="POST">
                                                        <input type="hidden" name="user_id" id="user_id">
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="form-group mb-3">
                                                                    <label for="kedb">Ldap</label>
                                                                    <input type="text" class="form-control" id="ldap" name="ldap" placeholder="Input Ldap">
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label for="kedb">PIC Name</label>
                                                                    <input type="text" class="form-control" id="username" name="username" placeholder="Input PIC">
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label for="kedb">No HP</label>
                                                                    <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Input No HP">
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label for="kedb">No WA</label>
                                                                    <input type="text" class="form-control" id="no_wa" name="no_wa" placeholder="Input No WA">
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label for="kedb">Site</label>
                                                                    <input type="text" class="form-control" id="office_site" name="office_site" placeholder="Input Site">
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <div class="form-group mb-3">
                                                                    <label for="kedb">Email Tsel</label>
                                                                    <input type="text" class="form-control" id="email_tsel" name="email_tsel" placeholder="Input Email Tsel">
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label for="kedb">Email Solusi</label>
                                                                    <input type="text" class="form-control" id="email_solusi" name="email_solusi" placeholder="Input Email Solusi">
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label for="kedb">Email Gmail</label>
                                                                    <input type="text" class="form-control" id="email_gmail" name="email_gmail" placeholder="Input Email Gmail">
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label for="kedb">Hire Date</label>
                                                                    <input type="date" class="form-control" id="hire_date" name="hire_date" placeholder="Input Hire Date">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                            <button type="submit" id="saveBtn" class="btn btn-primary" value="create">Save</button>
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
                            <table id="tbuser" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Ldap</th>
                                        <th>PIC Name</th>
                                        <th>No HP</th>
                                        <th>No WA</th>
                                        <th>Email Tsel</th>
                                        <th>Email Solusi</th>
                                        <th>Email Gmail</th>
                                        <th>Site</th>
                                        <th>Hire Date</th>
                                        <th>Option</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Ldap</th>
                                        <th>PIC Name</th>
                                        <th>No HP</th>
                                        <th>No WA</th>
                                        <th>Email Tsel</th>
                                        <th>Email Solusi</th>
                                        <th>Email Gmail</th>
                                        <th>Site</th>
                                        <th>Hire Date</th>
                                        <th>Option</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="modal fade" id="ajaxUserDelete">
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

            var table = $('#tbuser')
                .DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('pic.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                        },
                        {
                            data: 'ldap',
                            name: 'ldap'
                        },
                        {
                            data: 'username',
                            name: 'username'
                        },
                        {
                            data: 'no_hp',
                            name: 'no_hp'
                        },
                        {
                            data: 'no_wa',
                            name: 'no_wa'
                        },
                        {
                            data: 'email_tsel',
                            name: 'email_tsel'
                        },
                        {
                            data: 'email_solusi',
                            name: 'email_solusi'
                        },
                        {
                            data: 'email_gmail',
                            name: 'email_gmail'
                        },
                        {
                            data: 'office_site',
                            name: 'office_site'
                        },
                        {
                            data: 'hire_date',
                            name: 'hire_date'
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
                        [2, 'asc']
                    ],
                });

            $('#createUser').click(function() {
                $('#saveBtn').val("create-user");
                $('#user_id').val('');
                $('#form-pic').trigger("reset");
                $('#modalHeading').html("Add New PIC");
                $('#ajaxUser').modal('show');
            });

            $('body').on('click', '.editPic', function() {
                var user_id = $(this).data('id');
                $.get("{{ route('pic.index') }}" + '/' + user_id + '/edit', function(data) {
                    $('#saveBtn').val("edit-user");
                    $('#modalHeading').html("Edit PIC");
                    $('#ajaxUser').modal('show');
                    $('#user_id').val(data.user_id);
                    $('#ldap').val(data.ldap);
                    $('#username').val(data.username);
                    $('#no_hp').val(data.no_hp);
                    $('#no_wa').val(data.no_wa);
                    $('#email_tsel').val(data.email_tsel);
                    $('#email_solusi').val(data.email_solusi);
                    $('#email_gmail').val(data.email_gmail);
                    $('#office_site').val(data.office_site);
                    $('#hire_date').val(data.hire_date);
                })
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $('#form-pic').serialize(),
                    url: "{{ route('pic.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        $('#form-pic').trigger("reset");
                        $('#ajaxUser').modal('hide');
                        table.draw();
                        toastr.success('Data saved successfully.', 'Success', {
                            timeOut: 5000
                        });
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        $('#saveBtn').html('Save Changes');
                        toastr.error('An error occurred while saving data.', 'Error', {
                            timeOut: 5000
                        });
                    }
                });
            });

            $('body').on('click', '.deletePic', function() {
                var user_id = $(this).data("id");
                $('#form-delete').attr('action', "{{ route('pic.index') }}" + '/' + user_id);
                $('#ajaxUserDelete').modal('show');
            });

            $('#formUserCsv').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('pic.importusercsv') }}",
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        console.log('Success:', data.count_user_exist);
                        console.log('Success:', data.count_user_not_exist);
                        var count_user_exist = data.count_user_exist;
                        var count_user_not_exist = data.count_user_not_exist;
                        if (count_user_exist > 0) {
                            toastr.info('Data already exist, ' + count_user_exist + ' data not imported.', 'Info', {
                                timeOut: 5000
                            });
                        }
                        if (count_user_not_exist > 0) {
                            toastr.success('Data imported successfully, ' + count_user_not_exist + ' data imported.', 'Success', {
                                timeOut: 5000
                            });
                        }
                        table.draw();
                    },
                });
            });
        });
    </script>
@endsection
