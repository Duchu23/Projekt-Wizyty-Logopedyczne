<?php

require("session.php");
require("db.php");

$id_cwiczenia   = $_POST["id_cwiczenia"] ?? 0;
$id_uzytkownika = $_SESSION["id"];

try {
    $stmt = $pdo->prepare("SELECT id FROM ulubione WHERE id_cwiczenia = ? AND id_uzytkownika = ?");
    $stmt->execute([$id_cwiczenia, $id_uzytkownika]);
    $istnieje = $stmt->fetch();

    if ($istnieje) {
        $stmt = $pdo->prepare("DELETE FROM ulubione WHERE id_cwiczenia = ? AND id_uzytkownika = ?");
        $stmt->execute([$id_cwiczenia, $id_uzytkownika]);
        echo "usunieto";
    } else {
        $stmt = $pdo->prepare("INSERT INTO ulubione (id_cwiczenia, id_uzytkownika) VALUES (?, ?)");
        $stmt->execute([$id_cwiczenia, $id_uzytkownika]);
        echo "dodano";
    }
} catch (PDOException $e) {
    echo "blad";
}
?>
