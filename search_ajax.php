<?php

require("session.php");
require("db.php");

header("Content-Type: text/html; charset=utf-8");

$fraza = $_GET["fraza"] ?? "";

if ($fraza === "") {
    exit;
}

$stmt = $pdo->prepare(
    "SELECT c.id, c.tytul, k.nazwa AS kategoria
     FROM cwiczenia c, kategorie k
     WHERE c.id_kategorii = k.id AND c.tytul LIKE ?
     ORDER BY c.tytul"
);
$stmt->execute(["%" . $fraza . "%"]);
$wyniki = $stmt->fetchAll();

if (count($wyniki) === 0) {
    echo "<p class='info'>Brak wyników dla: " . htmlspecialchars($fraza) . "</p>";
} else {
    echo "<ul class='wyniki-lista'>";
    foreach ($wyniki as $w) {
        echo "<li><a href='details.php?id=" . $w["id"] . "'>"
           . htmlspecialchars($w["tytul"])
           . "</a> <span class='tag'>" . htmlspecialchars($w["kategoria"]) . "</span></li>";
    }
    echo "</ul>";
}
?>
