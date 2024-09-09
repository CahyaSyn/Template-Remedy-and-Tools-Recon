@extends('layouts.app')
@section('title', 'Dashboard')
@section('styles')
    <style>
        .wrap-text {
            white-space: normal;
            text-wrap: balance;
        }
    </style>
@endsection
@section('content')
    <section class="content py-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h5>Summary tickets in this {{ $month }}</h5>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-ticket-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total ticket</span>
                            <span class="info-box-number">{{ $count_ticket_per_month }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-danger elevation-1"><i class="far fa-share-square"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Assign Surrounding</span>
                            <span class="info-box-number">{{ $count_ticket_assign_surrounding_per_month }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-share-square"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Escalate L2</span>
                            <span class="info-box-number">{{ $count_ticket_escalate_l2_per_month }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-thumbs-up"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Resolved</span>
                            <span class="info-box-number">{{ $count_ticket_resolved_per_month }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <h5>Summary Ticket This Year</h5>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-ticket-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total ticket</span>
                            <span class="info-box-number">{{ $count_ticket }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-danger elevation-1"><i class="far fa-share-square"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Assign Surrounding</span>
                            <span class="info-box-number">{{ $count_ticket_assign_surrounding }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-share-square"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Escalate L2</span>
                            <span class="info-box-number">{{ $count_ticket_escalate_l2 }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-thumbs-up"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Resolved</span>
                            <span class="info-box-number">{{ $count_ticket_resolved }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-center">
                                <strong>Chart Ticket last 24 Days</strong>
                            </p>
                            <div class="chart">
                                <canvas id="ticket_chart_data" height="180" style="height: 180px"></canvas>
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
                                    <h5 class="card-title mb-0">Top 10 tickets based on KEDB</h5>
                                </div>
                                <div>
                                    <h3 class="card-title"><span class="badge px-5 py-3" style="background-color: #3F6791">Updated : {{ $datenow }}</span></h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- <div class="row">
                                <div class="col">
                                    <p>{{ $ticket_with_most_kedb_id }}: {{ $count_ticket_with_most_kedb_id }}</p>
                                </div>
                            </div> --}}
                            <table id="tb_top" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Top Tickets</th>
                                        <th>Count Ticket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($top_kedb_finalisasi_labels as $index => $label)
                                        <tr>
                                            <td>{{ $label }}</td>
                                            <td>{{ $top_kedb_finalisasi_counts[$index] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total Tickets</th>
                                        <th>{{ $top_kedb_finalisasi_counts->sum() }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection
@section('scripts')
    <script>
        $(function() {
            $('#tb_top').DataTable({
                "responsive": true,
                "autoWidth": false,
                "paging": false,
                "searching": false,
                "info": false,
                "ordering": false,
                "columnDefs": [{
                    "targets": "_all",
                    "className": "wrap-text"
                }]
            });
        });

        $(function() {
            'use strict'
            var ctx = document.getElementById('ticket_chart_data').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: 'Jumlah Ticket Harian',
                        data: @json($data),
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                fontColor: "white",
                                beginAtZero: true
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                fontColor: "white",
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        });
    </script>
@endsection
