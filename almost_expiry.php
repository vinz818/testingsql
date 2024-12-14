<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';

$sql = "SELECT *, DATEDIFF(renewal_date, CURDATE()) AS days_left FROM customers WHERE renewal_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)";
$result = $conn->query($sql);
?>

<h1>Customers Expiring in 1 Month</h1>
<table border="1">
    <tr>
        <th>Customer ID</th>
        <th>Register Date</th>
        <th>Renewal Date</th>
        <th>Package</th>
        <th>Days Left</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['customer_id'] . "</td>
                    <td>" . $row['register_date'] . "</td>
                    <td>" . $row['renewal_date'] . "</td>
                    <td>" . $row['package'] . "</td>
                    <td>" . $row['days_left'] . "</td>
                    <td>" . $row['status'] . "</td>
                    <td>
                        <a href='edit_customer.php?id=" . $row['id'] . "'>Edit</a> |
                        <form method='POST' action='dashboard.php' onsubmit='return confirm(\"Are you sure you want to extend the subscription by 1 year?\")'>
                            <input type='hidden' name='id' value='" . $row['id'] . "'>
                            <button type='submit' name='extend'>Update 1 More Year</button>
                        </form>
                    </td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No customers found</td></tr>";
    }
    ?>
</table>
