<div>
    <div
        x-data="chart"
        x-init="init($wire.dataset)"
        wire:ignore
        wire:loading.class="opacity-50"
        class="relative h-[10rem] sm:h-[22rem] w-full overflow-hidden bg-white rounded-xl p-6 border border-slate-200"
    >
        <h3 role="heading" aria-level="3" class="absolute top-4 left-5.5 text-lg font-medium text-gray-800">
            Montant des factures par type
            @if($filters->range !== \App\Livewire\Pages\Dashboard\Range::All_Time)
                <span class="text-sm font-normal text-gray-500 ml-2">
                    ({{ $filters->range->label($filters->start, $filters->end) }})
                </span>
            @endif
        </h3>

        <canvas class="pt-10 w-full"></canvas>

        <div x-show="!hasData" class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm">
            <span>Aucune donnée disponible pour ce filtre</span>
        </div>
    </div>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets

@script
<script>
    Alpine.data('chart', () => {
        let chart = null;

        return {
            hasData: true,

            init(initialData) {
                this.refreshChart(initialData);

                this.$wire.$watch('dataset', newData => {
                    this.refreshChart(newData);
                });

                window.addEventListener('popstate', () => {
                    setTimeout(() => this.refreshChart(this.$wire.dataset), 200);
                });
            },

            refreshChart(dataset) {
                if (!dataset || !dataset.labels || dataset.labels.length === 0) {
                    this.hasData = false;
                    if (chart) chart.destroy();
                    chart = null;
                    return;
                }

                this.hasData = true;

                if (chart) {
                    chart.destroy();
                    chart = null;
                }

                setTimeout(() => {
                    const el = this.$wire.$el.querySelector('canvas');
                    if (!el) return;

                    chart = this.createChart(el, dataset);
                }, 10);
            },

            createChart(el, dataset) {
                const { labels, values } = dataset;

                const colors = ['#1E40AF', '#4F46E5', '#7C3AED', '#9333EA', '#C026D3', '#D946EF', '#EC4899', '#EF4444', '#F97316', '#F59E0B', '#10B981', '#14B8A6', '#6B7280', '#4B5563', '#374151', '#1F2937'];
                const hoverColors = ['#1C3879', '#4338CA', '#6D28D9', '#7E22CE', '#A21CAF', '#C026D3', '#DB2777', '#DC2626', '#EA580C', '#D97706', '#059669', '#0D9488', '#4B5563', '#374151', '#1F2937'];

                return new Chart(el, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Montant des factures par type',
                            data: values,
                            backgroundColor: context => colors[context.dataIndex % colors.length],
                            hoverBackgroundColor: context => hoverColors[context.dataIndex % hoverColors.length],
                            borderWidth: 0,
                            borderRadius: 8,
                            barThickness: 'flex',
                            minBarLength: 10,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 600,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            title: { display: false },
                            legend: { display: false },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                displayColors: false,
                                backgroundColor: 'rgba(17, 24, 39, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 12,
                                cornerRadius: 6,
                                titleFont: {
                                    size: 13,
                                    weight: '600',
                                },
                                bodyFont: {
                                    size: 13,
                                },
                                callbacks: {
                                    title: context => context[0].label,
                                    label: context => {
                                        const value = context.raw || 0;
                                        return 'Montant: ' + new Intl.NumberFormat('fr-FR', {
                                            style: 'currency',
                                            currency: 'EUR'
                                        }).format(value);
                                    }
                                }
                            },
                        },
                        hover: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            x: {
                                display: true,
                                grid: { display: false },
                                border: { display: false },
                                ticks: {
                                    autoSkip: true,
                                    maxRotation: 35,
                                    minRotation: 0,
                                    padding: 0,
                                    font: {
                                        size: 11,
                                        color: '#6B7280'
                                    },
                                    callback: function(value, index) {
                                        const label = this.getLabelForValue(index);
                                        if (label && label.length > 12) {
                                            return label.substring(0, 10) + '...';
                                        }
                                        return label;
                                    }
                                }
                            },
                            y: {
                                display: true,
                                border: { display: false },
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    color: '#F3F4F6',
                                    lineWidth: 1
                                },
                                ticks: {
                                    padding: 0,
                                    callback: function(value) {
                                        const options = value >= 1000000
                                            ? {
                                                style: 'currency',
                                                currency: 'EUR',
                                                notation: 'compact',
                                                compactDisplay: 'short',
                                                maximumFractionDigits: 1
                                            }
                                            : {
                                                style: 'currency',
                                                currency: 'EUR',
                                                maximumFractionDigits: 0
                                            };

                                        return new Intl.NumberFormat('fr-FR', options).format(value);
                                    },
                                    font: {
                                        size: 11,
                                        color: '#6B7280'
                                    }
                                },
                            },
                        },
                    },
                });
            }
        };
    });
</script>
@endscript
