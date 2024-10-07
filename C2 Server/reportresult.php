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

$hardwareId = $conn->real_escape_string($_POST['hardwareId']);
$result = $conn->real_escape_string($_POST['result']);

// Aktualizace stavu úkolu na 'completed' a uložení výsledku
$sql = "UPDATE tasks SET status='completed', result='$result' WHERE hardwareId='$hardwareId' AND status='in progress' LIMIT 1";
if ($conn->query($sql) === TRUE) {
    echo "Úkol byl úspěšně aktualizován.";
} else {
    echo "Chyba při aktualizaci úkolu: " . $conn->error;
}

$conn->close();
?>
