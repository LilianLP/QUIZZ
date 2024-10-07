<?php
session_start();
include 'db.php';

// Lire les films depuis la base de données
$stmt = $pdo->query("SELECT titres, affiches, critiques FROM films");
$films = $stmt->fetchAll(PDO::FETCH_ASSOC);

// S'assurer qu'il y a suffisamment de films
if (count($films) < 4) {
    die("Il faut au moins 4 films pour créer le quiz.");
}

// Vérifier si tous les films ont été utilisés
if (!isset($_SESSION['used_titles'])) {
    $_SESSION['used_titles'] = [];
}

// Réinitialiser les titres si tous les films ont été utilisés
if (count($_SESSION['used_titles']) >= count($films)) {
    $_SESSION['used_titles'] = [];
}

// Trouver un film non utilisé
do {
    $randomIndex = array_rand($films);
    $selectedFilm = $films[$randomIndex];
} while (in_array($selectedFilm['titres'], $_SESSION['used_titles']));

// Enregistrer le titre du film utilisé
$_SESSION['used_titles'][] = $selectedFilm['titres'];

// Filtrer les autres films (assure-toi qu'il en reste trois)
$otherOptions = array_filter($films, function($film) use ($selectedFilm) {
    return $film['titres'] !== $selectedFilm['titres'];
});

// Mélanger les autres films et prendre trois options
shuffle($otherOptions);
$otherOptions = array_slice($otherOptions, 0, 3);

// Combiner le film sélectionné avec les autres pour créer un tableau de réponses
$options = array_merge([$selectedFilm], $otherOptions);
shuffle($options); // Mélanger les options

// Retourner le film sélectionné et les autres films
echo json_encode([
    'selectedFilm' => $selectedFilm,
    'options' => $options
]);
?>