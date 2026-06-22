<?php

require("db.php");

$komunikat = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = $_POST["login"];
    $haslo = $_POST["haslo"];
    $email = $_POST["email"];

    $hash = password_hash($haslo, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO uzytkownicy (login, haslo, email, is_admin)
             VALUES (?, ?, ?, 0)"
        );
        $stmt->execute([$login, $hash, $email]);

        $komunikat = "Konto zostało utworzone! <a href='login.php'>Przejdź do logowania</a>.";
    } catch (PDOException $e) {
        $komunikat = "Nie udało się utworzyć konta (login może być już zajęty).
                      <a href='registration.php'>Spróbuj ponownie</a>.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rejestracja – Gabinet Logopedyczny</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body class="auth-page">
    <div class="auth-box">
        <h1>Rejestracja</h1>

        <?php if ($komunikat): ?>
            <p class="komunikat"><?= $komunikat ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label>Login
                <input type="text" name="login" required>
            </label>
            <label>Hasło
                <input type="password" name="haslo" required>
            </label>
            <label>Adres e-mail
                <input type="email" name="email" required>
            </label>
            <button type="submit">Zarejestruj się</button>
        </form>

        <p class="auth-switch">Masz już konto? <a href="login.php">Zaloguj się</a></p>
    </div>
</body>
</html>
