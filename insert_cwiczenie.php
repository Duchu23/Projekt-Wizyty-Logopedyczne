<?php

require("admin_guard.php");
require("db.php");

$kategorie = $pdo->query("SELECT id, nazwa FROM kategorie ORDER BY nazwa")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dodaj ćwiczenie – Gabinet Logopedyczny</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php require("header.php"); ?>

<main class="container">
    <a href="cwiczenia.php" class="powrot">← Wróć</a>
    <h1>Dodaj nowe ćwiczenie</h1>

    <form method="post" action="insert.php" enctype="multipart/form-data" class="form-duzy">
        <label>Tytuł
            <input type="text" name="tytul" required>
        </label>

        <label>Autor (logopeda)
            <input type="text" name="autor" required>
        </label>

        <label>Kategoria
            <select name="id_kategorii">
                <?php foreach ($kategorie as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nazwa']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Poziom trudności (1-5)
            <input type="number" name="poziom" min="1" max="5" value="1" required>
        </label>

        <label>Zalecany wiek
            <input type="text" name="wiek" placeholder="np. 4-7 lat">
        </label>

        <label>Zdjęcie (opcjonalne)
            <input type="file" name="zdjecie" accept="image/*">
        </label>

        <label>Opis
            <textarea name="opis" rows="5"></textarea>
        </label>

        <button type="submit">Dodaj ćwiczenie</button>
    </form>
</main>
</body>
</html>
