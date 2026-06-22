<?php

require("admin_guard.php");
require("db.php");

$id = $_GET["id"] ?? 0;

$stmt = $pdo->prepare("DELETE FROM wizyty WHERE id = ?");
$stmt->execute([$id]);

header("Location: wizyty_admin.php");
exit;
?>
