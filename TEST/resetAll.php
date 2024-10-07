<?php
session_start();

// Détruire toutes les sessions
session_destroy();

// Rediriger vers la page d'accueil (facultatif)
header('Location: index.php');
exit();
?>