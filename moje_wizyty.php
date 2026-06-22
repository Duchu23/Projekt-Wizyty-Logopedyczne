<?php
require("session.php");
require("db.php");

$id_uzytkownika = $_SESSION["id"];
$dzis = date("Y-m-d");

$stmt = $pdo->prepare(
    "SELECT w.id, w.data_wizyty, w.godzina, w.cel,
            s.imie_nazwisko, s.specjalizacja
     FROM wizyty w, specjalisci s
     WHERE w.id_specjalisty = s.id AND w.id_uzytkownika = ?
     ORDER BY w.data_wizyty, w.godzina"
);
$stmt->execute([$id_uzytkownika]);
$wizyty = $stmt->fetchAll();

$nadchodzace = [];
$minione = [];
foreach ($wizyty as $w) {
    if ($w["data_wizyty"] >= $dzis) {
        $nadchodzace[] = $w;
    } else {
        $minione[] = $w;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Moje wizyty – Gabinet Logopedyczny</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php require("header.php"); ?>

<main class="container">
    <h1>Moje wizyty</h1>

    <?php if (count($wizyty) === 0): ?>
        <p class="info">Nie masz jeszcze żadnych wizyt.
           <a href="umow_wizyte.php">Umów pierwszą wizytę</a>.</p>
    <?php else: ?>

        <h2>Nadchodzące</h2>
        <div id="lista-wizyt">
            <?php if (count($nadchodzace) === 0): ?>
                <p class="info">Brak nadchodzących wizyt.</p>
            <?php else: ?>
                <?php foreach ($nadchodzace as $w): ?>
                    <article class="wizyta" id="wizyta-<?= $w['id'] ?>">
                        <div class="wizyta-glowne">
                            <span class="wizyta-data">
                                <?= date("d.m.Y", strtotime($w['data_wizyty'])) ?>
                                o godz. <?= substr($w['godzina'], 0, 5) ?>
                            </span>
                            <span class="wizyta-spec">
                                <?= htmlspecialchars($w['imie_nazwisko']) ?>
                                – <?= htmlspecialchars($w['specjalizacja']) ?>
                            </span>
                        </div>
                        <?php if ($w["cel"]): ?>
                            <p class="wizyta-cel"><?= htmlspecialchars($w['cel']) ?></p>
                        <?php endif; ?>
                        <button class="anuluj-wizyte" data-wizyta-id="<?= $w['id'] ?>">
                            Odwołaj wizytę
                        </button>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (count($minione) > 0): ?>
            <h2>Minione</h2>
            <?php foreach ($minione as $w): ?>
                <article class="wizyta wizyta-miniona">
                    <div class="wizyta-glowne">
                        <span class="wizyta-data">
                            <?= date("d.m.Y", strtotime($w['data_wizyty'])) ?>
                            o godz. <?= substr($w['godzina'], 0, 5) ?>
                        </span>
                        <span class="wizyta-spec">
                            <?= htmlspecialchars($w['imie_nazwisko']) ?>
                        </span>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>

    <?php endif; ?>
</main>

<script src="scripts/jquery-3.7.1.min.js"></script>
<script src="scripts/app.js"></script>
</body>
</html>
