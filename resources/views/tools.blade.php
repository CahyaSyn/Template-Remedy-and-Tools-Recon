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
                                <li class="nav-item"><a class="nav-link" href="#recon_recharge" data-toggle="tab">RECHARGE</a></li>
                                <li class="nav-item"><a class="nav-link" href="#recon_injectvf" data-toggle="tab">VF</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="recon_arp">
                                    <div class="row">
                                        <div class="col">
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
                                                        <button type="submit" class="btn btn-primary mt-3">Process File Recon</button>
                                                    </div>
                                                    <div>
                                                        <a href="{{ asset('TEMPLATE_ARP - Copy.xlsx') }}" class="btn btn-success mt-3">Download Template</a>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col">
                                            <h5>Notes :</h5>
                                            <ul>
                                                <li>Download Template.xlsx</li>
                                                <li>Masukkan <i><u>Hasil Recon</u></i> ke sheet <b>DATA_RECON</b> sesuai header</li>
                                                <li>Masukkan <i><u>Hasil Recon Already Success</u></i> ke sheet <b>ALREADY_SUCCESS</b> sesuai header</li>
                                                <li>Masukkan <i><u>Hasil pengecekan LOS, UPCC, TC, DOM dan NGRS</u></i> ke masing - masing sheetname <b>LOS, UPCC, TC, DOM dan NGRS</b> sesuai header</li>
                                                <li>Upload file kemudian klik <b>Process File Recon</b></li>
                                                <li>Pastikan file di <i><b>cek kembali</b></i> untuk antisipasi jika terdapat anomaly</li>
                                            </ul>
                                            <h5>Changelog :</h5>
                                            <ul>
                                                <li>Add : Summary di sheet SUMMARY</li>
                                                <li>Add : Fungsi download file excel hasil recon dan csv file untuk update di GUI dalam satu zip file</li>
                                                <li>Fix : Error ketika sheet LOS, UPCC, TC, DOM dan NGRS kosong</li>
                                                <li>Fix : Logic pengecekan activation_status berdasarkan status TC</li>
                                                <li>Fix : Logic pengecekan recharge_status berdasarkan status di DOM dan NGRS</li>
                                                <li>Fix : Logic pengecekan package_status berdasarkan los < 30 dan selisih created_at dan subscriber_date di UPCC</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="recon_vas">
                                    <div class="row">
                                        <div class="col">
                                            <p>Sedang dalam pengerjaan</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="recon_recharge">
                                    <div class="row">
                                        <div class="col">
                                            <p>Sedang dalam pengerjaan</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="recon_injectvf">
                                    <div class="row">
                                        <div class="col">
                                            <p>Sedang dalam pengerjaan</p>
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
        });
    </script>
@endsection
