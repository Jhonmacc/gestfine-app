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
                    <canvas id="certificateChart" style="max-width: 400px; max-height: 400px;"></canvas>
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
                    var ctx = document.getElementById('certificateChart').getContext('2d');
                    var certificateChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Dentro do prazo', 'Perto de vencer', 'Vencidos'],
                            datasets: [{
                                label: 'Status dos Certificados',
                                data: data.data,
                                backgroundColor: [
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(255, 99, 132, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(255, 99, 132, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
        });
    </script>
</x-app-layout>
