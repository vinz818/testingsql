<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}
require 'db.php';
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$packages = ['Package #1', '#1 Sec Pack', '#1 Additional x1 PC', 'Package #2', '#2 Sec Pack', '#2 Additional x1 PC'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_customer'])) {
        $customer_id = $_POST['customer_id'];
        $company_name = $_POST['company_name'];
        $contact_number = $_POST['contact_number'];
        $person_in_charge = $_POST['person_in_charge'];
        $remarks = $_POST['remarks'];
        $register_date = $_POST['register_date'];
        $package = $_POST['package'];
        $years = $_POST['years'];
        $amount_paid = $_POST['amount_paid'];
        $renewal_date = date('Y-m-d', strtotime($register_date . " + $years year"));
        $sql = "SELECT * FROM customers WHERE customer_id='$customer_id'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "<script>alert('Customer ID already exists'); window.location.href='dashboard.php';</script>";
        } else {
            $sql = "INSERT INTO customers (customer_id, company_name, contact_number, person_in_charge, remarks, register_date, renewal_date, package, amount_paid) VALUES ('$customer_id', '$company_name', '$contact_number', '$person_in_charge', '$remarks', '$register_date', '$renewal_date', '$package', '$amount_paid')";
            if ($conn->query($sql) === TRUE) {
                $sql_log = "INSERT INTO logs (user_id, action) VALUES ('$user_id', 'Added new customer $customer_id')";
                $conn->query($sql_log);
                echo "New customer added successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
    if (isset($_POST['search'])) {
        $search_id = $_POST['search_id'];
        $sql = "SELECT * FROM customers WHERE customer_id='$search_id'";
        $result = $conn->query($sql);
    } else {
        $sql = "SELECT *, DATEDIFF(renewal_date, CURDATE()) AS days_left FROM customers";
        $result = $conn->query($sql);
    }
    if (isset($_POST['extend'])) {
        $id = $_POST['id'];
        $sql = "UPDATE customers SET renewal_date = DATE_ADD(renewal_date, INTERVAL 1 YEAR) WHERE id = '$id'";
        if ($conn->query($sql) === TRUE) {
            $sql_log = "INSERT INTO logs (user_id, action) VALUES ('$user_id', 'Extended subscription for customer ID $id by 1 year')";
            $conn->query($sql_log);
            echo "<script>alert('Renewal date updated successfully'); window.location.href='dashboard.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM customers WHERE id = '$id'";
        if ($conn->query($sql) === TRUE) {
            $sql_log = "INSERT INTO logs (user_id, action) VALUES ('$user_id', 'Deleted customer ID $id')";
            $conn->query($sql_log);
            echo "<script>alert('Customer record deleted successfully'); window.location.href='dashboard.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    if (isset($_POST['delete_logs']) && $user_role === 'admin') {
        $sql = "DELETE FROM logs";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Log history deleted successfully'); window.location.href='dashboard.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} else {
    $sql = "SELECT *, DATEDIFF(renewal_date, CURDATE()) AS days_left FROM customers";
    $result = $conn->query($sql);
}
?>

<h1>Dashboard</h1>
<form method="POST">
    Customer ID: <input type="text" name="customer_id" required>
    Company Name: <input type="text" name="company_name"><br>
    Contact Number: <input type="text" name="contact_number"><br>
    Person In Charge: <input type="text" name="person_in_charge"><br>
    Remarks: <textarea name="remarks"></textarea><br>
    Register Date: <input type="date" name="register_date" required><br>
    Package: 
    <select name="package">
        <?php foreach ($packages as $package): ?>
            <option value="<?php echo $package; ?>"><?php echo $package; ?></option>
        <?php endforeach; ?>
    </select><br>
    Years: <input type="number" name="years" value="1" min="1" required><br>
    Amount Paid: <input type="number" name="amount_paid" required><br>
    <button type="submit" name="add_customer">Add Customer</button>
</form>
<form method="POST">
    Search Customer ID: <input type="text" name="search_id"><br>
    <button type="submit" name="search">Search</button>
</form>
<form method="GET" action="almost_expiry.php">
    <button type="submit">Show Almost Expire</button>
</form>

<table border="1">
    <tr>
        <th>Customer ID</th>
        <th>Company Name</th>
        <th>Contact Number</th>
        <th>Person In Charge</th>
        <th>Remarks</th>
        <th>Register Date</th>
        <th>Renewal Date</th>
        <th>Package</th>
        <th>Days Left</th>
        <th>Status</th>
        <th>Amount Paid (RM)</th>
        <th>Actions</th>
        <th>User Activity Log</th>
    </tr>
    <?php
    $total_paid = 0;
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $total_paid += $row['amount_paid'];
            echo "<tr>
                    <td>" . $row['customer_id'] . "</td>
                    <td>" . $row['company_name'] . "</td>
                    <td>" . $row['contact_number'] . "</td>
                    <td>" . $row['person_in_charge'] . "</td>
                    <td>" . $row['remarks'] . "</td>
                    <td>" . $row['register_date'] . "</td>
                    <td>" . $row['renewal_date'] . "</td>
                    <td>" . $row['package'] . "</td>
                    <td>" . $row['days_left'] . "</td>
                    <td>" . $row['status'] . "</td>
                    <td>" . $row['amount_paid'] . "</td>
                    <td>
                        <a href='edit_customer.php?id=" . $row['id'] . "'>Edit</a> |
                        <form method='POST' style='display:inline;' action='' onsubmit='return confirm(\"Are you sure you want to extend the subscription by 1 year?\")'>
                            <input type='hidden' name='id' value='" . $row['id'] . "'>
                            <button type='submit' name='extend'>Update 1 More Year</button>
                        </form> |
                        <form method='POST' style='display:inline;' action='' onsubmit='return confirm(\"Are you sure you want to delete this customer?\")'>
                            <input type='hidden' name='id' value='" . $row['id'] . "'>
                            <button type='submit' name='delete'>Delete</button>
                        </form>
                    </td>
                    <td>";
                    $log_sql = "SELECT users.username, logs.action, logs.timestamp FROM logs JOIN users ON logs.user_id = users.id WHERE logs.action LIKE '%customer ID " . $row['id'] . "%'";
                    $log_result = $conn->query($log_sql);
                    if ($log_result->num_rows > 0) {
                        while ($log = $log_result->fetch_assoc()) {
                            echo "<p><strong>" . $log['username'] . "</strong> - " . $log['action'] . " on " . $log['timestamp'] . "</p>";
                        }
                    } else {
                        echo "No activity logs.";
                    }
                    echo "</td>
                  </tr>";
        }
    } else {
                echo "<tr><td colspan='13'>No customers found</td></tr>";
    }
    ?>
    <tr>
        <td colspan='13'>Total Amount Paid (RM): <?php echo $total_paid; ?></td>
    </tr>
</table>

<?php if ($user_role === 'admin'): ?>
    <form method="POST">
        <button type="submit" name="delete_logs">Delete Log History</button>
    </form>
    <a href="create_user.php">Create New User</a>
    <a href="log_history.php">View Delete Customer Log History</a>
    <a href="backup_restore.php">Backup and Restore Database</a>
<?php endif; ?>

<form method="POST">
    <button type="submit" name="logout">Logout</button>
</form>
