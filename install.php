<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pc_service";
$db_exists = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli($servername, $username, $password);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_POST['install'])) {
        $sql = "CREATE DATABASE IF NOT EXISTS $dbname;
                USE $dbname;
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
                );
                CREATE TABLE IF NOT EXISTS customers (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    customer_id VARCHAR(50) NOT NULL UNIQUE,
                    company_name VARCHAR(100),
                    contact_number VARCHAR(15),
                    person_in_charge VARCHAR(50),
                    remarks TEXT,
                    register_date DATE NOT NULL,
                    renewal_date DATE NOT NULL,
                    package VARCHAR(50) NOT NULL,
                    amount_paid DECIMAL(10, 2) NOT NULL,
                    days_left INT AS (DATEDIFF(renewal_date, CURDATE())),
                    status VARCHAR(10) AS (IF(DATEDIFF(renewal_date, CURDATE()) > 0, 'Active', 'Expired'))
                );
                CREATE TABLE IF NOT EXISTS logs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    action VARCHAR(255) NOT NULL,
                    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id)
                );
                INSERT INTO users (username, password, role) VALUES ('admin', MD5('admin'), 'admin');";

        if ($conn->multi_query($sql) === TRUE) {
            echo "Database and tables created successfully.";
        } else {
            echo "Error creating tables: " . $conn->error;
        }
    }

    if (isset($_POST['delete'])) {
        $conn->select_db($dbname);
        $sql = "DROP TABLE IF EXISTS logs, customers, users";
        if ($conn->multi_query($sql) === TRUE) {
            echo "Tables deleted successfully.";
        } else {
            echo "Error deleting tables: " . $conn->error;
        }
    }

    $conn->close();
}

$conn = new mysqli($servername, $username, $password);
if ($conn->select_db($dbname) === TRUE) {
    $db_exists = true;
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Install</title>
</head>
<body>
    <h1>Install or Delete Database Tables</h1>
    <form method="POST">
        <button type="submit" name="install">Install Database and Tables</button>
        <button type="submit" name="delete">Delete Database Tables</button>
    </form>
    <h2>Database Connection Details</h2>
    <form method="POST">
        <label>Server Name: <input type="text" name="servername" value="<?php echo $servername; ?>"></label><br>
        <label>Username: <input type="text" name="username" value="<?php echo $username; ?>"></label><br>
        <label>Password: <input type="password" name="password" value="<?php echo $password; ?>"></label><br>
        <button type="submit" name="update">Update Connection Details</button>
    </form>
    <?php if ($db_exists): ?>
        <h3>Status: Database 'pc_service' exists</h3>
    <?php else: ?>
        <h3>Status: Database 'pc_service' does not exist</h3>
    <?php endif; ?>
</body>
</html>
