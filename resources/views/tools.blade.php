@extends('layouts.app')
@section('title', 'Tools')
@section('content')
    <section class="content py-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#recon_arp" data-toggle="tab">ARP</a></li>
                                <li class="nav-item"><a class="nav-link" href="#recon_vas" data-toggle="tab">VAS</a></li>
                                <li class="nav-item"><a class="nav-link" href="#recon_recharge" data-toggle="tab">Recharge</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="recon_arp">
                                    <form action="{{ route('tools.importFiles') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="recon_file_arp" name="recon_file_arp">
                                                    <label class="custom-file-label" for="recon_file_arp">Choose file RECON_ARP.xlsx</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <button type="submit" class="btn btn-primary mt-3">Import Files</button>
                                            </div>
                                            <div>
                                                <a href="{{ asset('TEMPLATE_ARP - Copy.xlsx') }}" class="btn btn-success mt-3">Download Template</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane" id="recon_vas">
                                    <!-- Content for VAS tab -->
                                </div>
                                <div class="tab-pane" id="recon_recharge">
                                    <!-- Content for Recharge tab -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <h5>Notes</h5>
                    <ul>
                        <li>Download Template</li>
                        <li>Masukkan hasil rekon ke DATA_RECON sesuai header</li>
                        <li>Masukkan hasil rekon already success ke DATA_RECON sesuai header</li>
                        <li>Masukan hasil pengecekan LOS, ARPU, PROFILE, DOM dan NGRS ke masing - masing sheetname sesuai header</li>
                        <li>Import template yang sudah di isi</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        $(function() {
            bsCustomFileInput.init();
        });
    </script>
@endsection
