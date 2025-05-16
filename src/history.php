<?php
session_start();
require('includes/dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Logic แก้ไข transaction
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // แก้ไข transaction
    if (isset($_POST['edit_submit'])) {
        $id = (int) $_POST['id'];
        $new_type = ($_POST['type'] === 'withdraw') ? 'withdraw' : 'deposit';
        $new_amount = (float) $_POST['amount'];

        // ดึง transaction เดิม
        $old = mysqli_fetch_assoc(mysqli_query($con, "SELECT type, amount FROM transactions WHERE id = $id AND user_id = $user_id"));
        if (!$old) {
            die("ไม่พบรายการ");
        }

        // ดึง balance ปัจจุบัน
        $user = mysqli_fetch_assoc(mysqli_query($con, "SELECT balance FROM users WHERE id = $user_id"));
        $balance = (float) $user['balance'];

        // คืนยอดจากรายการเดิม
        if ($old['type'] === 'deposit') {
            $balance -= $old['amount'];
        } else {
            $balance += $old['amount'];
        }

        // หัก/เพิ่มยอดใหม่
        if ($new_type === 'deposit') {
            $balance += $new_amount;
        } else {
            $balance -= $new_amount;
        }

        // อัปเดตรายการ
        mysqli_query($con, "UPDATE transactions SET type='$new_type', amount=$new_amount WHERE id=$id AND user_id=$user_id");

        // อัปเดต balance
        mysqli_query($con, "UPDATE users SET balance=$balance WHERE id=$user_id");

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // ลบ transaction
    if (isset($_POST['delete_submit'])) {
        $id = (int) $_POST['id'];

        // ดึง transaction เดิม
        $old = mysqli_fetch_assoc(mysqli_query($con, "SELECT type, amount FROM transactions WHERE id = $id AND user_id = $user_id"));
        if (!$old) {
            die("ไม่พบรายการ");
        }

        // ดึง balance ปัจจุบัน
        $user = mysqli_fetch_assoc(mysqli_query($con, "SELECT balance FROM users WHERE id = $user_id"));
        $balance = (float) $user['balance'];

        // คืนยอดจากรายการที่ลบ
        if ($old['type'] === 'deposit') {
            $balance -= $old['amount'];
        } else {
            $balance += $old['amount'];
        }

        // ตรวจสอบไม่ให้ติดลบ
        if ($balance < 0) {
            die("ยอดเงินติดลบ ไม่สามารถลบได้");
        }

        // ลบ transaction
        mysqli_query($con, "DELETE FROM transactions WHERE id=$id AND user_id=$user_id");

        // อัปเดต balance
        mysqli_query($con, "UPDATE users SET balance=$balance WHERE id=$user_id");

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// ดึง transaction ของ user
$sql = "SELECT * FROM transactions WHERE user_id=$user_id ORDER BY created_at DESC";
$result = mysqli_query($con, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
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

<body>
    <div class="d-flex flex-column flex-md-row min-vh-100">
        <?php include("./includes/navbar.php"); ?>
        <div class="container my-5 px-3">
            <h2 class="mb-4 text-center text-md-start">Transaction History</h2>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Amount (THB)</th>
                            <th>Date</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['id'];
                            $type = htmlspecialchars($row['type']);
                            $amount = number_format($row['amount'], 2);
                            $date = $row['created_at'];
                            $badge = $type === 'deposit' ? 'success' : 'danger';
                            echo "<tr>";
                            echo "<td class='text-center'>$i</td>";
                            echo "<td class='text-center'><span class='badge bg-$badge text-uppercase'>$type</span></td>";
                            echo "<td class='text-center'>$amount</td>";
                            echo "<td class='text-center'>$date</td>";
                            echo "<td class='text-center'><button class='btn btn-warning btn-sm edit-btn' 
                                data-id='$id' data-type='$type' data-amount='{$row['amount']}' data-bs-toggle='modal' data-bs-target='#editModal'>Edit</button></td>";
                            echo "<td class='text-center'><button class='btn btn-danger btn-sm delete-btn' 
                                data-id='$id' data-bs-toggle='modal' data-bs-target='#deleteModal'>Delete</button></td>";
                            echo "</tr>";
                            $i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id" />
                    <div class="mb-3">
                        <label for="edit-type" class="form-label">Type</label>
                        <select name="type" id="edit-type" class="form-select" required>
                            <option value="deposit">Deposit</option>
                            <option value="withdraw">Withdraw</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-amount" class="form-label">Amount</label>
                        <input type="number" name="amount" id="edit-amount" class="form-control" step="0.01" min="0.01"
                            required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="edit_submit" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="delete-id" />
                    <p>Are you sure you want to delete this transaction?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="delete_submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                const type = button.getAttribute('data-type');
                const amount = button.getAttribute('data-amount');

                document.getElementById('edit-id').value = id;
                document.getElementById('edit-type').value = type;
                document.getElementById('edit-amount').value = amount;
            });
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                document.getElementById('delete-id').value = id;
            });
        });
    </script>

</body>

</html>