<div>
    <div
        x-data="chart"
        x-init="$wire.$on('statusChanged', () => { setTimeout(() => refreshChart($wire.dataset), 50); });
                   $wire.$on('familyMemberChanged', () => { setTimeout(() => refreshChart($wire.dataset), 50); })"
        wire:ignore
        wire:loading.class="opacity-50"
        class="relative h-[10rem] sm:h-[20rem] w-full overflow-hidden bg-white rounded-xl p-4 border border-gray-200"
    >
        <canvas class="w-full"></canvas>

        <div x-show="!hasData" class="absolute inset-0 flex items-center justify-center text-gray-500 text-sm">
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

            init() {
                window.addEventListener('popstate', () => {
                    setTimeout(() => this.refreshChart(this.$wire.dataset), 200);
                });

                this.refreshChart(this.$wire.dataset);

                this.$wire.$watch('dataset', (newData) => {
                    this.refreshChart(newData);
                });
            },

            destroy() {
                if (chart) chart.destroy();
                chart = null;
            },

            refreshChart(dataset) {
                if (!dataset || !dataset.labels || !dataset.values || dataset.labels.length === 0) {
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
                    chart = this.initChart(dataset);
                }, 10);
            },

            initChart(dataset) {
                let el = this.$wire.$el.querySelector('canvas');

                if (!el) return null;

                let { labels, values } = dataset;

                return new Chart(el, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Montant des factures',
                            data: values,
                            backgroundColor: function(context) {
                                const colors = [
                                    '#4F86C6', '#5DB075', '#E47D7D', '#9747FF',
                                    '#F7C04A', '#3F8CFF', '#FF9F40', '#4BC0C0',
                                    '#8884D8', '#FF6384', '#36A2EB', '#FFCD56',
                                    '#FF91A4', '#C9CBCF', '#7788EE', '#B2E061',
                                    '#E887B2', '#82DDFA'
                                ];

                                const index = context.dataIndex % colors.length;
                                return colors[index];
                            },
                            hoverBackgroundColor: function(context) {
                                const colors = [
                                    '#2E5EAA', '#3A8A54', '#C05555', '#7A35DF',
                                    '#D6A93A', '#2667D6', '#DC7520', '#2F9E9E',
                                    '#6663BB', '#E33F66', '#1A81C7', '#DBAC38',
                                    '#DE6A84', '#A6A8AD', '#5465D3', '#8FBD42',
                                    '#CE6399', '#57BFE5'
                                ];

                                const index = context.dataIndex % colors.length;
                                return colors[index];
                            },
                            borderWidth: 0,
                            borderRadius: 4,
                            barThickness: 'flex',
                            minBarLength: 10,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 400,
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Montant des factures par type',
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                },
                                padding: {
                                    bottom: 10
                                }
                            },
                            legend: { display: false },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                displayColors: false,
                                callbacks: {
                                    title: function(context) {
                                        return context[0].label;
                                    },
                                    label: function(context) {
                                        let value = context.raw || 0;
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
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                            },
                        },
                        scales: {
                            x: {
                                display: true,
                                grid: {
                                    display: false,
                                },
                                ticks: {
                                    autoSkip: true,
                                    maxRotation: 45,
                                    minRotation: 0,
                                    font: {
                                        size: 10
                                    },
                                    callback: function(value, index) {
                                        let label = this.getLabelForValue(index);
                                        if (label && label.length > 15) {
                                            return label.substring(0, 12) + '...';
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
                                    color: '#E5E7EB'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat('fr-FR', {
                                            style: 'currency',
                                            currency: 'EUR',
                                            maximumFractionDigits: 0
                                        }).format(value);
                                    },
                                    font: {
                                        size: 10
                                    }
                                },
                            },
                        },
                    },
                })
            },
        }
    })
</script>
@endscript
