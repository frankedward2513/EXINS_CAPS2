<?php

include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/ai/gemini.php';


// Get category and aggregated inventory information with names
$sql = "
    SELECT 
        c.ID AS category_id,
        c.CATEGORY_NAME AS category_name,
        COUNT(p.id) AS total_products,
        SUM(p.quantity) AS total_quantity
    FROM product_category c
    LEFT JOIN products p ON c.ID = p.category_id
    GROUP BY c.ID, c.CATEGORY_NAME
    ORDER BY total_quantity ASC
";

$result = $conn->query($sql);

if (!$result) {
    die("Database query failed: " . $conn->error);
}

// Store category data
$categories = [];

while ($row = $result->fetch_assoc()) {
    $categories[] = [
        "category_id" => (int) $row["category_id"],
        "category_name" => $row["category_name"],
        "total_products" => (int) $row["total_products"],
        "total_quantity" => (int) $row["total_quantity"]
    ];
}

// Convert data to JSON
$categoryData = json_encode(
    $categories,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
);

// Gemini prompt
$prompt = <<<PROMPT

You are an AI inventory analyst for an ukay-ukay retail management system.

Analyze the category-level inventory data provided below. Use the actual category names (e.g., Dresses, Jackets, Pants) in your analysis rather than referencing them by ID numbers.

CATEGORY INVENTORY DATA:

$categoryData

Your analysis must include:

1. Overall Inventory Status
   - Give a short summary of the current stock status across all ukay-ukay categories.

2. Low Stock Categories
   - Identify categories with the lowest total quantities or product varieties using their actual category names.
   - Explain which categories need attention first.

3. High Stock Categories
   - Identify categories with relatively high stock levels or product counts using their actual names.

4. Stock Distribution
   - Analyze how evenly distributed the stock is across different categories.

5. Restocking Recommendations
   - Recommend which categories may need restocking or new bale drops using their real names.
   - Prioritize categories with low total quantities.

6. Inventory Observations
   - Identify unusual or important patterns among the categories.

7. Business Recommendations
   - Give practical recommendations for the store owner regarding category assortment.

IMPORTANT RULES:

- Do not invent information.
- Only use information present in the category inventory data.
- Refer to categories by their **category_name** (e.g., "Dresses"), not by their ID number.
- Do not claim that an item style is selling quickly because sales data is not included.
- Do not calculate profit or revenue because financial information is not included.
- Use simple language.
- Use headings and bullet points.
- Focus on useful recommendations for the business owner.

PROMPT;


// Ask Gemini
$response = askGemini($prompt);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Category Inventory Analysis</title>
    <link rel="stylesheet" href="../styles/ai.css">
</head>

<body>

    <div class="container">
        <div class="card">
            <h1>AI Category Inventory Analysis</h1>

            <?php if ($response["success"]): ?>
                <div class="analysis">
                    <?= htmlspecialchars($response["analysis"]) ?>
                </div>
            <?php else: ?>
                <div class="error">
                    <strong>Gemini Error:</strong><br>
                    <?= htmlspecialchars($response["error"]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>