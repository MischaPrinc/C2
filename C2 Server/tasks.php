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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hardwareId = $conn->real_escape_string($_POST['hardwareId']);
    $task = $conn->real_escape_string($_POST['task']);

    $sql = "INSERT INTO tasks (hardwareId, task, status) VALUES ('$hardwareId', '$task', 'pending')";
    if ($conn->query($sql) === TRUE) {
        header('Location: tasks.php?hardwareId='. $hardwareId);
        exit();
    } else {
        echo "Chyba: " . $sql . "<br>" . $conn->error;
    }
}

$hardwareId = $conn->real_escape_string($_GET['hardwareId']);

// Výběr úkolů pro dané hardware ID
$sql = "SELECT * FROM tasks WHERE hardwareId='$hardwareId' ORDER by ID DESC LIMIT 10";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Správa úkolů</title>
    <meta http-equiv="refresh" content="60">
</head>
<body>
    <h1>Přidat úkol pro Hardware ID: <?php echo htmlspecialchars($hardwareId); ?></h1>
    <form method="post" action="">
        <input type="hidden" name="hardwareId" value="<?php echo htmlspecialchars($hardwareId); ?>">
        Úkol: <input type="text" name="task" required><br>
        <input type="submit" value="Přidat úkol">
    </form>
    <hr>
    <h1>Seznam úkolů - <?php echo htmlspecialchars($hardwareId); ?></h1><a href="index.php">Zpět</a><br><br>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Úkol</th>
            <th>Stav</th>
            <th>Výsledek</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["id"]). "</td>
                        <td>" . htmlspecialchars($row["task"]). "</td>
                        <td>" . htmlspecialchars($row["status"]). "</td>
                        <td><pre>" . htmlspecialchars($row["result"]). "<pre></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Žádné úkoly</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
