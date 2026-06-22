<?php

require("session.php");
require("db.php");

header("Content-Type: application/json; charset=utf-8");

$id_specjalisty = $_GET["id_specjalisty"] ?? 0;
$data_wizyty    = $_GET["data"] ?? "";

$stmt = $pdo->prepare(
    "SELECT godzina FROM wizyty WHERE id_specjalisty = ? AND data_wizyty = ?"
);
$stmt->execute([$id_specjalisty, $data_wizyty]);

$zajete = [];
foreach ($stmt->fetchAll() as $w) {
    $zajete[] = substr($w["godzina"], 0, 5);
}

echo json_encode($zajete);
?>
