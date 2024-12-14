<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
require 'db.php';

// Backup functionality
if (isset($_POST['backup'])) {
    $backup_file = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $command = "mysqldump --user={$username} --password={$password} --host={$servername} {$dbname} > {$backup_file}";
    exec($command, $output, $result);

    if ($result == 0) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($backup_file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($backup_file));
        readfile($backup_file);
        exit;
    } else {
        echo "<script>alert('Failed to create database backup.'); window.location.href='dashboard.php';</script>";
    }
}

// Restore functionality
if (isset($_POST['restore'])) {
    if (is_uploaded_file($_FILES['backup_file']['tmp_name'])) {
        $backup_file = $_FILES['backup_file']['tmp_name'];
        $command = "mysql --user={$username} --password={$password} --host={$servername} {$dbname} < {$backup_file}";
        exec($command, $output, $result);

        if ($result == 0) {
            echo "<script>alert('Database restored successfully.'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Failed to restore database.'); window.location.href='dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Please upload a valid backup file.'); window.location.href='dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Backup and Restore Database</title>
</head>
<body>
    <h1>Backup and Restore Database</h1>
    
    <!-- Form for creating a backup -->
    <form method="POST">
        <button type="submit" name="backup">Backup Database</button><br><br>
    </form>

    <!-- Form for restoring from a backup -->
    <form method="POST" enctype="multipart/form-data">
        <label for="backup_file">Choose Backup File:</label>
        <input type="file" name="backup_file" id="backup_file" required><br><br>
        <button type="submit" name="restore">Restore Database</button>
    </form>

    <!-- Link to go back to the dashboard -->
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
