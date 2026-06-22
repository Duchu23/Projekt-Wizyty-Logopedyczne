<?php

require("session.php");
require("db.php");

$godziny = ["08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00"];

$specjalisci = $pdo->query("SELECT id, imie_nazwisko, specjalizacja FROM specjalisci ORDER BY imie_nazwisko")->fetchAll();

$komunikat = "";
$sukces = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_specjalisty = $_POST["id_specjalisty"];
    $data_wizyty    = $_POST["data_wizyty"];
    $godzina        = $_POST["godzina"];
    $cel            = $_POST["cel"];
    $id_uzytkownika = $_SESSION["id"];

    if ($data_wizyty < date("Y-m-d")) {
        $komunikat = "Nie można umówić wizyty na datę z przeszłości.";
    } elseif (!in_array($godzina, $godziny)) {
        $komunikat = "Wybierz poprawną godzinę wizyty.";
    } else {
        $stmt = $pdo->prepare(
            "SELECT id FROM wizyty
             WHERE id_specjalisty = ? AND data_wizyty = ? AND godzina = ?"
        );
        $stmt->execute([$id_specjalisty, $data_wizyty, $godzina]);

        if ($stmt->fetch()) {
            $komunikat = "Ten termin jest już zajęty. Wybierz inną godzinę.";
        } else {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO wizyty (id_uzytkownika, id_specjalisty, data_wizyty, godzina, cel)
                     VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([$id_uzytkownika, $id_specjalisty, $data_wizyty, $godzina, $cel]);
                $sukces = true;
                $komunikat = "Wizyta została umówiona! Zobacz ją w zakładce
                              <a href='moje_wizyty.php'>Moje wizyty</a>.";
            } catch (PDOException $e) {
                $komunikat = "Nie udało się umówić wizyty – termin mógł zostać właśnie zajęty.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Umów wizytę – Gabinet Logopedyczny</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php require("header.php"); ?>

<main class="container">
    <h1>Umów wizytę</h1>

    <?php if ($komunikat): ?>
        <p class="komunikat <?= $sukces ? 'komunikat-ok' : 'komunikat-blad' ?>"><?= $komunikat ?></p>
    <?php endif; ?>

    <form method="post" action="umow_wizyte.php" class="form-duzy form-wizyta">
        <label>Specjalista
            <select name="id_specjalisty" id="pole-specjalista" required>
                <?php foreach ($specjalisci as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= htmlspecialchars($s['imie_nazwisko']) ?>
                        (<?= htmlspecialchars($s['specjalizacja']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Data wizyty
            <input type="date" name="data_wizyty" id="pole-data"
                   min="<?= date('Y-m-d') ?>" required>
        </label>

        <label>Godzina
            <select name="godzina" id="pole-godzina" required>
                <?php foreach ($godziny as $g): ?>
                    <option value="<?= $g ?>"><?= $g ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <p id="info-terminy" class="info-terminy"></p>

        <label>Cel wizyty (opcjonalnie)
            <textarea name="cel" rows="3" placeholder="np. konsultacja, terapia głoski R..."></textarea>
        </label>

        <button type="submit">Zarezerwuj termin</button>
    </form>
</main>

<script src="scripts/jquery-3.7.1.min.js"></script>
<script src="scripts/app.js"></script>
</body>
</html>
