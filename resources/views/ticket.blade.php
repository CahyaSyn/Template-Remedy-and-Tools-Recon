@extends('layouts.app')
@section('title', 'Ticket List')
@section('styles')
    <style>
        .wrap-text {
            white-space: normal;
            word-wrap: break-word;
        }
    </style>
@endsection
@section('content')
    <section class="content py-3">
        <div class="container-fluid">
            <div class="row">
                <dov class="col">
                    <div class="card">
                        <div class="card-body">
                            {{-- import excel --}}
                            <form action="{{ route('ticketlist.importexcel') }}" method="POST" enctype="multipart/form-data" class="d-inline">
                                @csrf
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFile" name="files[]" multiple>
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Import Excel</button>
                            </form>
                        </div>
                    </div>
                </dov>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title">All list of ticket</h3>
                                </div>

                                <div>
                                    <div class="mr-2 d-flex">
                                        <div class="mr-2">
                                            <a href="{{ route('ticketlist.exportexcelperday') }}" class="btn btn-success">Export Ticket Today</a>
                                        </div>
                                        <div class="mr-2">
                                            <a href="{{ route('ticketlist.exportexcel') }}" class="btn btn-success">Export Ticket</a>
                                        </div>
                                        <div>
                                            <form action="{{ route('ticketlist.clear') }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Delete All Ticket</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="tbticketlist" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Created At</th>
                                        <th>Ticket Number</th>
                                        <th>KEDB</th>
                                        <th>Status</th>
                                        <th>PIC</th>
                                        <th>Notes</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Created At</th>
                                        <th>Ticket Number</th>
                                        <th>KEDB</th>
                                        <th>Status</th>
                                        <th>PIC</th>
                                        <th>Notes</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="modal fade" id="ajaxformDelete">
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
                            <div class="modal fade" id="ajaxformShow">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="card-header">
                                            <h3 class="card-title" id="nomor_ticket"></h3>
                                        </div>
                                        <div class="modal-body text-center">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between">
                                                            <label for="notes">Notes</label>
                                                            <button class="btn btn-sm btn-info" id="btn-copy-notes">Copy</button>
                                                        </div>
                                                        <textarea class="form-control mt-3" rows="10" id="notes" readonly></textarea>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between">
                                                            <label for="parameter">Parameter</label>
                                                            <button class="btn btn-sm btn-info" id="btn-copy-parameter">Copy</button>
                                                        </div>
                                                        <textarea class="form-control mt-3" rows="10" id="parameter" readonly></textarea>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between">
                                                            <label for="document">Copy to doc</label>
                                                            <button class="btn btn-sm btn-info" id="btn-copy-doc">Copy</button>
                                                        </div>
                                                        <textarea class="form-control mt-3" rows="10" id="document" readonly></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
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

            var table = $('#tbticketlist')
                .addClass('nowrap')
                .DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('ticketlist.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'ticket_id',
                            name: 'ticket_id'
                        },
                        {
                            data: 'kedb_finalisasi',
                            name: 'kedb_finalisasi',
                            className: 'wrap-text'
                        },
                        {
                            data: 'assignment',
                            name: 'assignment'
                        },
                        {
                            data: 'username',
                            name: 'username'
                        },
                        {
                            data: 'notes',
                            name: 'notes',
                            className: 'wrap-text'
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
                        [1, 'desc']
                    ]
                });

            // Delete ticket
            $('body').on('click', '.deleteForm', function() {
                var form_id = $(this).data("id");
                $('#form-delete').attr('action', "{{ route('ticketlist.index') }}" + '/' + form_id);
                $('#ajaxformDelete').modal('show');
            });

            // Show ticket
            $('body').on('click', '.showForm', function() {
                var form_id = $(this).data("id");
                $.get("{{ route('ticketlist.index') }}" + '/' + form_id, function(data) {
                    $('#nomor_ticket').text("Detail Ticket - " + data.ticket_id);
                    $('#parameter').val(data.parameter);
                    $('#notes').val(data.notes);
                    $('#document').val(data.document);
                    $('#ajaxformShow').modal('show');
                    console.log(data);
                });
            });
        });
    </script>
@endsection
