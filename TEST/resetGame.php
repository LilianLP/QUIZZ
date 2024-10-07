<?php
session_start();

// Réinitialiser les données de session
unset($_SESSION['players']);
unset($_SESSION['game_started']);
unset($_SESSION['players_points']);

// Optionnel : Réinitialiser le nom du joueur
unset($_SESSION['playerName']); // Si tu veux également vider le pseudo du joueur
?>