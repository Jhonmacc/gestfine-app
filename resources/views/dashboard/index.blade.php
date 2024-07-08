@extends('layouts.dashboard')
@section('content')
<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <legend class="badge text-bg-primary span12" style="font-size: 18px;" for="">Gráficos dos Certificados</legend>
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div id="certificateChartContainer">
                                <canvas id="certificateChart" style="max-width: 400px; max-height: 400px;"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="cpfCnpjChartContainer">
                                <canvas id="cpfCnpjChart" style="max-width: 400px; max-height: 400px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('{{ route("dashboard.chartData") }}')
                .then(response => response.json())
                .then(data => {
                    var ctx1 = document.getElementById('certificateChart').getContext('2d');
                    var certificateChart = new Chart(ctx1, {
                        type: 'pie',
                        data: {
                            labels: ['Dentro do prazo', 'Perto de vencer', 'Vencidos'],
                            datasets: [{
                                label: 'Status dos Certificados',
                                data: data.statusData,
                                backgroundColor: [
                                    'rgba(20, 190, 100, 0.1)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(255, 99, 132, 0.2)',
                                ],
                                borderColor: [
                                    'rgba(20, 190, 100, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(255, 99, 132, 1)',
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });

                    var ctx2 = document.getElementById('cpfCnpjChart').getContext('2d');
                    var cpfCnpjChart = new Chart(ctx2, {
                        type: 'pie',
                        data: {
                            labels: ['Pessoa Física', 'Pessoa Jurídica'],
                            datasets: [{
                                label: 'Certificados',
                                data: [data.cpfCount, data.cnpjCount],
                                backgroundColor: [
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(50, 0, 150, 0.2)',
                                ],
                                borderColor: [
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(50, 0, 150, 1)',
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                })
                .catch(error => {
                    console.error('Erro ao obter dados:', error);
                });
        });
    </script>
</x-app-layout>
@endsection
