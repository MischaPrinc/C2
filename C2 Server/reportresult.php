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

$hardwareId = $_POST['hardwareId'];
$result = $_POST['result'];

// Aktualizace stavu úkolu na 'completed' a uložení výsledku
$sql = "UPDATE tasks SET status='completed', result='".addslashes($result)."' WHERE hardwareId='".addslashes($hardwareId)."' AND status='in progress' LIMIT 1";
$conn->query($sql);

$conn->close();
?>
