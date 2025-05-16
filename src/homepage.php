<?php
session_start();
require('includes/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';

// ดำเนินการฝาก/ถอน
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type'], $_POST['amount'])) {
    $type = $_POST['type'];
    $amount = floatval($_POST['amount']);

    if ($amount <= 0) {
        $_SESSION['error'] = "Amount must be greater than 0.";
    } else {
        $sql = "SELECT balance FROM users WHERE id = $user_id";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($result);
        $balance = $row['balance'];

        if ($amount < 0 || $amount > 100000) {
            $_SESSION['error'] = 'Invalid Input amount must be in range 0-100000';
        } else {
            if ($type === 'withdraw') {
                if ($amount > $balance) {
                    $_SESSION['error'] = "จำนวนเงินถอนมากกว่ายอดคงเหลือ";
                } else {
                    $balance -= $amount;
                }
            } elseif ($type === 'deposit') {
                $balance += $amount;
            }
        }

        if (!isset($_SESSION['error'])) {
            $update = "UPDATE users SET balance = $balance WHERE id = $user_id";
            mysqli_query($con, $update);

            $insert = "INSERT INTO transactions (user_id, type, amount) VALUES ($user_id, '$type', $amount)";
            mysqli_query($con, $insert);
        }
    }

    header("Location: homepage.php");
    exit();
}

// ดึงข้อมูลล่าสุด
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($con, $sql);
$user = mysqli_fetch_assoc($result);

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>php-app-test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="bg-light">
    <div class="d-flex flex-column flex-md-row min-vh-100">
        <?php include("./includes/navbar.php"); ?>

        <div class="container d-flex flex-grow-1 align-items-center justify-content-center py-5">
            <div class="card shadow-lg p-4 w-100" style="max-width: 500px;">
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <h3 class="mb-3 text-center">Your current balance</h3>
                <h1 class="display-5 text-primary mb-4 text-center"><?= number_format($user['balance'], 2) ?> THB</h1>

                <form method="POST" class="row g-3">
                    <div class="col-12">
                        <label for="amount" class="form-label">Amount (THB):</label>
                        <input type="number" id="amount" name="amount" class="form-control" min="1" step="0.01"
                            required>
                    </div>
                    <div class="col-12 col-md-6">
                        <button type="submit" name="type" value="deposit" class="btn btn-success w-100">Deposit</button>
                    </div>
                    <div class="col-12 col-md-6">
                        <button type="submit" name="type" value="withdraw"
                            class="btn btn-danger w-100">Withdraw</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>