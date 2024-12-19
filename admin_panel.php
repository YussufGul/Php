<?php
session_start();

// Eğer admin oturumu açık değilse, admin giriş sayfasına yönlendir
if (!isset($_SESSION['admin'])) {
    header("Location: giris.php");
    exit();
}

// Veritabanı bağlantısı
$conn = new mysqli('localhost', 'root', '', 'users');
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Yeni yemek ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $yemek = htmlspecialchars($_POST['yemek']);
    $fiyat = htmlspecialchars($_POST['fiyat']);
    $foto = $_FILES['foto']['name'];
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);

    // Dosya türü ve boyutu kontrolü
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    if ($check !== false && ($_FILES["foto"]["size"] <= 500000) && in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        // Benzersiz dosya adı oluşturma
        $unique_filename = uniqid() . "." . $imageFileType;
        $target_file = $target_dir . $unique_filename;
        
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO menu (yemek, fiyat, foto) VALUES (?, ?, ?)");
            $stmt->bind_param("sds", $yemek, $fiyat, $unique_filename);

            if ($stmt->execute()) {
                $message = "Yemek başarıyla eklendi.";
            } else {
                $error = "Yemek eklenirken bir hata oluştu.";
            }

            $stmt->close();
        } else {
            $error = "Fotoğraf yüklenirken bir hata oluştu.";
        }
    } else {
        $error = "Geçersiz dosya türü veya boyutu.";
    }
}

// Menüdeki tüm yemekleri çekme
$result = $conn->query("SELECT * FROM menu");

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #32CD32;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        header {
            width: 100%;
            background-color: #006400;
            color: white;
            text-align: center;
            padding: 20px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin: 0;
            font-size: 2em;
        }
        .container {
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
        form {
            text-align: center;
            margin-top: 20px;
        }
        input[type="text"], input[type="number"], input[type="file"] {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #006400;
            border-radius: 4px;
            margin-bottom: 10px;
            width: calc(100% - 22px);
        }
        button {
            padding: 10px 20px;
            font-size: 1em;
            color: white;
            background-color: #006400;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #228B22;
        }
        .message {
            color: green;
        }
        .error {
            color: red;
        }
        .food-img {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Paneli</h1>
    </header>
    <div class="container">
        <?php if (isset($message)) : ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php if (isset($error)) : ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="admin_panel.php" method="POST" enctype="multipart/form-data">
            <h2>Yeni Yemek Ekle</h2>
            <input type="hidden" name="action" value="add">
            <input type="text" name="yemek" placeholder="Yemek Adı" required>
            <input type="number" name="fiyat" placeholder="Fiyat" step="0.01" required>
            <input type="file" name="foto" required>
            <button type="submit">Ekle</button>
        </form>

        <h2>Mevcut Menü</h2>
        <table>
            <thead>
                <tr>
                    <th>Yemek</th>
                    <th>Fiyat</th>
                    <th>Fotoğraf</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['yemek']); ?></td>
                        <td><?php echo number_format($row['fiyat'], 2); ?> TL</td>
                        <td><img src="images/<?php echo htmlspecialchars($row['foto']); ?>" alt="<?php echo htmlspecialchars($row['yemek']); ?>" class="food-img" width="50" height="50"></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
$conn->close();
?>
