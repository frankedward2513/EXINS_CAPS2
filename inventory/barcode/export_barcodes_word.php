<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Check if any barcodes were selected
$selected_ids = isset($_POST['selected_barcodes']) ? $_POST['selected_barcodes'] : [];

if (empty($selected_ids)) {
    echo "<script>alert('Please select at least one barcode to export!'); window.close();</script>";
    exit;
}

// Force browser to download as a Word document (.doc)
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment; Filename=Selected_Product_Barcodes_" . date('Y-m-d') . ".doc");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Printable Barcodes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 12px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
            font-size: 16px;
        }

        .product-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">EXINS - Selected Product Barcodes</h2>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Barcode Image</th>
        </tr>
        <?php
        // Sanitize and format IDs for SQL IN clause safely
        $sanitized_ids = array_map('intval', $selected_ids);
        $ids_string = implode(',', $sanitized_ids);

        // Fetch ONLY the selected barcodes
        $sql = "SELECT b.ProductID, p.product_name 
                FROM barcodes b 
                JOIN products p ON b.ProductID = p.id 
                WHERE b.BarcodeID IN ($ids_string) 
                ORDER BY b.BarcodeID DESC";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $product_name = htmlspecialchars($row['product_name']);
                $product_id = $row['ProductID'];

                $barcode_value = "EXINS-" . str_pad($product_id, 4, '0', STR_PAD_LEFT);
                $barcode_img_url = "https://barcode.tec-it.com/barcode.ashx?data=" . urlencode($barcode_value) . "&code=Code128&dpi=96&translate-esc=true";

                echo "<tr>
                    <td class='product-name'>{$product_name}</td>
                    <td>
                        <img src='{$barcode_img_url}' alt='{$barcode_value}' style='width: 200px; height: 60px;'><br>
                        <span style='font-size: 12px; color: #666;'>{$barcode_value}</span>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No selected barcodes found.</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</body>

</html>