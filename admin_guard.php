<?php

require("session.php");

if (empty($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    echo "<h1>Brak uprawnień</h1>";
    echo "<p>Ta strona jest dostępna tylko dla logopedy (administratora).</p>";
    echo "<p><a href='index.php'>Wróć do strony głównej</a></p>";
    exit;
}
?>
