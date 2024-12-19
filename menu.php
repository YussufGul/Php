<?php
session_start();
if (!isset($_SESSION['masa'])) {
    header("Location: giris.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'users');
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['yemek']) && isset($_POST['fiyat'])) {
        $masaNumarasi = $_SESSION['masa'];
        $yemek = htmlspecialchars($_POST['yemek']);
        $fiyat = htmlspecialchars($_POST['fiyat']);

        $stmt = $conn->prepare("INSERT INTO orders (masa_numarasi, yemek, fiyat) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $masaNumarasi, $yemek, $fiyat);

        if ($stmt->execute()) {
            $_SESSION['siparis'] = [
                'masa' => $masaNumarasi,
                'yemek' => $yemek,
                'hazirlik_zamani' => time() + 300 // 5 dakika sonra oturum sona erecek
            ];
            header("Location: hazirlaniyor.php");
            exit();
        } else {
            echo "<script>alert('Sipariş alınırken bir hata oluştu.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Geçersiz istek.');</script>";
    }
}

$menuItems = [
    ['yemek' => 'Adana Kebap', 'fiyat' => 40.00, 'foto' => 'adana.jpeg'],
    ['yemek' => 'Ayran', 'fiyat' => 5.00, 'foto' => 'ayran.jpeg'],
    ['yemek' => 'İskender Kebap', 'fiyat' => 35.00, 'foto' => 'iskender.jpeg'],
    ['yemek' => 'Köfte', 'fiyat' => 30.00, 'foto' => 'köfte.jpeg'],
    ['yemek' => 'Kola', 'fiyat' => 7.00, 'foto' => 'kola.jpeg'],
    ['yemek' => 'Kuzu Şiş', 'fiyat' => 45.00, 'foto' => 'kuzu.jpeg'],
    ['yemek' => 'Lahmacun', 'fiyat' => 10.00, 'foto' => 'lahmacun.jpeg'],
    ['yemek' => 'Tavuk Şiş', 'fiyat' => 25.00, 'foto' => 'tavuk.jpeg'],
    ['yemek' => 'Pide', 'fiyat' => 15.00, 'foto' => 'pide.jpeg'],
    ['yemek' => 'Urfa Kebap', 'fiyat' => 38.00, 'foto' => 'urfa.jpeg'],
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lezzet İskender - Menü</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #32CD32; /* Yeşil arka plan */
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        header {
            width: 100%;
            background-color: #006400; /* Daha koyu yeşil */
            color: white;
            text-align: center;
            padding: 20px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin: 0;
            font-size: 2em;
        }
        .menu-container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .food-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
        form {
            text-align: center;
            margin-top: 20px;
        }
        input[type="hidden"], input[type="submit"] {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #006400;
            border-radius: 4px;
            margin-bottom: 10px;
            width: calc(100% - 22px);
        }
        input[type="submit"] {
            background-color: #006400; /* Daha koyu yeşil */
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #228B22; /* Daha açık yeşil */
        }
    </style>
</head>
<body>
    <header>
        <h1>Menü</h1>
    </header>
    <div class="menu-container">
        <table>
            <thead>
                <tr>
                    <th>Yemek</th>
                    <th>Fiyat</th>
                    <th>Fotoğraf</th>
                    <th>Sipariş Ver</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menuItems as $item) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['yemek']); ?></td>
                        <td><?php echo number_format($item['fiyat'], 2); ?> TL</td>
                        <td><img src="images/<?php echo htmlspecialchars($item['foto']); ?>" alt="<?php echo htmlspecialchars($item['yemek']); ?>" class="food-img"></td>
                        <td>
                            <form action="menu.php" method="POST">
                                <input type="hidden" name="yemek" value="<?php echo htmlspecialchars($item['yemek']); ?>">
                                <input type="hidden" name="fiyat" value="<?php echo htmlspecialchars($item['fiyat']); ?>">
                                <input type="submit" value="Sipariş Ver">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
$conn->close();
?>
