<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisir un Pseudo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Menu Principal</h1>
    <div id="joinContainer">
        <input type="text" id="playerName" placeholder="Entrez votre pseudo" required>
        <button id="joinBtn" style="display:none;">Rejoindre la Partie</button> <!-- Ce bouton est caché -->
    </div>

    <div class="button-container">
        <button id="createGameBtn">Créer une Partie</button>
        <button id="resetBtn">Réinitialiser</button>
    </div>

    <script>
        document.getElementById('createGameBtn').onclick = () => {
            const playerName = document.getElementById('playerName').value;

            if (playerName.trim() === "") {
                alert("Veuillez entrer un pseudo avant de créer une partie.");
                return;
            }

            // Vérifie si une partie est déjà active
            fetch('checkActiveGame.php')
                .then(response => response.json())
                .then(data => {
                    if (data.activeGame) {
                        // Demande confirmation pour supprimer la partie
                        if (confirm("Une partie est déjà en cours. Souhaitez-vous la supprimer et en créer une nouvelle ?")) {
                            // Réinitialiser les données
                            fetch('resetAll.php', { method: 'POST' })
                                .then(() => {
                                    // Enregistrer le pseudo dans la session
                                    return fetch('setPlayerName.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                        },
                                        body: JSON.stringify({ playerName: playerName })
                                    });
                                })
                                .then(() => {
                                    // Redirection vers la page de création de partie
                                    window.location.href = 'createGame.php';
                                });
                        }
                    } else {
                        // Aucune partie active, juste créer une partie
                        fetch('setPlayerName.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ playerName: playerName })
                        }).then(() => {
                            // Redirection vers la page de création de partie
                            window.location.href = 'createGame.php';
                        });
                    }
                });
        };

        document.getElementById('resetBtn').onclick = () => {
            // Confirmation avant réinitialisation
            if (confirm("Êtes-vous sûr de vouloir réinitialiser toutes les données ?")) {
                fetch('resetAll.php', {
                    method: 'POST'
                }).then(() => {
                    // Rediriger vers la page d'accueil après réinitialisation
                    alert("Toutes les données ont été réinitialisées.");
                    location.reload(); // Recharger la page
                });
            }
        };
    </script>
</body>
</html>