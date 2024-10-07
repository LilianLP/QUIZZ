<?php
session_start();

// Vérifier si la partie est en cours et s'il y a des joueurs
if (isset($_SESSION['players'])) {
    echo json_encode($_SESSION['players']);
} else {
    echo json_encode([]);
}
?>