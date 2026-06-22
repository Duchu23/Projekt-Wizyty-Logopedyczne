<?php

require("admin_guard.php");
require("db.php");

$id = $_GET["id"] ?? ($_POST["id"] ?? 0);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $tytul        = $_POST["tytul"];
    $autor        = $_POST["autor"];
    $id_kategorii = $_POST["id_kategorii"];
    $poziom       = $_POST["poziom"];
    $wiek         = $_POST["wiek"];
    $opis         = $_POST["opis"];

    if (isset($_FILES["zdjecie"]) && $_FILES["zdjecie"]["error"] === 0) {
        $nazwaPliku = basename($_FILES["zdjecie"]["name"]);
        $sciezka = "zdjecia/" . $nazwaPliku;
        move_uploaded_file($_FILES["zdjecie"]["tmp_name"], $sciezka);
        generujMiniature($sciezka, "zdjecia/thumbs/" . $nazwaPliku, 200);

        $stmt = $pdo->prepare(
            "UPDATE cwiczenia
             SET id_kategorii=?, tytul=?, autor=?, poziom=?, wiek=?, opis=?, zdjecie=?
             WHERE id=?"
        );
        $stmt->execute([$id_kategorii, $tytul, $autor, $poziom, $wiek, $opis, $nazwaPliku, $id]);
    } else {
        $stmt = $pdo->prepare(
            "UPDATE cwiczenia
             SET id_kategorii=?, tytul=?, autor=?, poziom=?, wiek=?, opis=?
             WHERE id=?"
        );
        $stmt->execute([$id_kategorii, $tytul, $autor, $poziom, $wiek, $opis, $id]);
    }

    header("Location: details.php?id=" . $id);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM cwiczenia WHERE id = ?");
$stmt->execute([$id]);
$cwiczenie = $stmt->fetch();

if (!$cwiczenie) {
    echo "<p>Nie znaleziono ćwiczenia. <a href='index.php'>Wróć</a></p>";
    exit;
}

$kategorie = $pdo->query("SELECT id, nazwa FROM kategorie ORDER BY nazwa")->fetchAll();

function generujMiniature($zrodlo, $cel, $szerokoscMin) {
    $info = getimagesize($zrodlo);
    if (!$info) return;
    $szerokosc = $info[0]; $wysokosc = $info[1]; $typ = $info[2];
    if ($typ === IMAGETYPE_JPEG)      $obrazek = imagecreatefromjpeg($zrodlo);
    elseif ($typ === IMAGETYPE_PNG)   $obrazek = imagecreatefrompng($zrodlo);
    else return;
    $nowaSzerokosc = $szerokoscMin;
    $nowaWysokosc  = (int) round($wysokosc * ($nowaSzerokosc / $szerokosc));
    $miniatura = imagecreatetruecolor($nowaSzerokosc, $nowaWysokosc);
    imagecopyresampled($miniatura, $obrazek, 0,0,0,0, $nowaSzerokosc,$nowaWysokosc, $szerokosc,$wysokosc);
    imagejpeg($miniatura, $cel, 80);
    imagedestroy($obrazek); imagedestroy($miniatura);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edytuj ćwiczenie – Gabinet Logopedyczny</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php require("header.php"); ?>

<main class="container">
    <a href="details.php?id=<?= $cwiczenie['id'] ?>" class="powrot">← Wróć</a>
    <h1>Edytuj ćwiczenie</h1>

    <form method="post" action="edit.php" enctype="multipart/form-data" class="form-duzy">
        <input type="hidden" name="id" value="<?= $cwiczenie['id'] ?>">

        <label>Tytuł
            <input type="text" name="tytul" value="<?= htmlspecialchars($cwiczenie['tytul']) ?>" required>
        </label>

        <label>Autor
            <input type="text" name="autor" value="<?= htmlspecialchars($cwiczenie['autor']) ?>" required>
        </label>

        <label>Kategoria
            <select name="id_kategorii">
                <?php foreach ($kategorie as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $k['id'] == $cwiczenie['id_kategorii'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nazwa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Poziom trudności (1-5)
            <input type="number" name="poziom" min="1" max="5" value="<?= $cwiczenie['poziom'] ?>" required>
        </label>

        <label>Zalecany wiek
            <input type="text" name="wiek" value="<?= htmlspecialchars($cwiczenie['wiek']) ?>">
        </label>

        <label>Nowe zdjęcie (zostaw puste, aby nie zmieniać)
            <input type="file" name="zdjecie" accept="image/*">
        </label>

        <label>Opis
            <textarea name="opis" rows="5"><?= htmlspecialchars($cwiczenie['opis']) ?></textarea>
        </label>

        <button type="submit">Zapisz zmiany</button>
    </form>
</main>
</body>
</html>
