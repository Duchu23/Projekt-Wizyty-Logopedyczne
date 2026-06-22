<?php

?>
<header class="topbar">
    <div class="logo"><a href="index.php">🗣️ Gabinet Logopedyczny</a></div>
    <nav class="menu">
        <a href="index.php">Strona główna</a>
        <a href="umow_wizyte.php">Umów wizytę</a>
        <a href="moje_wizyty.php">Moje wizyty</a>
        <a href="cwiczenia.php">Ćwiczenia</a>

        <?php // Link administracyjny – widoczny TYLKO dla logopedy (admina). ?>
        <?php if (!empty($_SESSION["is_admin"]) && $_SESSION["is_admin"] == 1): ?>
            <a href="wizyty_admin.php" class="admin-link">Panel wizyt</a>
            <a href="insert_cwiczenie.php" class="admin-link">+ Dodaj ćwiczenie</a>
        <?php endif; ?>

        <span class="witaj">Witaj, <?= htmlspecialchars($_SESSION["login"]) ?>!</span>
        <a href="logout.php" class="logout">Wyloguj</a>
    </nav>
</header>
