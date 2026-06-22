<?php

require("admin_guard.php");
require("db.php");

$tytul        = $_POST["tytul"];
$autor        = $_POST["autor"];
$id_kategorii = $_POST["id_kategorii"];
$poziom       = $_POST["poziom"];
$wiek         = $_POST["wiek"];
$opis         = $_POST["opis"];

$nazwaPliku = null;

if (isset($_FILES["zdjecie"]) && $_FILES["zdjecie"]["error"] === 0) {

    $nazwaPliku = basename($_FILES["zdjecie"]["name"]);
    $sciezkaDocelowa = "zdjecia/" . $nazwaPliku;

    move_uploaded_file($_FILES["zdjecie"]["tmp_name"], $sciezkaDocelowa);

    generujMiniature($sciezkaDocelowa, "zdjecia/thumbs/" . $nazwaPliku, 200);
}

$stmt = $pdo->prepare(
    "INSERT INTO cwiczenia (id_kategorii, tytul, autor, poziom, wiek, zdjecie, opis)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->execute([$id_kategorii, $tytul, $autor, $poziom, $wiek, $nazwaPliku, $opis]);

header("Location: cwiczenia.php");
exit;

function generujMiniature($zrodlo, $cel, $szerokoscMin) {
    $info = getimagesize($zrodlo);
    if (!$info) return;

    $szerokosc = $info[0];
    $wysokosc  = $info[1];
    $typ       = $info[2];

    if ($typ === IMAGETYPE_JPEG) {
        $obrazek = imagecreatefromjpeg($zrodlo);
    } elseif ($typ === IMAGETYPE_PNG) {
        $obrazek = imagecreatefrompng($zrodlo);
    } else {
        return;
    }

    $nowaSzerokosc = $szerokoscMin;
    $nowaWysokosc  = (int) round($wysokosc * ($nowaSzerokosc / $szerokosc));

    $miniatura = imagecreatetruecolor($nowaSzerokosc, $nowaWysokosc);
    imagecopyresampled(
        $miniatura, $obrazek,
        0, 0, 0, 0,
        $nowaSzerokosc, $nowaWysokosc,
        $szerokosc, $wysokosc
    );

    imagejpeg($miniatura, $cel, 80);
    imagedestroy($obrazek);
    imagedestroy($miniatura);
}
?>
