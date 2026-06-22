<?php

require("session.php");
require("db.php");

$id_uzytkownika = $_SESSION["id"];

$stmt = $pdo->prepare(
    "SELECT c.id, c.tytul, c.autor, c.zdjecie, k.nazwa AS kategoria
     FROM ulubione u, cwiczenia c, kategorie k
     WHERE u.id_cwiczenia = c.id
       AND c.id_kategorii = k.id
       AND u.id_uzytkownika = ?
     ORDER BY c.tytul"
);
$stmt->execute([$id_uzytkownika]);
$ulubione = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ulubione – Gabinet Logopedyczny</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php require("header.php"); ?>

<main class="container">
    <a href="cwiczenia.php" class="powrot">← Wróć</a>
    <h1>Ulubione ćwiczenia</h1>

    <?php if (count($ulubione) === 0): ?>
        <p class="info">Nie masz jeszcze ulubionych ćwiczeń. Dodaj je na stronie szczegółów!</p>
    <?php else: ?>
        <div class="karty">
            <?php foreach ($ulubione as $c): ?>
                <div class="karta">
                    <?php if ($c["zdjecie"]): ?>
                        <img src="zdjecia/thumbs/<?= htmlspecialchars($c['zdjecie']) ?>" alt="">
                    <?php else: ?>
                        <img src="images/brak-zdjecia.png" alt="Brak zdjęcia">
                    <?php endif; ?>
                    <h3><a href="details.php?id=<?= $c['id'] ?>"><?= htmlspecialchars($c['tytul']) ?></a></h3>
                    <p class="karta-kategoria"><?= htmlspecialchars($c['kategoria']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script src="scripts/jquery-3.7.1.min.js"></script>
<script src="scripts/app.js"></script>
</body>
</html>
