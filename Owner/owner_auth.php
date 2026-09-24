<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

$error = '';
$success = '';
$active_tab = 'login'; // Track active tab

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';
    $active_tab = $action; // Keep user on the same tab they submitted
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } else {
        if ($action === 'signup') {
            // Check if email already exists
            $check = $conn->prepare("SELECT id FROM owners WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = "An owner account with this email already exists.";
            } else {
                // Securely hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO owners (email, password) VALUES (?, ?)");
                $stmt->bind_param("ss", $email, $hashed_password);

                if ($stmt->execute()) {
                    $success = "Account created successfully! You can now log in.";
                    $active_tab = 'login'; // Switch back to login on success
                } else {
                    $error = "Error creating account: " . $stmt->error;
                }
                $stmt->close();
            }
            $check->close();

        } elseif ($action === 'login') {
            // Verify login credentials
            $stmt = $conn->prepare("SELECT id, password FROM owners WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($owner_id, $hashed_password);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    $_SESSION['owner_id'] = $owner_id;
                    $_SESSION['owner_email'] = $email;

                    // Redirect to system.php and break out of the iframe
                    echo "<script>window.top.location.href = '../system.php';</script>";
                    exit();
                } else {
                    $error = "Incorrect password.";
                }
            } else {
                $error = "No owner account found with that email.";
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
    <title>Owner Authentication - EXINS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full bg-slate-800 border border-slate-700 rounded-xl shadow-2xl p-8">

        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-emerald-400">EXINS Owner Portal</h1>
            <p class="text-xs text-slate-400 mt-1">Sign in or create your management account</p>
        </div>

        <!-- Success/Error Messages -->
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

        <!-- Toggle Tabs -->
        <div class="flex bg-slate-900 p-1 rounded-lg mb-6 border border-slate-700">
            <button type="button" id="tab-login" onclick="switchTab('login')"
                class="flex-1 py-2 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white">Log
                In</button>
            <button type="button" id="tab-signup" onclick="switchTab('signup')"
                class="flex-1 py-2 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white">Sign
                Up</button>
        </div>

        <!-- Auth Form -->
        <form method="POST" id="authForm" class="space-y-4">
            <input type="hidden" name="action" id="formAction" value="<?= htmlspecialchars($active_tab) ?>">

            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 mb-1">Email Address</label>
                <input type="email" name="email" required
                    class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-emerald-500 transition">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-emerald-500 transition">
            </div>

            <button type="submit" id="submitBtn"
                class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-2.5 rounded-lg text-sm transition shadow-lg mt-2">
                Log In
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="Shop/shop.php" target="contentFrame"
                class="text-xs text-slate-400 hover:text-emerald-400 transition">&larr; Return to Shop</a>
        </div>
    </div>

    <script>
        function switchTab(mode) {
            const tabLogin = document.getElementById('tab-login');
            const tabSignup = document.getElementById('tab-signup');
            const formAction = document.getElementById('formAction');
            const submitBtn = document.getElementById('submitBtn');

            if (mode === 'login') {
                tabLogin.className = "flex-1 py-2 text-xs font-semibold rounded-md transition bg-emerald-600 text-white shadow";
                tabSignup.className = "flex-1 py-2 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white";
                formAction.value = "login";
                submitBtn.innerText = "Log In";
            } else {
                tabSignup.className = "flex-1 py-2 text-xs font-semibold rounded-md transition bg-emerald-600 text-white shadow";
                tabLogin.className = "flex-1 py-2 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white";
                formAction.value = "signup";
                submitBtn.innerText = "Create Account";
            }
        }

        // Initialize active tab on load
        switchTab('<?= $active_tab ?>');
    </script>
</body>

</html>