<?php

require("session.php");
require("db.php");

$login = $_SESSION["login"];

$stmt = $pdo->prepare(
    "SELECT k.id, k.ocena, k.tresc, k.data_dodania, c.id AS id_cwiczenia, c.tytul
     FROM komentarze k, cwiczenia c
     WHERE k.id_cwiczenia = c.id AND k.nick = ?
     ORDER BY k.data_dodania DESC"
);
$stmt->execute([$login]);
$komentarze = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Moje komentarze – Gabinet Logopedyczny</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php require("header.php"); ?>

<main class="container">
    <a href="cwiczenia.php" class="powrot">← Wróć</a>
    <h1>Moje komentarze</h1>

    <?php if (count($komentarze) === 0): ?>
        <p class="info">Nie dodałeś jeszcze żadnego komentarza.</p>
    <?php else: ?>
        <div id="lista-komentarzy">
            <?php foreach ($komentarze as $kom): ?>
                <article class="komentarz" id="komentarz-<?= $kom['id'] ?>">
                    <div class="komentarz-naglowek">
                        <a href="details.php?id=<?= $kom['id_cwiczenia'] ?>">
                            <strong><?= htmlspecialchars($kom['tytul']) ?></strong>
                        </a>
                        <span class="ocena"><?= str_repeat("⭐", $kom['ocena']) ?></span>
                        <span class="data"><?= date("d.m.Y H:i", strtotime($kom['data_dodania'])) ?></span>
                    </div>
                    <p><?= nl2br(htmlspecialchars($kom['tresc'])) ?></p>

                    <button class="delete-review" data-review-id="<?= $kom['id'] ?>">
                        Usuń komentarz
                    </button>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script src="scripts/jquery-3.7.1.min.js"></script>
<script src="scripts/app.js"></script>
</body>
</html>
