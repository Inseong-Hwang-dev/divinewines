<?php
/**
 * @var \App\View\AppView $this
 * @var float $currentRevenue
 * @var float $previousRevenue
 * @var float $salesGrowthRate
 * @var float $conversionRate
 * @var int $totalOrders
 * @var int $totalCustomers
 * @var \Cake\Collection\CollectionInterface $monthlyRevenue
 * @var \Cake\I18n\Date $startDate
 * @var \Cake\I18n\Date $endDate
 * @var \Cake\I18n\Date $previousStartDate
 * @var \Cake\I18n\Date $previousEndDate
 */
?>
<div class="analytics-dashboard">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Analytics Dashboard</h1>
            <div class="date-filter">
                <?= $this->Form->create(null, ['type' => 'get', 'class' => 'd-flex gap-3']) ?>
                <div class="form-group">
                    <?= $this->Form->control('start_date', [
                        'type' => 'date',
                        'label' => false,
                        'value' => $startDate->format('Y-m-d'),
                        'class' => 'form-control'
                    ]) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('end_date', [
                        'type' => 'date',
                        'label' => false,
                        'value' => $endDate->format('Y-m-d'),
                        'class' => 'form-control'
                    ]) ?>
                </div>
                <?= $this->Form->button('Apply', ['class' => 'btn btn-primary']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row g-4 mb-4">
            <!-- Revenue Card -->
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <h3>Total Revenue</h3>
                    </div>
                    <div class="metric-value">
                        <?php
                        $revenueChange = 0;
                        if ($previousRevenue > 0) {
                            $revenueChange = (($currentRevenue - $previousRevenue) / $previousRevenue) * 100;
                        } elseif ($currentRevenue > 0) {
                            $revenueChange = 100; // If previous was 0 and current is positive, that's 100% growth
                        }
                        ?>
                        <div class="trend-indicator <?= $revenueChange > 0 ? 'up' : ($revenueChange < 0 ? 'down' : 'neutral') ?>">
                            <i class="bi bi-caret-<?= $revenueChange > 0 ? 'up' : ($revenueChange < 0 ? 'down' : '') ?>"></i>
                            <?= abs(round($revenueChange, 1)) ?>%
                        </div>
                        <?= $this->Number->currency($currentRevenue) ?>
                    </div>
                    <div class="metric-chart">
                        <canvas id="revenue-sparkline" width="100%" height="50"></canvas>
                    </div>
                    <div class="metric-detail">
                        <?= $startDate->format('M d, Y') ?> - <?= $endDate->format('M d, Y') ?>
                    </div>
                </div>
            </div>

            <!-- Sales Growth Card -->
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h3>Sales Growth</h3>
                    </div>
                    <div class="metric-value">
                        <div class="trend-indicator <?= $salesGrowthRate > 0 ? 'up' : ($salesGrowthRate < 0 ? 'down' : 'neutral') ?>">
                            <i class="bi bi-caret-<?= $salesGrowthRate > 0 ? 'up' : ($salesGrowthRate < 0 ? 'down' : '') ?>"></i>
                            <?= abs(round($salesGrowthRate, 1)) ?>%
                        </div>
                        <span class="metric-amount"><?= $this->Number->toPercentage($salesGrowthRate) ?></span>
                    </div>
                    <div class="metric-gauge">
                        <canvas class="growth-gauge"></canvas>
                    </div>
                    <div class="metric-detail">
                        <?= $startDate->format('M d, Y') ?> - <?= $endDate->format('M d, Y') ?> vs 
                        <?= $previousStartDate->format('M d, Y') ?> - <?= $previousEndDate->format('M d, Y') ?>
                    </div>
                </div>
            </div>

            <!-- Conversion Rate Card -->
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3>Conversion Rate</h3>
                    </div>
                    <div class="metric-value">
                        <div class="trend-indicator <?= $conversionTrend > 0 ? 'up' : ($conversionTrend < 0 ? 'down' : 'neutral') ?>">
                            <i class="bi bi-caret-<?= $conversionTrend > 0 ? 'up' : ($conversionTrend < 0 ? 'down' : '') ?>"></i>
                            <?= abs(round($conversionTrend, 1)) ?>%
                        </div>
                        <?= $this->Number->toPercentage($conversionRate) ?>
                    </div>
                    <div class="metric-scale">
                        <canvas class="conversion-scale"></canvas>
                    </div>
                    <div class="metric-detail">
                        <?= $startDate->format('M d, Y') ?> - <?= $endDate->format('M d, Y') ?>
                        <br>
                        <?= $totalOrders ?> orders from <?= $totalCustomers ?> customers
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="row">
            <div class="col-12">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Monthly Revenue Trend</h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Wait for both DOM and Chart.js to be ready
window.addEventListener('load', function() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded!');
        return;
    }

    // Revenue Sparkline
    const revenueCanvas = document.getElementById('revenue-sparkline');
    if (!revenueCanvas) {
        console.error('Revenue sparkline canvas not found!');
        return;
    }

    const revenueCtx = revenueCanvas.getContext('2d');
    if (!revenueCtx) {
        console.error('Could not get 2d context for revenue sparkline!');
        return;
    }

    // Get the daily revenue data
    <?php
    // Create array of all dates in range
    $allDates = [];
    $allData = [];
    
    // Use CakePHP's Date methods
    $currentDate = clone $startDate;
    while ($currentDate->lessThanOrEquals($endDate)) {
        $allDates[] = $currentDate->format('Y-m-d');
        $allData[] = 0;
        $currentDate = $currentDate->addDays(1);
    }

    // Fill in actual revenue data
    foreach ($dailyRevenue as $item) {
        $index = array_search($item->date, $allDates);
        if ($index !== false) {
            $allData[$index] = floatval($item->revenue);
        }
    }
    ?>

    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($allDates) ?>,
            datasets: [{
                data: <?= json_encode($allData) ?>,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                borderWidth: 2,
                fill: 'start',
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 4,
                spanGaps: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            },
            layout: {
                padding: {
                    top: 10,
                    right: 10,
                    bottom: 10,
                    left: 10
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return '$' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: false,
                    grid: {
                        display: false
                    }
                },
                y: {
                    display: false,
                    beginAtZero: true,
                    grid: {
                        display: false
                    },
                    suggestedMin: 0,
                    suggestedMax: Math.max(...<?= json_encode($allData) ?>) * 1.1 // Add 10% padding to max value
                }
            },
            elements: {
                line: {
                    tension: 0.4
                }
            }
        }
    });

    // Growth Gauge
    const growthCtx = document.querySelector('.growth-gauge').getContext('2d');
    new Chart(growthCtx, {
        type: 'bar',
        data: {
            labels: [''],
            datasets: [{
                data: [<?= abs(round($salesGrowthRate, 1)) ?>],
                backgroundColor: <?= $salesGrowthRate < 0 ? "'#e74c3c'" : "'rgb(75, 192, 192)'" ?>,
                borderRadius: 20,
                barPercentage: 0.6,
                categoryPercentage: 0.6,
                borderWidth: 0
            }, {
                data: [100],
                backgroundColor: 'rgb(240, 240, 240)',
                borderRadius: 20,
                barPercentage: 0.6,
                categoryPercentage: 0.6,
                borderWidth: 0
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    filter: function(tooltipItem) {
                        return tooltipItem.datasetIndex === 0;
                    },
                    callbacks: {
                        label: function(context) {
                            return <?= $salesGrowthRate < 0 ? "-" : "" ?> + context.raw + '% growth';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    display: false,
                    grid: {
                        display: false
                    },
                    stacked: true
                },
                y: {
                    display: false,
                    grid: {
                        display: false
                    },
                    stacked: true
                }
            },
            elements: {
                bar: {
                    borderRadius: 20
                }
            }
        }
    });

    // Conversion Scale
    const conversionCtx = document.querySelector('.conversion-scale').getContext('2d');
    new Chart(conversionCtx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [<?= round($conversionRate, 1) ?>, <?= 100 - round($conversionRate, 1) ?>],
                backgroundColor: [
                    'rgb(75, 192, 192)',
                    'rgb(240, 240, 240)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            circumference: 180,
            rotation: -90,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    filter: function(tooltipItem) {
                        return tooltipItem.datasetIndex === 0 && tooltipItem.dataIndex === 0;
                    },
                    callbacks: {
                        label: function(context) {
                            return context.raw + '% of customers made a purchase';
                        }
                    }
                }
            }
        }
    });

    // Main Revenue Chart
    const mainCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(mainCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($monthlyRevenue->map(function($item) {
                return date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year));
            })) ?>,
            datasets: [{
                label: 'Monthly Revenue',
                data: <?= json_encode($monthlyRevenue->map(function($item) {
                    return $item->revenue;
                })) ?>,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>

<style>
.analytics-dashboard {
    background-color: #f8f9fa;
    min-height: 100vh;
    padding: 1rem;
}

.metric-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: transform 0.2s;
}

.metric-card:hover {
    transform: translateY(-5px);
}

.metric-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.metric-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    background: rgba(75, 192, 192, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.metric-icon i {
    font-size: 1.25rem;
    color: rgb(75, 192, 192);
}

.metric-header h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #6c757d;
    margin: 0;
}

.metric-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.metric-trend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.trend-indicator {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.trend-indicator.up {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.trend-indicator.down {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.trend-indicator.neutral {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
}

.trend-indicator i {
    font-size: 0.75rem;
}

.trend-label {
    font-size: 0.875rem;
    color: #6c757d;
}

.metric-chart, .metric-gauge, .metric-scale {
    height: 3rem;
    margin-top: auto;
}

.metric-detail {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.5rem;
}

.chart-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-top: 1rem;
}

.chart-header {
    padding: 1.5rem;
    border-bottom: 1px solid #dee2e6;
}

.chart-header h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #212529;
    margin: 0;
}

.chart-body {
    padding: 1.5rem;
    height: 400px;
}

.date-filter {
    background: white;
    padding: 1rem;
    border-radius: 0.75rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-control {
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
    padding: 0.5rem 1rem;
}

.btn-primary {
    border-radius: 0.5rem;
    padding: 0.5rem 1.5rem;
    background: rgb(75, 192, 192);
    border: none;
}

.btn-primary:hover {
    background: rgb(65, 182, 182);
}

@media (max-width: 768px) {
    .analytics-dashboard {
        padding: 0.5rem;
    }

    .metric-card {
        margin-bottom: 1rem;
    }

    .date-filter {
        margin-top: 1rem;
    }
}

/* Chart-specific styles */
.metric-gauge {
    position: relative;
    padding: 0 1rem;
    display: flex;
    align-items: center;
    height: 6rem;
    margin-top: auto;
    overflow: visible !important;
}

.metric-gauge canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.metric-scale {
    position: relative;
    height: 6rem;
    margin-top: auto;
}

.metric-chart {
    height: 6rem !important;
    margin-top: auto;
    position: relative;
    background: transparent;
    padding: 0;
}

.metric-chart canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
</style> 