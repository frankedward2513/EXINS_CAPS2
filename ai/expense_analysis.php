<?php

include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/ai/gemini.php';


// Get expense data with category names
$sql = "
    SELECT
        e.id,
        e.category_id,
        e.expense_date,
        e.amount,
        e.description,
        e.created_at,
        ec.CATEGORY_NAME
    FROM expenses e
    LEFT JOIN expense_category ec
        ON e.category_id = ec.ID
    ORDER BY e.expense_date DESC
";

$result = $conn->query($sql);

if (!$result) {
    die("Database query failed: " . $conn->error);
}


// Store expenses
$expenses = [];

while ($row = $result->fetch_assoc()) {

    $expenses[] = [
        "id" => (int) $row["id"],
        "category" => $row["CATEGORY_NAME"] ?? "Unknown Category",
        "expense_date" => $row["expense_date"],
        "amount" => (float) $row["amount"],
        "description" => $row["description"] ?? "",
        "created_at" => $row["created_at"]
    ];
}


// Convert expense data to JSON
$expenseData = json_encode(
    $expenses,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
);

// Gemini prompt
$prompt = <<<PROMPT
You are an AI financial analyst for a retail inventory management system.
Analyze the following expense data from the business database.

EXPENSE DATA:

$expenseData

Provide a clear and practical expense analysis.

Include:

1. Overall Expense Summary
   - Calculate and state the total recorded expenses.
   - Give a short summary of the current expenses.

2. Highest Expense Categories
   - Identify categories with the highest spending.
   - Rank major expense categories from highest to lowest.

3. Lowest Expense Categories
   - Identify categories with relatively low spending.

4. Expense Patterns
   - Look for patterns based on dates and amounts.
   - Identify periods where expenses appear higher.

5. Significant Expenses
   - Identify unusually high individual expenses.
   - Explain why they are significant based only on the available data.

6. Cost Control Recommendations
   - Suggest practical ways the business owner could monitor and control expenses.

7. Business Recommendations
   - Give useful recommendations based only on the provided expense data.

IMPORTANT RULES:

- Do not invent information.
- Only use the provided expense data.
- Do not assume an expense is unnecessary without evidence.
- Do not calculate sales or profit because this dataset contains expenses only.
- Use the actual category names provided.
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
    <title>AI Expense Analysis</title>
    <link rel="stylesheet" href="../styles/ai.css">
</head>

<body>

    <div class="container">
        <div class="card">
            <h1>AI Expense Analysis</h1>
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