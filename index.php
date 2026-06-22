<?php

require("session.php");
require("db.php");

$specjalisci = $pdo->query("SELECT * FROM specjalisci ORDER BY imie_nazwisko")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Strona główna – Gabinet Logopedyczny</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php require("header.php"); ?>

<main class="container">

    <section class="hero">
        <h1>Witamy w Gabinecie Logopedycznym „Słowik"</h1>
        <p>Profesjonalna diagnoza i terapia mowy dla dzieci i dorosłych.
           Umów wizytę online i ćwicz w domu z naszymi materiałami.</p>
        <div class="hero-akcje">
            <a href="umow_wizyte.php" class="przycisk-glowny">Umów wizytę</a>
            <a href="cwiczenia.php" class="przycisk-poboczny">Przeglądaj ćwiczenia</a>
        </div>
    </section>

    <section class="info-klinika">
        <h2>O gabinecie</h2>
        <div class="info-karty">
            <div class="info-karta">
                <h3>📍 Adres</h3>
                <p>ul. Mowy Polskiej 12<br>08-110 Siedlce</p>
            </div>
            <div class="info-karta">
                <h3>🕐 Godziny otwarcia</h3>
                <p>Pon – Pt: 8:00 – 17:00<br>Sobota: 9:00 – 13:00</p>
            </div>
            <div class="info-karta">
                <h3>📞 Kontakt</h3>
                <p>tel. 25 123 45 67<br>kontakt@gabinet-slowik.pl</p>
            </div>
        </div>
    </section>

    <section class="zespol">
        <h2>Nasi specjaliści</h2>
        <div class="karty">
            <?php foreach ($specjalisci as $s): ?>
                <div class="karta karta-specjalista">
                    <h3><?= htmlspecialchars($s["imie_nazwisko"]) ?></h3>
                    <p class="tytul-spec"><?= htmlspecialchars($s["tytul"]) ?></p>
                    <p class="specjalizacja"><?= htmlspecialchars($s["specjalizacja"]) ?></p>
                    <p class="opis-spec"><?= htmlspecialchars($s["opis"]) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

</main>
</body>
</html>
