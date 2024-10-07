<?php
$servername = "";
$username = "";
$password = "";
$dbname = "";

// Vytvoření připojení
$conn = new mysqli($servername, $username, $password, $dbname);

// Kontrola připojení
if ($conn->connect_error) {
    die("Připojení selhalo: " . $conn->connect_error);
}

// Získání aktuálního času a času před 5 minutami
$currentTime = date('Y-m-d H:i:s');
$timeFiveMinutesAgo = date('Y-m-d H:i:s', strtotime('-5 minutes'));

// Výběr unikátních hardware ID, která se přihlásila v posledních 5 minutách
$sql = "SELECT DISTINCT hardwareId, MAX(timestamp) as last_seen, ipAddress FROM online WHERE timestamp >= '$timeFiveMinutesAgo' GROUP BY hardwareId";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seznam online počítačů</title>
<meta http-equiv="refresh" content="10">
</head>
<body>
    <h1>Seznam online počítačů</h1>
    <table border="1">
        <tr>
            <th>Hardware ID</th>
            <th>IP Adresa</th>
            <th>Čas</th>
            <th>Úkoly</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row["hardwareId"]. "</td>
                        <td>" . $row["ipAddress"]. "</td>
                        <td>" . $row["last_seen"]. "</td>
                        <td><a href='tasks.php?hardwareId=" . $row["hardwareId"] . "'>Seznam úkolů</a></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Žádné počítače se nepřihlásily v posledních 5 minutách</td></tr>";
        }
        ?>
    </table>
</body>
</html>
<?php
$conn->close();
?>
