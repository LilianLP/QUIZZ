<?php
session_start();
include 'db.php';

// Vérifiez si le formulaire a été soumis par le créateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les scores des joueurs
    $stmt = $pdo->query("SELECT playerName, score FROM scores ORDER BY score DESC");
    $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Rediriger vers le quiz si accès direct
    header("Location: quizz.php");
    exit();
}

// Vérifie si le bouton "Rejouer" a été cliqué
if (isset($_POST['replay'])) {
    // Réinitialiser la table des scores
    $pdo->exec("TRUNCATE TABLE scores"); // Effacer les scores de la table

    // Réinitialiser toutes les données de la session
    session_destroy(); // Détruire toutes les sessions
    header("Location: index.php"); // Rediriger vers l'accueil après réinitialisation
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats du Quiz</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Tableau des Scores</h1>
    <table>
        <thead>
            <tr>
                <th>Pseudo</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($scores as $score): ?>
                <tr>
                    <td><?php echo htmlspecialchars($score['playerName']); ?></td>
                    <td><?php echo htmlspecialchars($score['score']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Bouton pour rejouer -->
    <form method="POST">
        <button type="submit" name="replay">Rejouer</button>
    </form>
</body>
</html>