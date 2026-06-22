<?php

require("admin_guard.php");
require("db.php");

$id = $_GET["id"] ?? $_POST["id"] ?? 0;
$godziny = ["08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00"];
$specjalisci = $pdo->query("SELECT id, imie_nazwisko FROM specjalisci ORDER BY imie_nazwisko")->fetchAll();
$komunikat = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_specjalisty = $_POST["id_specjalisty"];
    $data_wizyty    = $_POST["data_wizyty"];
    $godzina        = $_POST["godzina"];
    $cel            = $_POST["cel"];

    $stmt = $pdo->prepare(
        "SELECT id FROM wizyty
         WHERE id_specjalisty = ? AND data_wizyty = ? AND godzina = ? AND id != ?"
    );
    $stmt->execute([$id_specjalisty, $data_wizyty, $godzina, $id]);

    if ($stmt->fetch()) {
        $komunikat = "Ten termin jest już zajęty u wybranego specjalisty.";
    } else {
        $stmt = $pdo->prepare(
            "UPDATE wizyty
             SET id_specjalisty = ?, data_wizyty = ?, godzina = ?, cel = ?
             WHERE id = ?"
        );
        $stmt->execute([$id_specjalisty, $data_wizyty, $godzina, $cel, $id]);
        header("Location: wizyty_admin.php");
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM wizyty WHERE id = ?");
$stmt->execute([$id]);
$wizyta = $stmt->fetch();

if (!$wizyta) {
    echo "<p>Nie znaleziono wizyty. <a href='wizyty_admin.php'>Wróć</a></p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edycja wizyty – Gabinet Logopedyczny</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php require("header.php"); ?>

<main class="container">
    <h1>Edycja wizyty</h1>
    <a href="wizyty_admin.php" class="powrot">← Wróć do panelu wizyt</a>

    <?php if ($komunikat): ?>
        <p class="komunikat komunikat-blad"><?= $komunikat ?></p>
    <?php endif; ?>

    <form method="post" action="wizyta_admin_edit.php" class="form-duzy">
        <input type="hidden" name="id" value="<?= $wizyta['id'] ?>">

        <label>Specjalista
            <select name="id_specjalisty" required>
                <?php foreach ($specjalisci as $s): ?>
                    <option value="<?= $s['id'] ?>"
                        <?= $s['id'] == $wizyta['id_specjalisty'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['imie_nazwisko']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Data wizyty
            <input type="date" name="data_wizyty" value="<?= $wizyta['data_wizyty'] ?>" required>
        </label>

        <label>Godzina
            <select name="godzina" required>
                <?php foreach ($godziny as $g): ?>
                    <option value="<?= $g ?>"
                        <?= $g == substr($wizyta['godzina'], 0, 5) ? 'selected' : '' ?>>
                        <?= $g ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Cel wizyty
            <textarea name="cel" rows="3"><?= htmlspecialchars($wizyta['cel']) ?></textarea>
        </label>

        <button type="submit">Zapisz zmiany</button>
    </form>
</main>
</body>
</html>
