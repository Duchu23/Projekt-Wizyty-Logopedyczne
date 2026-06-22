<?php

require("session.php");
require("db.php");

$id_cwiczenia = $_POST["id_cwiczenia"];
$ocena        = $_POST["ocena"];
$tresc        = $_POST["tresc"];
$nick         = $_SESSION["login"];

$stmt = $pdo->prepare(
    "INSERT INTO komentarze (id_cwiczenia, nick, ocena, tresc)
     VALUES (?, ?, ?, ?)"
);
$stmt->execute([$id_cwiczenia, $nick, $ocena, $tresc]);

header("Location: details.php?id=" . $id_cwiczenia);
exit;
?>
