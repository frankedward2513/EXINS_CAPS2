<?php
// Database Connection (if not already included)
if (!isset($conn)) {
    include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';
}
?>

<!-- External Styles -->
<link rel="stylesheet" href="../../styles/database.css">
<h3>Barcode Database</h3>

<!-- Form that submits selected barcodes to the Word exporter -->
<form action="export_barcodes_word.php" method="POST" target="_blank">

    <div style="margin-bottom: 12px; display: flex; gap: 10px; align-items: center;">
        <button type="submit"
            style="background: #007bff; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            📄 Download Selected as Word File
        </button>
        <span style="font-size: 13px; color: #666;">(Check the boxes for the items you want to print)</span>
    </div>

    <table border="1">
        <tr>
            <th style="width: 40px; text-align: center;">
                <input type="checkbox" id="selectAll">
            </th>
            <th>Barcode ID</th>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Barcode Image Preview</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>

        <?php
        $sql = "SELECT b.BarcodeID, b.ProductID, b.BarcodeImage, b.CreatedAt, p.product_name 
                FROM barcodes b 
                JOIN products p ON b.ProductID = p.id 
                ORDER BY b.BarcodeID DESC";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $barcode_id = htmlspecialchars($row['BarcodeID']);
                $product_id = htmlspecialchars($row['ProductID']);
                $product_name = htmlspecialchars($row['product_name']);
                $barcode_image = $row['BarcodeImage'];
                $created_at = htmlspecialchars($row['CreatedAt']);

                echo "<tr> 
                    <td style='text-align: center;'>
                        <input type='checkbox' name='selected_barcodes[]' value='{$barcode_id}' class='barcodeCheckbox'>
                    </td>
                    <td>{$barcode_id}</td>
                    <td>{$product_id}</td>
                    <td>{$product_name}</td>
                    <td style='text-align: center; padding: 10px;'>{$barcode_image}</td>
                    <td>{$created_at}</td>
                    <td>
                        <a href='delete_barcode.php?id={$barcode_id}' 
                        onclick='return confirm(\"Are you sure you want to delete this barcode?\")'>Delete</a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr>
                <td colspan='7'>No Barcodes Found.</td>
            </tr>";
        }
        ?>
    </table>
</form>

<!-- JavaScript for Select All Checkboxes -->
<script>
    document.getElementById('selectAll').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.barcodeCheckbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>