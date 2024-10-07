<?php
$servername = "localhost";
$username = "";
$password = "";
$dbname = "";

// Vytvoření připojení
$conn = new mysqli($servername, $username, $password, $dbname);

// Kontrola připojení
if ($conn->connect_error) {
    die("Připojení selhalo: " . $conn->connect_error);
}

$hardwareId = addslashes($_POST['hardwareId']);
$ipAddress = addslashes($_SERVER['REMOTE_ADDR']);

// Vložení záznamu do tabulky online
$sql = "INSERT INTO online (hardwareId, ipAddress, timestamp) VALUES ('$hardwareId', '$ipAddress', NOW())";
$conn->query($sql);

// Kontrola, zda existuje úkol pro tento hardwareId
$sql = "SELECT task FROM tasks WHERE hardwareId='$hardwareId' AND status='pending' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Výstup úkolu
    $row = $result->fetch_assoc();
    echo $row['task'];

    // Aktualizace stavu úkolu na 'in progress'
    $updateSql = "UPDATE tasks SET status='in progress' WHERE hardwareId='$hardwareId' AND status='pending' LIMIT 1";
    $conn->query($updateSql);
} else {
    echo "";
}

$conn->close();
?>
