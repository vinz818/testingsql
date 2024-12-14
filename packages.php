<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

$packages = [
    ['name' => 'Package #1', 'price' => 2500],
    ['name' => '#1 Sec Pack', 'price' => 1800],
    ['name' => '#1 Additional x1 PC', 'price' => 350],
    ['name' => 'Package #2', 'price' => 2500],
    ['name' => '#2 Sec Pack', 'price' => 1800],
    ['name' => '#2 Additional x1 PC', 'price' => 450],
];
?>

<h1>Available Packages</h1>
<table border="1">
    <tr>
        <th>Package Name</th>
        <th>Price (RM)</th>
    </tr>
    <?php foreach ($packages as $package): ?>
        <tr>
            <td><?php echo $package['name']; ?></td>
            <td><?php echo $package['price']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>
