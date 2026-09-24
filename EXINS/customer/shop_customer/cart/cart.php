<?php
session_start();
include '../../../db_connection.php';

// Ensure customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../../authentication/customer_auth.php?tab=login");
    exit();
}

$customer_id =$_SESSION['customer_id'];

// Handle AJAX quantity change requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_update'])) {
    $cart_id = intval($_POST['cart_id']);
    $new_qty = intval($_POST['quantity']);

    // Check current stock limit from products table
    $stock_check =$conn->prepare("SELECT p.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.customer_id = ?");
    $stock_check->bind_param("ii", $cart_id,$customer_id);
    $stock_check->execute();$stock_res = $stock_check->get_result()->fetch_assoc();$stock_check->close();

    if (!$stock_res) {
        echo json_encode(['status' => 'error', 'message' => 'Item not found in cart.']);
        exit();
    }

    $max_stock = intval($stock_res['quantity']);

    if ($new_qty >$max_stock) {
        echo json_encode([
            'status' => 'max_stock', 
            'max' => $max_stock, 
            'message' => "Only {$max_stock} available for this product."
        ]);
        exit();
    }

    if ($new_qty > 0) {
        $update_stmt =$conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND customer_id = ?");
        $update_stmt->bind_param("iii", $new_qty, $cart_id,$customer_id);
        $update_stmt->execute();$update_stmt->close();
        echo json_encode(['status' => 'success']);
    } else {
        // Remove item if quantity drops to 0
        $del_stmt =$conn->prepare("DELETE FROM cart WHERE id = ? AND customer_id = ?");
        $del_stmt->bind_param("ii", $cart_id,$customer_id);
        $del_stmt->execute();$del_stmt->close();
        echo json_encode(['status' => 'removed']);
    }
    exit();
}

// Fetch cart items for this customer
$query = "SELECT c.id as cart_id, c.quantity as cart_qty, p.id as product_id, p.product_name, p.price, p.image_path, p.quantity as stock_qty 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.customer_id = ?";
$stmt =$conn->prepare($query);$stmt->bind_param("i", $customer_id);$stmt->execute();
$result =$stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shopping Cart</title>
    <link rel="stylesheet" href="../../../styles/shop.css">
    <style>
        .qty-btn {
            background: #e2e8f0;
            border: none;
            width: 28px;
            height: 28px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .qty-btn:hover { background: #cbd5e1; }
        .qty-display {
            display: inline-block;
            width: 30px;
            text-align: center;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div style="padding: 20px; max-width: 950px; margin: auto;">
        <a href="../shop_cust.php">&larr; Continue Shopping</a>
        <h2>Your Shopping Cart</h2>

        <?php if ($result->num_rows === 0): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <form action="checkout.php" method="POST" id="cart-form">
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                            <th style="padding: 10px;"><input type="checkbox" id="select-all" onclick="toggleSelectAll(this)"></th>
                            <th style="padding: 10px;">Product</th>
                            <th style="padding: 10px;">Price</th>
                            <th style="padding: 10px;">Quantity</th>
                            <th style="padding: 10px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()):$subtotal = $row['price'] *$row['cart_qty'];
                        ?>
                            <tr style="border-bottom: 1px solid #eee;" id="row-<?php echo $row['cart_id']; ?>">
                                <td style="padding: 10px;">
                                    <input type="checkbox" name="selected_items[]" value="<?php echo $row['cart_id']; ?>" 
                                           class="item-checkbox" 
                                           data-price="<?php echo $row['price']; ?>" 
                                           data-cart-id="<?php echo $row['cart_id']; ?>"
                                           onchange="calculateTotal()">
                                </td>
                                <td style="padding: 10px; display: flex; align-items: center; gap: 15px;">
                                    <img src="/EXINS/uploads/product/<?php echo htmlspecialchars($row['image_path']); ?>" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                                    <span><?php echo htmlspecialchars($row['product_name']); ?></span>
                                </td>
                                <td style="padding: 10px;">$<?php echo number_format($row['price'], 2); ?></td>
                                <td style="padding: 10px;">
                                    <!-- Clean Plus / Minus Controls -->
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <button type="button" class="qty-btn" onclick="changeQuantity(<?php echo $row['cart_id']; ?>, -1, <?php echo$row['stock_qty']; ?>)">-</button>
                                        <span id="qty-<?php echo $row['cart_id']; ?>" class="qty-display"><?php echo $row['cart_qty']; ?></span>
                                        <button type="button" class="qty-btn" onclick="changeQuantity(<?php echo $row['cart_id']; ?>, 1, <?php echo$row['stock_qty']; ?>)">+</button>
                                    </div>
                                </td>
                                <td style="padding: 10px;" id="subtotal-<?php echo $row['cart_id']; ?>" data-price="<?php echo $row['price']; ?>">
                                    $<?php echo number_format($subtotal, 2); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Summary Section -->
                <div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px; text-align: right;">
                    <p style="margin: 5px 0; font-size: 1.1em;">Total Items Selected: <span id="total-items">0</span></p>
                    <p style="margin: 5px 0; font-size: 1.3em; font-weight: bold; color: #2e7d32;">Total Amount: $<span id="total-amount">0.00</span></p>
                    
                    <button type="submit" id="checkout-btn" disabled 
                            style="margin-top: 10px; padding: 10px 25px; background: #ccc; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: not-allowed;">
                        Proceed to Checkout
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        function changeQuantity(cartId, change, maxStock) {
            let qtySpan = document.getElementById('qty-' + cartId);
            let currentQty = parseInt(qtySpan.innerText);
            let newQty = currentQty + change;

            if (newQty < 1) return; // Minimum limit is 1

            // Frontend check to prevent exceeding available stock immediately
            if (newQty > maxStock) {
                alert(`Only ${maxStock} left available for this product.`);
                return;
            }

            // Send background AJAX request to update database safely
            let formData = new FormData();
            formData.append('ajax_update', '1');
            formData.append('cart_id', cartId);
            formData.append('quantity', newQty);

            fetch('cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    qtySpan.innerText = newQty;
                    
                    // Update Subtotal UI instantly
                    let subtotalTd = document.getElementById('subtotal-' + cartId);
                    let price = parseFloat(subtotalTd.getAttribute('data-price'));
                    let newSubtotal = newQty * price;
                    subtotalTd.innerText = '$' + newSubtotal.toFixed(2);

                    // Recalculate totals
                    calculateTotal();
                } else if (data.status === 'max_stock') {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error updating quantity:', error));
        }

        function toggleSelectAll(source) {
            let checkboxes = document.getElementsByClassName('item-checkbox');
            for(let i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = source.checked;
            }
            calculateTotal();
        }

        function calculateTotal() {
            let checkboxes = document.getElementsByClassName('item-checkbox');
            let totalItems = 0;
            let totalAmount = 0;
            let checkedCount = 0;

            for (let i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    checkedCount++;
                    let cartId = checkboxes[i].getAttribute('data-cart-id');
                    let qty = parseInt(document.getElementById('qty-' + cartId).innerText) || 0;
                    let price = parseFloat(checkboxes[i].getAttribute('data-price'));

                    totalItems += qty;
                    totalAmount += (qty * price);
                }
            }

            document.getElementById('total-items').innerText = totalItems;
            document.getElementById('total-amount').innerText = totalAmount.toFixed(2);

            let checkoutBtn = document.getElementById('checkout-btn');
            if (checkedCount > 0) {
                checkoutBtn.disabled = false;
                checkoutBtn.style.background = '#2563eb';
                checkoutBtn.style.cursor = 'pointer';
            } else {
                checkoutBtn.disabled = true;
                checkoutBtn.style.background = '#ccc';
                checkoutBtn.style.cursor = 'not-allowed';
            }
        }
    </script>
</body>
</html>