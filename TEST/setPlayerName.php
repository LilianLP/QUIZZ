<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['playerName'])) {
        $_SESSION['playerName'] = htmlspecialchars($data['playerName']); // Stocker le pseudo dans la session
        $_SESSION['score'] = 0; // Initialiser le score
        // Initialiser le tableau des scores des joueurs
        if (!isset($_SESSION['players_points'])) {
            $_SESSION['players_points'] = [];
        }
        $_SESSION['players_points'][$_SESSION['playerName']] = 0; // Initialiser le score à 0
    }
}
?>