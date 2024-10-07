<?php
session_start();

// Vérifie si une partie est active
$activeGame = isset($_SESSION['game_id']); // Une partie est active si l'ID de la partie existe

echo json_encode(['activeGame' => $activeGame]);
?>