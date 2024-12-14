<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_logs'])) {
    $sql = "DELETE FROM logs";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Log history deleted successfully'); window.location.href='log_history.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<h1>Delete Customer Log History</h1>
<table border="1">
    <tr>
        <th>Username</th>
        <th>Action</th>
        <th>Timestamp</th>
    </tr>
    <?php
    $log_sql = "SELECT users.username, logs.action, logs.timestamp FROM logs JOIN users ON logs.user_id = users.id WHERE logs.action LIKE 'Deleted customer ID%'";
    $log_result = $conn->query($log_sql);
    if ($log_result->num_rows > 0) {
        while ($log = $log_result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $log['username'] . "</td>
                    <td>" . $log['action'] . "</td>
                    <td>" . $log['timestamp'] . "</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No logs found</td></tr>";
    }
    ?>
</table>
<form method="POST">
    <button type="submit" name="delete_logs">Delete Log History</button>
</form>
<a href="dashboard.php">Back to Dashboard</a>
