@extends('layouts.app')
@section('title', 'Template Form')
@section('content')
    <section class="content py-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="">
                                    <h3 class="card-title">Form template</h3>
                                </div>
                                <div>
                                    <button class="btn btn-danger" id="reset">Reset Form</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('form.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col col-md-4">
                                        <label for="starts_at" class="form-label">Start Time</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <button type="button" id="btn-start" class="btn btn-success"><span class="font-weight-bold font-italic">Start at</span></button>
                                            </div>
                                            <input type="text" id="starts_at" name="starts_at" class="form-control form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at') }}"
                                                   readonly />
                                            @error('starts_at')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="ticket_id">Nomor Ticket</label>
                                            <input type="text" id="ticket_id" name="ticket_id" class="form-control form-control @error('ticket_id') is-invalid @enderror"
                                                   value="{{ old('ticket_id') }}" />
                                            @error('ticket_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="app_id">Application</label>
                                            <select id="app_id" name="app_id" class="form-control custom-select @error('app_id') is-invalid @enderror" data-placeholder="Select application">
                                                <option value="">Please select</option>
                                                @foreach ($applications as $app)
                                                    <option value="{{ $app->app_id }}">{{ $app->app_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('app_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="casename">Casename</label>
                                            <textarea id="casename" name="casename" class="form-control @error('casename') is-invalid @enderror" rows="2">{{ old('casename') }}</textarea>
                                            @error('casename')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="action">Action</label>
                                            <textarea id="action" name="action" class="form-control @error('action') is-invalid @enderror" rows="2">{{ old('action') }}</textarea>
                                            @error('action')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="nextaction">Next Action</label>
                                            <textarea id="nextaction" name="nextaction" class="form-control @error('nextaction') is-invalid @enderror" rows="3">{{ old('nextaction') }}</textarea>
                                            @error('nextaction')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="evidence">Evidence</label>
                                            <textarea id="evidence" name="evidence" class="form-control" rows="3">{{ old('evidence') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kedb_id">KEDB</label>
                                            <select class="form-control select2bs4 get_kedb @error('kedb_id') is-invalid @enderror" id="kedb_id" name="kedb_id"
                                                    data-placeholder="Please select the application first">
                                            </select>
                                            @error('kedb_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="assignment">Assignment</label>
                                            <select id="assignment" name="assignment" class="form-control custom-select @error('assignment') is-invalid @enderror" data-placeholder="Select assignment">
                                                <option value="">Please select</option>
                                                <option value="Assign Surrounding">Assign Surrounding</option>
                                                <option value="Escalate L2">Escalate L2</option>
                                                <option value="Resolved">Resolved</option>
                                            </select>
                                            @error('assignment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col col-md-6">
                                        <div class="form-group">
                                            <label for="user_id">PIC Handling</label>
                                            <select id="user_id" name="user_id" class="form-control custom-select custom-select @error('user_id') is-invalid @enderror"
                                                    data-placeholder="Select PIC Handling">
                                                <option selected="" value="">Please select</option>
                                                @foreach ($pic_name as $pic)
                                                    <option value="{{ $pic->user_id }}">{{ $pic->username }}</option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col col-md-4">
                                        <label for="ends_at" class="form-label">End Time</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <button type="button" id="btn-end" class="btn btn-sm btn-danger"><span class="font-weight-bold font-italic">&nbsp;&nbsp;&nbsp;End
                                                        at&nbsp;&nbsp;&nbsp;</span></button>
                                            </div>
                                            <input type="text" id="ends_at" name="ends_at" class="form-control form-control @error('ends_at') is-invalid @enderror"value="{{ old('ends_at') }}"
                                                   readonly />
                                            @error('ends_at')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col d-flex justify-content-end align-items-center">
                                        <button style="margin-top: 14px" type="submit" class="btn btn-primary w-100">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title">Notes/Parameter from last ticket</h3>
                                </div>
                                <div>
                                    <button class="btn btn-primary" id="edit_ticket">Edit last ticket</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between">
                                            <label for="notes">Notes</label>
                                            <span id="notes-alert"></span>
                                            <button class="btn btn-sm btn-info" id="btn-copy-notes">Copy</button>
                                        </div>
                                        <textarea class="form-control mt-3" rows="3" id="notes" readonly>{{ $last_form->notes }}</textarea>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between">
                                            <label for="parameter">Parameter</label>
                                            <span id="parameter-alert"></span>
                                            <button class="btn btn-sm btn-info" id="btn-copy-parameter">Copy</button>
                                        </div>
                                        <textarea class="form-control mt-3" rows="3" id="parameter" readonly>{{ $last_form->parameter }}</textarea>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between">
                                            <label for="doc">Copy to doc</label>
                                            <span id="doc-alert"></span>
                                            <button class="btn btn-sm btn-info" id="btn-copy-doc">Copy</button>
                                        </div>
                                        <textarea class="form-control mt-3" rows="3" id="doc" readonly>{{ $last_form->document }}</textarea>
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
    {{-- <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script> --}}
    <script>
        $(document).ready(function() {
            $(document).on('change', '#app_id', function() {
                var app_id = $(this).val();
                $('#kedb_id').show();
                $.ajax({
                    method: 'get',
                    url: "{{ route('form.getkedb') }}",
                    datatype: 'json',
                    data: {
                        app_id: app_id
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            $('.get_kedb').select2({
                                placeholder: 'Select one',
                                templateResult: function(data) {
                                    if (!data.id) {
                                        return data.text;
                                    }
                                    var text = data.text.split('|');
                                    var result = `<span style="font-size: 12px"><b>${text[0]}</b><br> Symptom : ${text[2]}</span>`;
                                    return $(result);
                                },
                                templateSelection: function(data) {
                                    if (!data.id) {
                                        return data.text;
                                    }
                                    var text = data.text.split('|');
                                    return text[0];
                                }
                            });

                            let all_options = "<option value=''>Select one</option>";
                            $.each(res.data, function(index, value) {
                                all_options +=
                                    `<option value='${value.kedb_id}'>${value.kedb_finalisasi}|${value.old_kedb}|${value.new_specific_symtom_kedb}|${value.action}</option>`;
                            });
                            $('.get_kedb').html(all_options);
                        }
                    }
                });
            });

            $("#reset").click(function() {
                $('.get_kedb').html("<option value=''>Select one</option>");
            });
        });
    </script>
    <script>
        $("#btn-start").click(function() {
            var d = new Date();
            if (d.getMonth() + 1 < 10) {
                var m = "0" + (d.getMonth() + 1);
            } else {
                var m = d.getMonth() + 1;
            }
            if (d.getDate() < 10) {
                var dd = "0" + d.getDate();
            } else {
                var dd = d.getDate();
            }
            if (d.getHours() < 10) {
                var hh = "0" + d.getHours();
            } else {
                var hh = d.getHours();
            }
            if (d.getMinutes() < 10) {
                var mm = "0" + d.getMinutes();
            } else {
                var mm = d.getMinutes();
            }
            if (d.getSeconds() < 10) {
                var ss = "0" + d.getSeconds();
            } else {
                var ss = d.getSeconds();
            }
            var now = d.getFullYear() + "" + m + "" + dd + "" + hh + "" + mm + "" + ss;
            $("#starts_at").val(now);
        });

        $("#btn-end").click(function() {
            var d = new Date();
            if (d.getMonth() + 1 < 10) {
                var m = "0" + (d.getMonth() + 1);
            } else {
                var m = d.getMonth() + 1;
            }
            if (d.getDate() < 10) {
                var dd = "0" + d.getDate();
            } else {
                var dd = d.getDate();
            }
            if (d.getHours() < 10) {
                var hh = "0" + d.getHours();
            } else {
                var hh = d.getHours();
            }
            if (d.getMinutes() < 10) {
                var mm = "0" + d.getMinutes();
            } else {
                var mm = d.getMinutes();
            }
            if (d.getSeconds() < 10) {
                var ss = "0" + d.getSeconds();
            } else {
                var ss = d.getSeconds();
            }
            var now = d.getFullYear() + "" + m + "" + dd + "" + hh + "" + mm + "" + ss;
            $("#ends_at").val(now);
        });
    </script>
    <script>
        $("#reset").click(function() {
            $("#starts_at").val("");
            $("#ends_at").val("");
            $("#ticket_id").val("");
            $("#app_id").val("");
            $("#casename").val("");
            $("#action").val("");
            $("#nextaction").val("");
            $("#evidence").val("");
            $("#kedb_id").val("");
            $("#assignment").val("");
            $("#user_id").val("");
        });
    </script>
    <script>
        $("#btn-copy-notes").click(function() {
            var notes = document.getElementById("notes");
            var notes_alert = document.getElementById("notes-alert");
            if (notes.value === "") {
                notes_alert.innerHTML = `<span class="badge badge-danger">Notes is empty</span>`;
            } else {
                notes.select();
                document.execCommand("copy");
                notes_alert.innerHTML = `<span class="badge badge-success">Notes copied!!</span>`;

                setTimeout(function() {
                    notes_alert.innerHTML = "";
                }, 2000);
            }
        });

        $("#btn-copy-parameter").click(function() {
            var parameter = document.getElementById("parameter");
            var notes_alert = document.getElementById("parameter-alert");
            if (parameter.value === "") {
                notes_alert.innerHTML = `<span class="badge badge-danger">Parameter is empty</span>`;
            } else {
                parameter.select();
                document.execCommand("copy");
                notes_alert.innerHTML = `<span class="badge badge-success">Parameter copied!!</span>`;

                setTimeout(function() {
                    notes_alert.innerHTML = "";
                }, 2000);
            }
        });

        $("#btn-copy-doc").click(function() {
            var g5_doc = document.getElementById("doc");
            var notes_alert = document.getElementById("doc-alert");
            if (g5_doc.value === "") {
                notes_alert.innerHTML = `<span class="badge badge-danger">Document Group is empty</span>`;
            } else {
                g5_doc.select();
                document.execCommand("copy");
                notes_alert.innerHTML = `<span class="badge badge-success">Document Group copied!!</span>`;

                setTimeout(function() {
                    notes_alert.innerHTML = "";
                }, 2000);
            }
        });
    </script>
    <script>
        $("#edit_ticket").click(function() {
            $.ajax({
                method: 'get',
                url: "{{ route('get.lastform') }}",
                datatype: 'json',
                success: function(res) {
                    console.log(res);
                    if (res.status == 'success') {
                        $('#starts_at').val(res.data.starts_at);
                        $("#ticket_id").val(res.data.ticket_id);
                        $("#casename").val(res.data.casename);
                        $("#action").val(res.data.action);
                        $("#nextaction").val(res.data.nextaction);
                        $("#evidence").val(res.data.evidence);
                        $("#assignment").val(res.data.assignment);
                        $("#user_id").val(res.data.user_id);
                        $('#ends_at').val(res.data.ends_at);
                    }
                }
            });
        });
    </script>
@endsection
