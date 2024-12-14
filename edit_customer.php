<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}
require 'db.php';
$customer_id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $register_date = $_POST['register_date'];
    $renewal_date = $_POST['renewal_date'];
    $package = $_POST['package'];
    $amount_paid = $_POST['amount_paid'];
    $company_name = $_POST['company_name'];
    $contact_number = $_POST['contact_number'];
    $person_in_charge = $_POST['person_in_charge'];
    $remarks = $_POST['remarks'];

    $sql = "UPDATE customers SET register_date='$register_date', renewal_date='$renewal_date', package='$package', amount_paid='$amount_paid', company_name='$company_name', contact_number='$contact_number', person_in_charge='$person_in_charge', remarks='$remarks' WHERE id='$customer_id'";
    if ($conn->query($sql) === TRUE) {
        $user_id = $_SESSION['user_id'];
        $sql_log = "INSERT INTO logs (user_id, action) VALUES ('$user_id', 'Edited customer ID $customer_id')";
        $conn->query($sql_log);
        echo "<script>alert('Customer updated successfully'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    $sql = "SELECT * FROM customers WHERE id='$customer_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
    } else {
        echo "No customer found";
        exit();
    }
}
?>

<h1>Edit Customer</h1>
<form method="POST">
    Register Date: <input type="date" name="register_date" value="<?php echo $customer['register_date']; ?>" required><br>
    Renewal Date: <input type="date" name="renewal_date" value="<?php echo $customer['renewal_date']; ?>" required><br>
    Package: 
    <select name="package">
        <option value="Package #1" <?php if ($customer['package'] == 'Package #1') echo 'selected'; ?>>Package #1</option>
        <option value="#1 Sec Pack" <?php if ($customer['package'] == '#1 Sec Pack') echo 'selected'; ?>>#1 Sec Pack</option>
        <option value="#1 Additional x1 PC" <?php if ($customer['package'] == '#1 Additional x1 PC') echo 'selected'; ?>>#1 Additional x1 PC</option>
        <option value="Package #2" <?php if ($customer['package'] == 'Package #2') echo 'selected'; ?>>Package #2</option>
        <option value="#2 Sec Pack" <?php if ($customer['package'] == '#2 Sec Pack') echo 'selected'; ?>>#2 Sec Pack</option>
        <option value="#2 Additional x1 PC" <?php if ($customer['package'] == '#2 Additional x1 PC') echo 'selected'; ?>>#2 Additional x1 PC</option>
    </select><br>
    Company Name: <input type="text" name="company_name" value="<?php echo $customer['company_name']; ?>"><br>
    Contact Number: <input type="text" name="contact_number" value="<?php echo $customer['contact_number']; ?>"><br>
    Person In Charge: <input type="text" name="person_in_charge" value="<?php echo $customer['person_in_charge']; ?>"><br>
    Remarks: <textarea name="remarks"><?php echo $customer['remarks']; ?></textarea><br>
    Amount Paid: <input type="number" name="amount_paid" value="<?php echo $customer['amount_paid']; ?>" required><br>
    <button type="submit">Update Customer</button>
</form>
<a href="dashboard.php">Back to Dashboard</a>
