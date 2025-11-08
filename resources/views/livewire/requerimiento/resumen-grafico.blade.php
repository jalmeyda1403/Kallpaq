<div class="card">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0">Evolución Mensual de Requerimientos</h6>
    </div>
    <div class="card-body">
        <canvas id="graficoRequerimientos"></canvas>
    </div>
</div>
@push('scripts')
    <script>
        Livewire.on('renderGrafico', ({
            labels,
            asignados,
            finalizados,
            programados
        }) => {

            const ctx = document.getElementById('graficoRequerimientos').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Asignados',
                            data: asignados,
                            borderColor: 'rgba(108, 117, 125, 1)',
                            backgroundColor: 'rgba(108, 117, 125, 0.2)',
                            tension: 0.3,
                            fill: true,
                            pointRadius: 4
                        },
                        {
                            label: 'Fin Planificado',
                            data: programados,
                            borderColor: 'rgba(80, 200, 162, 1)',
                            backgroundColor: 'rgba(80, 200, 162, 0.2)',
                            tension: 0.3,
                            fill: true,
                            pointRadius: 4
                        },
                        {
                            label: 'Fin Real',
                            data: finalizados,
                            borderColor: 'rgba(0, 123, 255, 1)', // azul celeste (opaco)
                            backgroundColor: 'rgba(0, 123, 255, 0.2)', // azul celeste transparente
                            tension: 0.3,
                            fill: true,
                            pointRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }
                }
            });
        });
    </script>
@endpush
