<?php
session_start();

if (isset($_GET['game_id'])) {
    $_SESSION['game_id'] = $_GET['game_id'];
} else {
    header("Location: index.php"); // Rediriger si l'ID de jeu n'est pas fourni
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Rejoindre une Partie</title>
</head>
<body>
    <h1>Rejoindre la Partie</h1>
    <p>ID de la Partie : <?php echo $_SESSION['game_id']; ?></p>
    <input type="text" id="playerName" placeholder="Entrez votre pseudo" required>
    <button id="joinBtn">Rejoindre</button>

    <script>
        document.getElementById('joinBtn').onclick = () => {
            const playerName = document.getElementById('playerName').value;
            if (playerName.trim() === "") {
                alert("Veuillez entrer un pseudo.");
                return;
            }

            // Enregistrer le pseudo dans la session
            fetch('setPlayerName.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ playerName })
            }).then(() => {
                // Redirection vers la page de la partie
                window.location.href = 'createGame.php';
            });
        };
    </script>
</body>
</html>