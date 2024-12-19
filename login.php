<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id FROM musteriler WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['start'] = time(); // Oturum başlangıç zamanını kaydet
        header("Location: index.php");
    } else {
        echo "Yanlış kullanıcı adı veya şifre.";
    }

    $stmt->close();
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #32CD32; /* Yeşil arka plan */
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        input[type="text"], input[type="password"] {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            font-size: 1em;
            background-color: #FFD700; /* Sarı */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        button:hover {
            background-color: #FFC107; /* Daha koyu sarı */
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Giriş Yap</h2>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Kullanıcı Adı veya E-posta" required>
            <input type="password" name="password" placeholder="Şifre" required>
            <button type="submit">Giriş Yap</button>
        </form>
        <p>Hesabınız yok mu? <a href="register.php">Kayıt Ol</a></p>
    </div>
</body>
</html>
