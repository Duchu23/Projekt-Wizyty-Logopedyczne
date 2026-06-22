<?php

require("session.php");
require("db.php");

$kategorie = $pdo->query("SELECT id, nazwa FROM kategorie ORDER BY nazwa")->fetchAll();

$sql = "SELECT c.id, c.tytul, c.autor, c.poziom, c.wiek, c.zdjecie, k.nazwa AS kategoria
        FROM cwiczenia c, kategorie k
        WHERE c.id_kategorii = k.id";
$parametry = [];

if (isset($_GET["id_kategorii"]) && $_GET["id_kategorii"] !== "") {
    $sql .= " AND c.id_kategorii = ?";
    $parametry[] = $_GET["id_kategorii"];
}

if (isset($_GET["fraza"]) && $_GET["fraza"] !== "") {
    $sql .= " AND c.tytul LIKE ?";
    $parametry[] = "%" . $_GET["fraza"] . "%";
}

$sql .= " ORDER BY c.tytul";

$stmt = $pdo->prepare($sql);
$stmt->execute($parametry);
$cwiczenia = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ćwiczenia – Gabinet Logopedyczny</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php require("header.php"); ?>

<main class="container">
    <h1>Ćwiczenia logopedyczne</h1>
    <p class="podtytul">Przykładowe ćwiczenia do pracy w domu. Kliknij tytuł, aby zobaczyć szczegóły.</p>

    <div class="kategorie-filtry">
        <a href="cwiczenia.php" class="filtr">Wszystkie</a>
        <?php foreach ($kategorie as $k): ?>
            <a href="cwiczenia.php?id_kategorii=<?= $k['id'] ?>" class="filtr">
                <?= htmlspecialchars($k['nazwa']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="szukaj-box">
        <input type="text" id="poleWyszukiwania" placeholder="Wpisz tytuł ćwiczenia...">
        <div id="wyniki-szukania"></div>
    </div>

    <?php if (count($cwiczenia) === 0): ?>
        <p class="info">Brak ćwiczeń spełniających kryteria.</p>
    <?php else: ?>
        <div class="kostki">
            <?php foreach ($cwiczenia as $c): ?>
                <div class="kostka">
                    <a class="kostka-link" href="details.php?id=<?= $c['id'] ?>">
                        <?php if ($c["zdjecie"]): ?>
                            <img class="kostka-foto" src="zdjecia/thumbs/<?= htmlspecialchars($c['zdjecie']) ?>" alt="">
                        <?php else: ?>
                            <img class="kostka-foto" src="images/brak-zdjecia.png" alt="Brak zdjęcia">
                        <?php endif; ?>
                        <div class="kostka-tresc">
                            <span class="kostka-kat"><?= htmlspecialchars($c['kategoria']) ?></span>
                            <h3 class="kostka-tytul"><?= htmlspecialchars($c['tytul']) ?></h3>
                            <div class="kostka-poziom"><?= str_repeat("⭐", $c['poziom']) ?></div>
                            <p class="kostka-autor"><?= htmlspecialchars($c['autor']) ?></p>
                        </div>
                    </a>

                    <?php if (!empty($_SESSION["is_admin"])): ?>
                        <div class="kostka-akcje">
                            <a href="edit.php?id=<?= $c['id'] ?>">Edytuj</a>
                            <a href="delete.php?id=<?= $c['id'] ?>"
                               onclick="return confirm('Na pewno usunąć to ćwiczenie?')">Usuń</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script src="scripts/jquery-3.7.1.min.js"></script>
<script src="scripts/app.js"></script>
</body>
</html>
