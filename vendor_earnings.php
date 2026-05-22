<?php
session_start();
if (!isset($_SESSION['vendor_id'])) {
    header('Location: vendorLogin.php');
    exit();
}
require_once 'config.php';

$vendorId = $_SESSION['vendor_id'];
$vendorName = $_SESSION['vendor_name'];

// Monthly earnings breakdown (list)
$monthlyStmt = $conn->prepare("
    SELECT 
        DATE_FORMAT(o.created_at, '%Y-%m') as month,
        SUM(toi.total_price) as earnings,
        COUNT(DISTINCT toi.order_id) as order_count
    FROM tool_order_items toi
    JOIN tool_orders o ON toi.order_id = o.id
    WHERE toi.vendor_id = ? AND o.order_status NOT IN ('cancelled')
    GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
");
$monthlyStmt->bind_param("i", $vendorId);
$monthlyStmt->execute();
$monthlyEarnings = $monthlyStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$monthlyStmt->close();

// Total earnings (lifetime)
$totalStmt = $conn->prepare("
    SELECT SUM(toi.total_price) as total_earnings
    FROM tool_order_items toi
    JOIN tool_orders o ON toi.order_id = o.id
    WHERE toi.vendor_id = ? AND o.order_status NOT IN ('cancelled')
");
$totalStmt->bind_param("i", $vendorId);
$totalStmt->execute();
$totalEarnings = $totalStmt->get_result()->fetch_assoc()['total_earnings'] ?? 0;
$totalStmt->close();

// Top selling tools (limit 5)
$topToolsStmt = $conn->prepare("
    SELECT tp.product_name, SUM(toi.quantity) as total_quantity, SUM(toi.total_price) as total_revenue
    FROM tool_order_items toi
    JOIN tool_products tp ON toi.tool_id = tp.id
    WHERE toi.vendor_id = ?
    GROUP BY toi.tool_id
    ORDER BY total_revenue DESC
    LIMIT 5
");
$topToolsStmt->bind_param("i", $vendorId);
$topToolsStmt->execute();
$topTools = $topToolsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$topToolsStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Earnings Report — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            max-width: 100%;
            height: 220px;
            margin: 0 auto;
        }
        canvas {
            max-height: 220px !important;
            width: 100% !important;
        }
    </style>
</head>
<body class="bg-[#f2f5f0] min-h-screen">

    <?php include('vendorNav.php'); ?>

    <main class="max-w-4xl mx-auto px-5 pt-24 pb-16">
        <div class="mb-8">
            <a href="vendorDash.php" class="inline-flex items-center gap-2 text-amber-600 text-sm font-semibold hover:text-amber-800 transition mb-4">
                <i class="fa-solid fa-arrow-left text-xs"></i> Back to Dashboard
            </a>
            <h1 class="text-2xl font-bold text-green-950">📊 Earnings Report</h1>
            <p class="text-stone-400 text-sm mt-1">Track your sales, revenue and performance</p>
        </div>

        <!-- Total Earnings Card (amber gradient) -->
        <div class="bg-gradient-to-r from-amber-700 to-amber-600 rounded-2xl p-6 text-white mb-8">
            <p class="text-sm opacity-90">Total Lifetime Earnings</p>
            <p class="text-4xl font-bold">৳<?php echo number_format($totalEarnings, 0); ?></p>
        </div>

        <!-- Sales Graphs Row -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <!-- Daily Sales Card -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold text-green-950 text-base">📈 Last 7 Days Sales</h2>
                    <i class="fa-solid fa-chart-line text-amber-600"></i>
                </div>
                <div class="chart-container">
                    <canvas id="dailySalesChart" width="400" height="200"></canvas>
                </div>
                <div id="dailyError" class="text-center text-xs text-red-500 hidden mt-2"></div>
                <p class="text-center text-xs text-stone-400 mt-2">Daily revenue in BDT</p>
            </div>

            <!-- Monthly Sales Card -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold text-green-950 text-base">📊 Last 6 Months Income</h2>
                    <i class="fa-solid fa-chart-simple text-amber-600"></i>
                </div>
                <div class="chart-container">
                    <canvas id="monthlySalesChart" width="400" height="200"></canvas>
                </div>
                <div id="monthlyError" class="text-center text-xs text-red-500 hidden mt-2"></div>
                <p class="text-center text-xs text-stone-400 mt-2">Monthly revenue in BDT</p>
            </div>
        </div>

        <!-- Monthly Earnings List -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 mb-8">
            <h2 class="font-bold text-green-950 mb-4">📆 Monthly Breakdown</h2>
            <?php if (count($monthlyEarnings) > 0): ?>
                <div class="space-y-3">
                    <?php foreach ($monthlyEarnings as $month): ?>
                    <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                        <div>
                            <p class="font-semibold text-stone-800">
                                <?php echo date('F Y', strtotime($month['month'] . '-01')); ?>
                            </p>
                            <p class="text-xs text-stone-400"><?php echo $month['order_count']; ?> order(s)</p>
                        </div>
                        <p class="font-bold text-amber-700">৳<?php echo number_format($month['earnings'], 0); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-stone-400 text-center py-8">No earnings yet</p>
            <?php endif; ?>
        </div>

        <!-- Top Selling Tools -->
        <?php if (count($topTools) > 0): ?>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
            <h2 class="font-bold text-green-950 mb-4">🏆 Top Selling Tools</h2>
            <div class="space-y-3">
                <?php foreach ($topTools as $tool): ?>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-stone-800"><?php echo htmlspecialchars($tool['product_name']); ?></p>
                        <p class="text-xs text-stone-400">Sold: <?php echo $tool['total_quantity']; ?> units</p>
                    </div>
                    <p class="font-bold text-amber-700">৳<?php echo number_format($tool['total_revenue'], 0); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <?php include('footer.php'); ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('vendor_sales_data.php')
                .then(response => {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Sales data received:', data);
                    
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    
                    // Daily chart
                    if (data.daily && data.daily.length > 0) {
                        const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
                        new Chart(dailyCtx, {
                            type: 'line',
                            data: {
                                labels: data.daily.map(item => item.date),
                                datasets: [{
                                    label: 'Sales (BDT)',
                                    data: data.daily.map(item => parseFloat(item.total) || 0),
                                    borderColor: '#d97706',
                                    backgroundColor: 'rgba(217, 119, 6, 0.1)',
                                    fill: true,
                                    tension: 0.3,
                                    pointRadius: 3,
                                    pointBackgroundColor: '#b45309'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: { position: 'top', labels: { boxWidth: 12, font: { size: 10 } } },
                                    tooltip: { callbacks: { label: (ctx) => `৳${ctx.raw.toFixed(2)}` } }
                                },
                                scales: {
                                    y: { beginAtZero: true, title: { display: true, text: 'BDT', font: { size: 10 } } },
                                    x: { ticks: { font: { size: 9 } } }
                                }
                            }
                        });
                    } else {
                        document.getElementById('dailyError').innerText = 'No daily sales data available.';
                        document.getElementById('dailyError').classList.remove('hidden');
                    }
                    
                    // Monthly chart
                    if (data.monthly && data.monthly.length > 0) {
                        const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
                        new Chart(monthlyCtx, {
                            type: 'bar',
                            data: {
                                labels: data.monthly.map(item => item.month),
                                datasets: [{
                                    label: 'Income (BDT)',
                                    data: data.monthly.map(item => parseFloat(item.total) || 0),
                                    backgroundColor: '#f59e0b',
                                    borderRadius: 6,
                                    barPercentage: 0.7
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: { position: 'top', labels: { boxWidth: 12, font: { size: 10 } } },
                                    tooltip: { callbacks: { label: (ctx) => `৳${ctx.raw.toFixed(2)}` } }
                                },
                                scales: {
                                    y: { beginAtZero: true, title: { display: true, text: 'BDT', font: { size: 10 } } },
                                    x: { ticks: { font: { size: 9 } } }
                                }
                            }
                        });
                    } else {
                        document.getElementById('monthlyError').innerText = 'No monthly sales data available.';
                        document.getElementById('monthlyError').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading sales data:', error);
                    document.getElementById('dailyError').innerText = 'Failed to load sales data: ' + error.message;
                    document.getElementById('dailyError').classList.remove('hidden');
                    document.getElementById('monthlyError').innerText = 'Please check that vendor_sales_data.php exists and is accessible.';
                    document.getElementById('monthlyError').classList.remove('hidden');
                });
        });
    </script>
</body>
</html>