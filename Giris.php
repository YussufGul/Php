<?php
session_start();

$timeout = 60; // 60 seconds timeout

// Session timeout check
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: giris.php");
    exit();
}

$_SESSION['last_activity'] = time(); // Update last activity time

// Handle table login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['masa'])) {
    $masaNumarasi = htmlspecialchars($_POST['masa']);
    $_SESSION['masa'] = $masaNumarasi;
    header("Location: menu.php");
    exit();
}

// Handle admin login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    $conn = new mysqli('localhost', 'root', '', 'users');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin'] = $username;
            header("Location: admin_panel.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lezzet İskender - Giriş</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #32CD32;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="number"], input[type="text"], input[type="password"] {
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
    </style>
</head>
<body>
    <form action="giris.php" method="POST">
        <h1>Lezzet İskender'e Hoşgeldiniz</h1>
        <p>Lütfen masa numaranızı girin:</p>
        <input type="number" name="masa" required>
        <button type="submit">Giriş Yap</button>
    </form>
    <form action="giris.php" method="POST">
        <h1>Admin Giriş</h1>
        <?php if (isset($error)) : ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <input type="text" name="username" placeholder="Kullanıcı Adı" required>
        <input type="password" name="password" placeholder="Şifre" required>
        <button type="submit">Giriş Yap</button>
    </form>
</body>
</html>