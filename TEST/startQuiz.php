<?php
session_start();
include 'db.php';

if ($_SESSION['playerName'] !== $_SESSION['creator']) {
    echo json_encode(['status' => 'error', 'message' => 'Vous n\'êtes pas le créateur de la partie.']);
    exit();
}

$_SESSION['players_points'] = [];

if (isset($_SESSION['players'])) {
    foreach ($_SESSION['players'] as $player) {
        $_SESSION['players_points'][$player] = 0;
    }
}

echo json_encode(['status' => 'success']);
?>