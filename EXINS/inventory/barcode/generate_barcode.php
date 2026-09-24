<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

$success_message = "";
$error_message = "";

// 1. HANDLE BULK AUTO-GENERATION OF MISSING BARCODES
if (isset($_POST['auto_generate_all'])) {
    // Find all products that do NOT have an entry in the barcodes table yet
    $missing_query = "SELECT id FROM products WHERE id NOT IN (SELECT ProductID FROM barcodes)";
    $missing_result = $conn->query($missing_query);

    if ($missing_result && $missing_result->num_rows > 0) {
        $count = 0;
        while ($prod = $missing_result->fetch_assoc()) {
            $product_id = $prod['id'];
            // Format barcode value (e.g., EXINS-0005)
            $barcode_text = "EXINS-" . str_pad($product_id, 4, '0', STR_PAD_LEFT);

            // Placeholder SVG structure rendered dynamically via JsBarcode in the table
            $default_svg = '<svg class="auto-barcode" data-value="' . $barcode_text . '"></svg>';

            $insert_sql = "INSERT INTO barcodes (ProductID, BarcodeImage) VALUES ('$product_id', '$default_svg')";
            if ($conn->query($insert_sql) === TRUE) {
                $count++;
            }
        }
        $success_message = "Successfully auto-generated barcodes for {$count} products!";
    } else {
        $error_message = "All products already have barcodes!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Product Barcodes</title>
    <link rel="stylesheet" href="../../styles/form.css">
    <!-- JsBarcode CDN -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
</head>

<body>

    <!-- BULK AUTOMATION FORM -->
    <form method="POST" id="barcodeForm" style="margin-bottom: 20px;">
        <h1>Product Barcode Management</h1>

        <?php if (!empty($success_message))
            echo "<p style='color: green;'>{$success_message}</p>"; ?>
        <?php if (!empty($error_message))
            echo "<p style='color: red;'>{$error_message}</p>"; ?>

        <div style="background: #eef2f7; padding: 20px; border-radius: 6px; margin-bottom: 20px; text-align: center;">
            <h3>Automatic Bulk Barcode Generation</h3>
            <p style="font-size: 14px; color: #555; margin-bottom: 15px;">Click below to instantly create and assign
                barcodes for all existing products that don't have one yet.</p>
            <button type="submit" name="auto_generate_all"
                style="background: #28a745; color: white; padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 15px;">
                ⚡ Auto-Generate All Missing Barcodes
            </button>
        </div>

        <div style="text-align: left;">
            <a href="management_product.php" style="text-decoration: none; color: #007bff;">← Back to Product
                Management</a>
        </div>
    </form>

    <hr style="margin: 20px 0;">

    <!-- Database Table Display (Includes its own form for selecting checkboxes and exporting to Word) -->
    <?php
    include 'barcode_table.php';
    ?>

    <script>
        // Automatically render any dynamic placeholder SVGs loaded in the table below
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".auto-barcode").forEach(function (svgElem) {
                const val = svgElem.getAttribute("data-value");
                if (val) {
                    try {
                        JsBarcode(svgElem, val, {
                            format: "CODE128",
                            lineColor: "#000",
                            width: 1.5,
                            height: 40,
                            displayValue: true
                        });
                    } catch (err) {
                        console.log(err);
                    }
                }
            });
        });
    </script>

</body>

</html>