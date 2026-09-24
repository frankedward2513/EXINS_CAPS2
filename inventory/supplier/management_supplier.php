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
    <title>Supplier Management</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="../../styles/form.css">
</head>

<body>

    <form action="process_supplier.php" method="POST">

        <!-- Form Title -->
        <p>Supplier Management Form</p>

        <!-- Supplier Name -->
        <label for="supplier_name">Supplier Name:</label><br>
        <input type="text" id="supplier_name" name="supplier_name"><br>

        <!-- Contact Person -->
        <label for="contact_person">Contact Person:</label><br>
        <input type="text" id="contact_person" name="contact_person"><br>

        <!-- Phone -->
        <label for="phone">Phone:</label><br>
        <input type="number" id="phone" name="phone"><br>

        <!-- Email -->
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email"><br>

        <!-- Address -->
        <label for="address">Address:</label><br>
        <input type="text" id="address" name="address"><br>

        <!-- Submit / Cancel -->
        <div id="submitorcancel">
            <input type="submit" value="SUBMIT" id="submit">
            <input type="reset" value="CANCEL" id="reset">
        </div>

    </form>

    <hr>

    <!-- Database -->
    <?php
    include 'table_supplier.php';
    ?>

</body>

</html>