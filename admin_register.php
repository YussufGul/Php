<?php
// Veritabanı bağlantısı
$conn = new mysqli('localhost', 'root', '', 'users');
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Admin kullanıcı adı ve şifresi
$username = 'admin';
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Admin kullanıcısını ekleme
$stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed_password);
if ($stmt->execute()) {
    echo "Admin kullanıcı başarıyla oluşturuldu.";
} else {
    echo "Hata: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
