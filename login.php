<?php

require("db.php");
session_start();

$blad = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = $_POST["login"];
    $haslo = $_POST["haslo"];

    $stmt = $pdo->prepare("SELECT * FROM uzytkownicy WHERE login = ?");
    $stmt->execute([$login]);
    $uzytkownik = $stmt->fetch();

    if ($uzytkownik && password_verify($haslo, $uzytkownik["haslo"])) {
        $_SESSION["login"]    = $uzytkownik["login"];
        $_SESSION["is_admin"] = $uzytkownik["is_admin"];
        $_SESSION["id"]       = $uzytkownik["id"];

        header("Location: index.php");
        exit;
    } else {
        $blad = "Błędny login lub hasło. <a href='login.php'>Spróbuj ponownie</a>.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logowanie – Gabinet Logopedyczny</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body class="auth-page">
    <div class="auth-box">
        <h1>🗣️ Gabinet Logopedyczny</h1>
        <h2>Logowanie</h2>

        <?php if ($blad): ?>
            <p class="blad"><?= $blad ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label>Login
                <input type="text" name="login" required>
            </label>
            <label>Hasło
                <input type="password" name="haslo" required>
            </label>
            <button type="submit">Zaloguj się</button>
        </form>

        <p class="auth-switch">Nie masz konta? <a href="registration.php">Zarejestruj się</a></p>
    </div>
</body>
</html>
