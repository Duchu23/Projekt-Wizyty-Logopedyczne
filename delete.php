<?php

require("admin_guard.php");
require("db.php");

$id = $_GET["id"] ?? 0;

$stmt = $pdo->prepare("SELECT zdjecie FROM cwiczenia WHERE id = ?");
$stmt->execute([$id]);
$cwiczenie = $stmt->fetch();

$stmt = $pdo->prepare("DELETE FROM cwiczenia WHERE id = ?");
$stmt->execute([$id]);

if ($cwiczenie && $cwiczenie["zdjecie"]) {
    $plik   = "zdjecia/" . $cwiczenie["zdjecie"];
    $thumb  = "zdjecia/thumbs/" . $cwiczenie["zdjecie"];

    if (file_exists($plik))  unlink($plik);   // file_exists chroni przed błędem
    if (file_exists($thumb)) unlink($thumb);
}

header("Location: cwiczenia.php");
exit;
?>
