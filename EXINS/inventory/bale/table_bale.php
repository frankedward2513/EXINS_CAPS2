<!-- Database Connection -->
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Title Page -->
    <title>Bale Management</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="../../styles/database.css">
</head>

<body>
    <!-- Header Section with Title and Button aligned side-by-side at the top right -->
    <div class="table-header-container">
        <h3>Bale Database</h3>
        <a href="form_bale.php" style="text-decoration: none;">
            <button type="button" class="glass-btn">
                + Create New Bale
            </button>
        </a>
    </div>

    <table>
        <!-- Table Headers -->
        <tr>
            <th>ID</th>
            <th>Bale Name</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Price Per Piece</th>
            <th>Action</th>
        </tr>

        <?php
        // Fetch Data in Database
        $sql = "SELECT * FROM bales ORDER BY id DESC";

        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['bale_name']}</td>
                    <td>{$row['b_quantity']}</td>
                    <td>₱" . number_format($row['b_price'], 2) . "</td>
                    <td>₱" . number_format($row['price_per_piece'], 2) . "</td>
                    <td>
                        <a href='edit_bale.php?id={$row['id']}'>Edit</a> |
                        <a href='delete_bale.php?id={$row['id']}' 
                        onclick='return confirm(\"Are you sure you want to delete this?\")'>Delete</a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr>
                <td colspan='6'>No Bales Found.</td>
            </tr>";
        }
        $conn->close();
        ?>
    </table>
</body>

</html>