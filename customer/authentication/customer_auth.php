<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
// Go up two levels to access root db_connection.php
include '../../db_connection.php';

$error = '';
$success = '';
$active_tab = $_GET['tab'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';
    $active_tab = $action;
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } else {
        if ($action === 'signup') {
            $fullname = trim($_POST['fullname'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if (empty($fullname) || empty($phone) || empty($address)) {
                $error = "Please fill in all fields for registration.";
            } else {
                $check = $conn->prepare("SELECT id FROM customers WHERE email = ?");
                $check->bind_param("s", $email);
                $check->execute();
                $check->store_result();

                if ($check->num_rows > 0) {
                    $error = "An account with this email already exists.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO customers (fullname, email, phone, address, password) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $fullname, $email, $phone, $address, $hashed_password);

                    if ($stmt->execute()) {
                        $success = "Account created successfully! You can now log in.";
                        $active_tab = 'login';
                    } else {
                        $error = "Error creating account: " . $stmt->error;
                    }
                    $stmt->close();
                }
                $check->close();
            }
        } elseif ($action === 'login') {
            $stmt = $conn->prepare("SELECT id, fullname, password FROM customers WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($customer_id, $fullname, $hashed_password);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    $_SESSION['customer_id'] = $customer_id;
                    $_SESSION['customer_name'] = $fullname;
                    $_SESSION['customer_email'] = $email;

                    echo "<script>window.top.location.href = '/EXINS/system_customer.php';</script>";
                    exit();
                } else {
                    $error = "Incorrect password.";
                }
            } else {
                $error = "No customer account found with that email.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Authentication - EXINS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-slate-800 border border-slate-700 rounded-xl shadow-2xl p-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-red-500">EXINS Customer Portal</h1>
            <p class="text-xs text-slate-400 mt-1">Sign in to shop or create your customer account</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-4 p-3 bg-rose-500/20 border border-rose-500/30 text-rose-400 text-xs rounded-lg text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div
                class="mb-4 p-3 bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 text-xs rounded-lg text-center">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div class="flex bg-slate-900 p-1 rounded-lg mb-6 border border-slate-700">
            <button type="button" id="tab-login" onclick="switchTab('login')"
                class="flex-1 py-2 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white">Log
                In</button>
            <button type="button" id="tab-signup" onclick="switchTab('signup')"
                class="flex-1 py-2 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white">Sign
                Up</button>
        </div>

        <form method="POST" id="authForm" class="space-y-4">
            <input type="hidden" name="action" id="formAction" value="<?= htmlspecialchars($active_tab) ?>">

            <div id="signup-fields" style="display: none;" class="space-y-4">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-400 mb-1">Full Name</label>
                    <input type="text" name="fullname" id="fullnameInput"
                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-red-500 transition">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-400 mb-1">Phone Number</label>
                    <input type="text" name="phone" id="phoneInput"
                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-red-500 transition">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-400 mb-1">Delivery Address</label>
                    <textarea name="address" id="addressInput" rows="2"
                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-red-500 transition"></textarea>
                </div>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 mb-1">Email Address</label>
                <input type="email" name="email" required
                    class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-red-500 transition">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-red-500 transition">
            </div>

            <button type="submit" id="submitBtn"
                class="w-full bg-red-600 hover:bg-red-500 text-white font-semibold py-2.5 rounded-lg text-sm transition shadow-lg mt-2">
                Log In
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="../../Shop/shop.php" target="contentFrame"
                class="text-xs text-slate-400 hover:text-red-400 transition">&larr; Return to Shop</a>
        </div>
    </div>

    <script>
        function switchTab(mode) {
            const tabLogin = document.getElementById('tab-login');
            const tabSignup = document.getElementById('tab-signup');
            const formAction = document.getElementById('formAction');
            const submitBtn = document.getElementById('submitBtn');
            const signupFields = document.getElementById('signup-fields');

            const fullnameInput = document.getElementById('fullnameInput');
            const phoneInput = document.getElementById('phoneInput');
            const addressInput = document.getElementById('addressInput');

            if (mode === 'login') {
                tabLogin.className = "flex-1 py-2 text-xs font-semibold rounded-md transition bg-red-600 text-white shadow";
                tabSignup.className = "flex-1 py-2 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white";
                formAction.value = "login";
                submitBtn.innerText = "Log In";
                signupFields.style.display = "none";

                fullnameInput.removeAttribute('required');
                phoneInput.removeAttribute('required');
                addressInput.removeAttribute('required');
            } else {
                tabSignup.className = "flex-1 py-2 text-xs font-semibold rounded-md transition bg-red-600 text-white shadow";
                tabLogin.className = "flex-1 py-2 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white";
                formAction.value = "signup";
                submitBtn.innerText = "Create Account";
                signupFields.style.display = "block";

                fullnameInput.setAttribute('required', 'true');
                phoneInput.setAttribute('required', 'true');
                addressInput.setAttribute('required', 'true');
            }
        }

        switchTab('<?= $active_tab ?>');
    </script>
</body>

</html>