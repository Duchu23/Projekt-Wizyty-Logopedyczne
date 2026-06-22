<?php

require("session.php");
require("db.php");

$id             = $_POST["id"] ?? 0;
$id_uzytkownika = $_SESSION["id"];

$stmt = $pdo->prepare("DELETE FROM wizyty WHERE id = ? AND id_uzytkownika = ?");
$stmt->execute([$id, $id_uzytkownika]);

echo $stmt->rowCount() > 0 ? "ok" : "blad";
?>
