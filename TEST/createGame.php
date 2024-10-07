<?php
session_start(); // Assure-toi que c'est la première ligne de ton fichier

// Initialiser les valeurs pour la session
if (!isset($_SESSION['game_id'])) {
    $_SESSION['game_id'] = uniqid(); // Créer un identifiant unique pour la partie
}

if (!isset($_SESSION['playerName'])) {
    header("Location: index.php"); // Rediriger si le joueur n'a pas de pseudo
}

// Définir le créateur de la partie
if (!isset($_SESSION['creator'])) {
    $_SESSION['creator'] = $_SESSION['playerName']; // Définir le créateur de la partie
}

// Ajouter le joueur à la liste
if (!isset($_SESSION['players'])) {
    $_SESSION['players'] = []; // Initialiser le tableau des joueurs
}

if (!in_array($_SESSION['playerName'], $_SESSION['players'])) {
    $_SESSION['players'][] = $_SESSION['playerName'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Créer une Partie</title>
</head>
<body>
    <h1>Créer une Partie</h1>
    <p>Votre ID de partie : <?php echo htmlspecialchars($_SESSION['game_id']); ?></p>
    
    <button id="copyLinkBtn">Copier le lien d'invitation</button>

    <h2>Joueurs Invités :</h2>
    <ul id="playerList">
        <?php
        foreach ($_SESSION['players'] as $player) {
            echo "<li>" . htmlspecialchars($player) . "</li>";
        }
        ?>
    </ul>

    <button id="startGameBtn">Lancer la Partie</button>

<script>
    document.getElementById('startGameBtn').onclick = () => {
        fetch('startQuiz.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ gameId: '<?php echo $_SESSION['game_id']; ?>' })
        }).then(response => response.json()).then(data => {
            if (data.status === 'success') {
                window.location.href = 'quizz.php';
            } else {
                alert(data.message);
            }
        });
    };

    document.getElementById('copyLinkBtn').onclick = () => {
        const linkToCopy = `Localhost:3000/joinGame.php?game_id=<?php echo htmlspecialchars($_SESSION['game_id']); ?>`;
        navigator.clipboard.writeText(linkToCopy).then(() => {
            alert("Lien copié dans le presse-papiers !");
        }).catch(err => {
            console.error('Erreur lors de la copie du lien: ', err);
        });
    };

    const playerList = document.getElementById('playerList');

    function updatePlayerList() {
        fetch('updatePlayers.php')
            .then(response => response.json())
            .then(players => {
                playerList.innerHTML = '';
                players.forEach(player => {
                    const li = document.createElement('li');
                    li.textContent = player;
                    playerList.appendChild(li);
                });
            });
    }

    setInterval(updatePlayerList, 5000);
</script>
</body>
</html>