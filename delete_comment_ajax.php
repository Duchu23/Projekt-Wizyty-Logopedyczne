<?php

require("session.php");
require("db.php");

$id    = $_POST["id"] ?? 0;
$login = $_SESSION["login"];

$stmt = $pdo->prepare("DELETE FROM komentarze WHERE id = ? AND nick = ?");
$stmt->execute([$id, $login]);

if ($stmt->rowCount() > 0) {
    echo "ok";
} else {
    echo "blad";
}
?>
