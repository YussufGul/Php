<?php
session_start();
include 'config.php';

if (!isset($_SESSION['loggedin']) || (time() - $_SESSION['start']) > 300) {
    // Oturum açılmamışsa veya oturum süresi 5 dakikayı geçtiyse çıkış yap
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
} else {
    $_SESSION['start'] = time(); // Oturum süresini güncelle
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order'])) {
    $username = $_SESSION['username'];
    $order_details = $_POST['order_details'];

    $sql = "UPDATE musteriler SET order_details = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $order_details, $username);
    $stmt->execute();
    $stmt->close();
}

$sql = "SELECT username, order_details FROM musteriler";
$result = $conn->query($sql);

$musteriler = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $musteriler[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Anasayfa</title>
</head>
<body>
    <h1>Hoşgeldiniz, <?php echo $_SESSION['username']; ?></h1>
    <a href="logout.php">Çıkış Yap</a>

    <h2>Sipariş Ver</h2>
    <form method="POST" action="index.php">
        <label for="order_details">Sipariş Detayları:</label>
        <textarea id="order_details" name="order_details" required></textarea><br>
        <input type="submit" name="order" value="Sipariş Ver">
    </form>

    <h2>Müşteriler ve Siparişleri</h2>
    <ul>
        <?php foreach ($musteriler as $musteri): ?>
            <li><?php echo $musteri['username'] . ': ' . $musteri['order_details']; ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
