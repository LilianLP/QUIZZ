<?php
$host = 'localhost'; // L'hôte de la base de données
$db = 'films'; // Nom de la base de données
$user = 'root'; // Nom d'utilisateur
$pass = ''; // Mot de passe (laisser vide si c'est le cas)

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Création de la table films si elle n'existe pas déjà
    $sql = "CREATE TABLE IF NOT EXISTS films (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        titres VARCHAR(255) NOT NULL,
        affiches VARCHAR(255) NOT NULL,
        notes FLOAT NOT NULL,
        critiques TEXT NOT NULL
    )";
    $pdo->exec($sql);

    // Création de la table scores si elle n'existe pas déjà
    $sqlScores = "CREATE TABLE IF NOT EXISTS scores (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        playerName VARCHAR(255) NOT NULL,
        score INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlScores);

    // Lecture du fichier CSV
    $file = fopen('film.csv', 'r');
    fgetcsv($file); // Ignorer l'en-tête

    while (($data = fgetcsv($file)) !== FALSE) {
        list($titre, $affiche, $note, $critique) = $data;

        // Vérifier si le film existe déjà
        $checkSql = "SELECT COUNT(*) FROM films WHERE titres = :titre";
        $stmt = $pdo->prepare($checkSql);
        $stmt->execute(['titre' => $titre]);
        $exists = $stmt->fetchColumn();

        if ($exists == 0) {
            // Insertion du film
            $sql = "INSERT INTO films (titres, affiches, notes, critiques) VALUES (:titre, :affiche, :note, :critique)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['titre' => $titre, 'affiche' => $affiche, 'note' => $note, 'critique' => $critique]);
        }
    }

    fclose($file); // Fermer le fichier CSV
} catch (PDOException $e) {
    error_log("Erreur de connexion : " . $e->getMessage());
}
?>