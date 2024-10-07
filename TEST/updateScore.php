<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['playerName']) && isset($data['score'])) {
        $_SESSION['players_points'][$data['playerName']] = (int)$data['score'];
        $_SESSION['score'] = (int)$data['score']; // Mise à jour du score dans la session
    }
}
?>