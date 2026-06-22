<?php

require("session.php");
require("db.php");

$id = $_GET["id"] ?? 0;

$stmt = $pdo->prepare(
    "SELECT c.*, k.nazwa AS kategoria
     FROM cwiczenia c, kategorie k
     WHERE c.id_kategorii = k.id AND c.id = ?"
);
$stmt->execute([$id]);
$cwiczenie = $stmt->fetch();

if (!$cwiczenie) {
    echo "<p>Nie znaleziono ćwiczenia. <a href='index.php'>Wróć</a></p>";
    exit;
}

$stmt = $pdo->prepare("SELECT AVG(ocena) AS srednia FROM komentarze WHERE id_cwiczenia = ?");
$stmt->execute([$id]);
$srednia = $stmt->fetch()["srednia"];

$stmt = $pdo->prepare("SELECT 1 FROM ulubione WHERE id_cwiczenia = ? AND id_uzytkownika = ?");
$stmt->execute([$id, $_SESSION["id"]]);
$polubione = $stmt->fetch() ? 1 : 0;

$stmt = $pdo->prepare("SELECT * FROM komentarze WHERE id_cwiczenia = ? ORDER BY data_dodania DESC");
$stmt->execute([$id]);
$komentarze = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($cwiczenie['tytul']) ?> – Gabinet Logopedyczny</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php require("header.php"); ?>

<main class="container">
    <a href="cwiczenia.php" class="powrot">← Wróć do listy ćwiczeń</a>

    <div class="szczegoly">
        <div class="szczegoly-foto">
            <?php if ($cwiczenie["zdjecie"]): ?>
                <img src="zdjecia/<?= htmlspecialchars($cwiczenie['zdjecie']) ?>" alt="">
            <?php else: ?>
                <img src="images/brak-zdjecia.png" alt="Brak zdjęcia">
            <?php endif; ?>
        </div>

        <div class="szczegoly-dane">
            <h1><?= htmlspecialchars($cwiczenie['tytul']) ?></h1>

            <table class="meta">
                <tr><th>Autor</th><td><?= htmlspecialchars($cwiczenie['autor']) ?></td></tr>
                <tr><th>Poziom trudności</th><td><?= str_repeat("⭐", $cwiczenie['poziom']) ?></td></tr>
                <tr><th>Zalecany wiek</th><td><?= htmlspecialchars($cwiczenie['wiek']) ?></td></tr>
                <tr>
                    <th>Kategoria</th>
                    <td>
                        <?php // Szybka nawigacja: kategoria jako link filtrujący ?>
                        <a href="cwiczenia.php?id_kategorii=<?= $cwiczenie['id_kategorii'] ?>">
                            <?= htmlspecialchars($cwiczenie['kategoria']) ?>
                        </a>
                    </td>
                </tr>
            </table>

            <p class="opis"><?= nl2br(htmlspecialchars($cwiczenie['opis'])) ?></p>

            <p class="srednia">
                <?php if ($srednia): ?>
                    Średnia ocen: <strong><?= round($srednia, 1) ?> / 5</strong>
                <?php else: ?>
                    Brak ocen
                <?php endif; ?>
            </p>

            <button class="fav-toggle"
                    data-cwiczenie-id="<?= $cwiczenie['id'] ?>"
                    data-liked="<?= $polubione ?>">
                <?= $polubione ? "❤️ Usuń z ulubionych" : "🤍 Dodaj do ulubionych" ?>
            </button>
        </div>
    </div>

    <section class="komentarze">
        <h2>Dodaj komentarz</h2>
        <form method="post" action="insert_comment.php" class="form-komentarz">
            <input type="hidden" name="id_cwiczenia" value="<?= $cwiczenie['id'] ?>">

            <label>Ocena
                <select name="ocena">
                    <option value="5">5 – świetne</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1 – słabe</option>
                </select>
            </label>

            <label>Treść
                <textarea name="tresc" rows="3" placeholder="Napisz, jak sprawdziło się ćwiczenie..."></textarea>
            </label>

            <button type="submit">Dodaj komentarz</button>
        </form>

        <h2>Komentarze (<?= count($komentarze) ?>)</h2>
        <?php if (count($komentarze) === 0): ?>
            <p class="info">Brak komentarzy. Bądź pierwszy!</p>
        <?php else: ?>
            <?php foreach ($komentarze as $kom): ?>
                <article class="komentarz">
                    <div class="komentarz-naglowek">
                        <strong><?= htmlspecialchars($kom['nick']) ?></strong>
                        <span class="ocena"><?= str_repeat("⭐", $kom['ocena']) ?></span>
                        <span class="data"><?= date("d.m.Y H:i", strtotime($kom['data_dodania'])) ?></span>
                    </div>
                    <p><?= nl2br(htmlspecialchars($kom['tresc'])) ?></p>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<script src="scripts/jquery-3.7.1.min.js"></script>
<script src="scripts/app.js"></script>
</body>
</html>
