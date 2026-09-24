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
    <title>Product Categories</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="../../styles/form.css">
</head>

<body>

    <form action="process_product_category.php" method="POST">

        <!-- Form Title -->
        <h1>Product Category</h1>

        <!-- Product Category -->
        <label for="product_category">Product Category Name: </label>
        <input type="text" id="product_category" name="product_category" required>

        <!-- Submit / Cancel -->
        <div id="submitorcancel">
            <input type="submit" value="SUBMIT" id="submit">
            <input type="reset" value="CANCEL" id="reset">
        </div>

    </form>

    <hr>

    <!-- Database -->
    <?php
    include 'table_product_category.php';
    ?>

</body>

</html>