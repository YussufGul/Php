<?php
// Veritabanı bağlantısı
$conn = new mysqli('localhost', 'root', '', 'users');
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Masa numarası ve toplam fiyatları al
$sql = "SELECT masa_numarasi, SUM(fiyat) AS toplam_fiyat FROM orders GROUP BY masa_numarasi";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masa Siparişleri</title>
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
        .order-container {
            width: 80%;
            max-width: 800px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-item span {
            font-size: 1.2em;
        }
        footer {
            width: 100%;
            background-color: #006400; /* Daha koyu yeşil */
            color: white;
            text-align: center;
            padding: 20px 0;
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <header>
        <h1>Masa Siparişleri</h1>
    </header>

    <div class="order-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="order-item">';
                echo '<span>Masa ' . $row['masa_numarasi'] . '</span>';
                echo '<span>' . $row['toplam_fiyat'] . ' TL</span>';
                echo '</div>';
            }
        } else {
            echo '<p>Hiç sipariş yok.</p>';
        }
        ?>
    </div>

    <footer>
        <form action="menu.php" method="POST">
   
