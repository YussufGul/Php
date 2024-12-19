<?php
session_start();

// Oturum süresi kontrolü ve yönlendirme
if (!isset($_SESSION['siparis']) || time() > $_SESSION['siparis']['hazirlik_zamani']) {
    header("Location: giris.php");
    exit();
}

$masa = $_SESSION['siparis']['masa'];
$yemek = $_SESSION['siparis']['yemek'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lezzet İskender - Hazırlanıyor</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #32CD32; /* Yeşil arka plan */
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            text-align: center;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #006400; /* Daha koyu yeşil */
        }
        .game-container {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .button {
            padding: 10px 20px;
            font-size: 1em;
            color: white;
            background-color: #006400; /* Daha koyu yeşil */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        .button:hover {
            background-color: #228B22; /* Daha açık yeşil */
        }
        .timer {
            margin-top: 20px;
            font-size: 1.5em;
            color: #006400;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($yemek); ?> hazırlanıyor</h1>
        <p>Masa numarası: <?php echo htmlspecialchars($masa); ?></p>

        <div class="game-container">
            <h2>Oyun: Basit Bir Sayı Tahmini</h2>
            <p>Sayıyı 1 ile 10 arasında tahmin edin:</p>
            <input type="number" id="guess" min="1" max="10" required>
            <button class="button" onclick="checkGuess()">Tahmin Et</button>
            <p id="result"></p>
        </div>

        <div class="timer" id="timer">5 dakika kaldı</div>
    </div>

    <script>
        var countDownDate = new Date().getTime() + (5 * 60 * 1000); // 5 dakika süresi

        var x = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate - now;

            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("timer").innerHTML = minutes + " dakika " + seconds + " saniye kaldı";

            if (distance < 0) {
                clearInterval(x);
                document.getElementById("timer").innerHTML = "Süre doldu";
                window.location.href = "giris.php"; // Süre dolduğunda yönlendirme
            }
        }, 1000);

        function checkGuess() {
            var guess = document.getElementById("guess").value;
            var randomNumber = Math.floor(Math.random() * 10) + 1;

            if (guess == randomNumber) {
                document.getElementById("result").innerHTML = "Tebrikler, doğru tahmin!";
            } else {
                document.getElementById("result").innerHTML = "Maalesef, yanlış tahmin. Doğru sayı " + randomNumber + " idi.";
            }
        }
    </script>
</body>
</html>
