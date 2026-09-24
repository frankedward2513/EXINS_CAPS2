<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- External Styles -->
    <link rel="stylesheet" href="../../styles/form.css">
</head>

<body>
    <form action="process_bale.php" method="POST">

        <!-- Form Title -->
        <h1>Bale Management</h1>

        <!-- Bale Name -->
        <label for="bale_name">Bale Name</label><br>
        <input type="text" id="bale_name" name="bale_name" required><br><br>

        <!-- Quantity -->
        <label for="b_quantity">Quantity</label><br>
        <input type="number" id="b_quantity" name="b_quantity" min="1" required><br><br>

        <!-- Total Bale Price -->
        <label for="b_price">Total Bale Price</label><br>
        <input type="number" id="b_price" name="b_price" step="0.01" min="0" required><br><br>

        <!-- Sumbit / Cancel -->
        <div id="submitorcancel">
            <input type="submit" value="SUBMIT" id="submit">
            <input type="reset" value="CANCEL" id="reset">
        </div>

        <a href="management_bale.php">Go Back</a>

    </form>
</body>

</html>