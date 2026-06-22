<?php

require("admin_guard.php");
require("db.php");

$dzis = date("Y-m-d");

$wizyty = $pdo->query(
    "SELECT w.id, w.data_wizyty, w.godzina, w.cel,
            u.login AS pacjent,
            s.imie_nazwisko AS specjalista
     FROM wizyty w, uzytkownicy u, specjalisci s
     WHERE w.id_uzytkownika = u.id AND w.id_specjalisty = s.id
     ORDER BY w.data_wizyty, w.godzina"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel wizyt – Gabinet Logopedyczny</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php require("header.php"); ?>

<main class="container">
    <h1>Panel wizyt (administrator)</h1>
    <p class="podtytul">Wszystkie zaplanowane i minione wizyty w gabinecie.</p>

    <?php if (count($wizyty) === 0): ?>
        <p class="info">W systemie nie ma jeszcze żadnych wizyt.</p>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Godzina</th>
                    <th>Pacjent</th>
                    <th>Specjalista</th>
                    <th>Cel</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($wizyty as $w): ?>
                <tr class="<?= $w['data_wizyty'] < $dzis ? 'wiersz-miniony' : '' ?>">
                    <td><?= date("d.m.Y", strtotime($w['data_wizyty'])) ?></td>
                    <td><?= substr($w['godzina'], 0, 5) ?></td>
                    <td><?= htmlspecialchars($w['pacjent']) ?></td>
                    <td><?= htmlspecialchars($w['specjalista']) ?></td>
                    <td><?= htmlspecialchars($w['cel']) ?></td>
                    <td class="akcje">
                        <a href="wizyta_admin_edit.php?id=<?= $w['id'] ?>">Edytuj</a>
                        <a href="wizyta_admin_delete.php?id=<?= $w['id'] ?>"
                           onclick="return confirm('Na pewno usunąć tę wizytę?')">Usuń</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
</body>
</html>
