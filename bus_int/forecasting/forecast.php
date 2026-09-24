<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection
$conn_path = $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';
if (file_exists($conn_path)) {
    include $conn_path;
} else {
    die("Error: Database connection file not found at: " . $conn_path);
}

// Define the 7-day window ending today
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-6 days'));

// Generate the full 7-day array (ensuring 0s are shown for missing days)
$periodDates = [];
for ($i = 0; $i < 7; $i++) {
    $periodDates[] = date('Y-m-d', strtotime("$start_date +$i days"));
}

// 1. Fetch sales grouped by Category and Transaction Date for the last 7 days
$query = "SELECT 
            COALESCE(c.ID, 0) AS category_id,
            COALESCE(c.CATEGORY_NAME, 'Uncategorized') AS CATEGORY_NAME,
            DATE(t.transaction_date) AS sale_date, 
            SUM(t.amount) AS total_sales 
          FROM transactions t
          LEFT JOIN products p ON t.product_id = p.id
          LEFT JOIN product_category c ON p.category_id = c.ID
          WHERE LOWER(t.transaction_type) = 'sold'
            AND DATE(t.transaction_date) >= '$start_date'
            AND DATE(t.transaction_date) <= '$end_date'
          GROUP BY category_id, CATEGORY_NAME, DATE(t.transaction_date)
          ORDER BY CATEGORY_NAME ASC, sale_date ASC";

$result = mysqli_query($conn, $query);

$rawSalesData = [];
$categoryNames = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $catId = $row['category_id'];
        $catName = $row['CATEGORY_NAME'];
        $date = $row['sale_date'];

        $categoryNames[$catId] = $catName;
        $rawSalesData[$catId][$date] = (float) $row['total_sales'];
    }
}

// Fallback: If no transactions exist, grab all categories so cards still render with 0 sales
if (empty($categoryNames)) {
    $catQuery = "SELECT ID, CATEGORY_NAME FROM product_category";
    $catResult = mysqli_query($conn, $catQuery);
    if ($catResult) {
        while ($catRow = mysqli_fetch_assoc($catResult)) {
            $categoryNames[$catRow['ID']] = $catRow['CATEGORY_NAME'];
        }
    }
    if (empty($categoryNames)) {
        $categoryNames[0] = 'Uncategorized';
    }
}

// Build structured data ensuring all 7 days are represented per category (filling 0 if missing)
$categoriesData = [];
foreach ($categoryNames as $catId => $catName) {
    $categoriesData[$catId] = [
        'name' => $catName,
        'dates' => $periodDates,
        'sales' => []
    ];

    foreach ($periodDates as $date) {
        $categoriesData[$catId]['sales'][] = isset($rawSalesData[$catId][$date]) ? $rawSalesData[$catId][$date] : 0.0;
    }
}

// 2. Process EWMA and Predictions for each category
$alpha = 0.3;
$processedCategories = [];

foreach ($categoriesData as $catId => $data) {
    $dates = $data['dates'];
    $actualSales = $data['sales'];

    $ewmaSales = [];
    $forecast = 0;

    foreach ($actualSales as $index => $sale) {
        if ($index == 0) {
            $forecast = $sale;
        } else {
            $forecast = ($alpha * $sale) + ((1 - $alpha) * $forecast);
        }
        $ewmaSales[] = round($forecast, 2);
    }

    $labels = $dates;
    $currentDataset = $actualSales;
    $predictedDataset = array_fill(0, count($actualSales), null);

    if (!empty($actualSales)) {
        $lastActual = end($actualSales);
        $lastForecast = end($ewmaSales);
        $nextForecast = round(($alpha * $lastActual) + ((1 - $alpha) * $lastForecast), 2);

        $lastDate = end($dates);
        $nextDate = date('Y-m-d', strtotime($lastDate . ' + 1 day'));

        $labels[] = $nextDate . " (Predicted)";
        $currentDataset[] = null;

        $predictedDataset[] = null;
        array_pop($predictedDataset);
        $predictedDataset[count($actualSales) - 1] = $lastActual;
        $predictedDataset[] = $nextForecast;
    }

    $processedCategories[$catId] = [
        'name' => $data['name'],
        'chartData' => [
            'labels' => $labels,
            'currentSales' => $currentDataset,
            'predictedSales' => $predictedDataset
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sales Forecasting by Category - EWMA</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../styles/ewma.css">
</head>

<body>

    <h1>Sales Forecasting</h1>

    <?php if (empty($processedCategories)): ?>
        <div class="card">
            <div class="no-data">
                <p>No product categories found.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($processedCategories as $catId => $catInfo): ?>
            <div class="card">
                <h2>Category: <?php echo htmlspecialchars($catInfo['name']); ?></h2>
                <div class="chart-container">
                    <canvas id="chart_<?php echo $catId; ?>"></canvas>
                </div>
            </div>

            <script>
                (function () {
                    const rawData = <?php echo json_encode($catInfo['chartData']); ?>;
                    const ctx = document.getElementById('chart_<?php echo $catId; ?>').getContext('2d');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: rawData.labels,
                            datasets: [
                                {
                                    label: 'Current Sales (Actual)',
                                    data: rawData.currentSales,
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderWidth: 2,
                                    tension: 0.1,
                                    fill: false
                                },
                                {
                                    label: 'Predicted Sales (EWMA Model)',
                                    data: rawData.predictedSales,
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderWidth: 2,
                                    borderDash: [5, 5],
                                    tension: 0.3,
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: { display: true, text: 'Sales Amount' }
                                },
                                x: {
                                    title: { display: true, text: 'Timeline (Last 7 Days + Next Day Prediction)' }
                                }
                            }
                        }
                    });
                })();
            </script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>

</html>