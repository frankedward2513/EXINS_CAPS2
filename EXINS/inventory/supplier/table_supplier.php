<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Supplier Management</title>

    <link rel="stylesheet" href="../../styles/database.css">
</head>

<body>

    <h3>Supplier Database</h3>
    <table border="1">

        <tr>
            <th>ID</th>
            <th>Supplier Name</th>
            <th>Contact Person</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Address</th>
            <th>Action</th>
        </tr>

        <?php
        // Fetch Data from Database
        $sql = "SELECT * FROM supplier ORDER BY supplier_id DESC";
        $result = $conn->query($sql); // Fixed: Assigned output to $result
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Escaping output for security (XSS prevention)
                $id = htmlspecialchars($row['supplier_id']);
                $name = htmlspecialchars($row['supplier_name']);
                $contact = htmlspecialchars($row['contact_person']);
                $phone = htmlspecialchars($row['phone']);
                $email = htmlspecialchars($row['email']);
                $address = htmlspecialchars($row['address']);

                echo "<tr> 
                    <td>{$id}</td>
                    <td>{$name}</td>
                    <td>{$contact}</td>
                    <td>{$phone}</td>
                    <td>{$email}</td>
                    <td>{$address}</td>

                    <td>
                        <a href='edit_supplier.php?id={$id}'>Edit</a> |
                        <a href='delete_supplier.php?id={$id}' 
                        onclick='return confirm(\"Are you sure you want to delete this?\")'>Delete</a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr>
                <td colspan='7'>No Supplier Found.</td>
            </tr>";
        }

        $conn->close();
        ?>
    </table>
</body>

</html>