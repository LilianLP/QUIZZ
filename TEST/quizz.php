<?php
session_start();
include 'db.php';

if (!isset($_SESSION['playerName'])) {
    header("Location: index.php");
    exit();
}

// Récupérer la liste des films
$stmt = $pdo->query("SELECT titres, affiches, critiques FROM films");
$films = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier le nombre de critiques
if (count($films) < 4) {
    die("Il faut au moins 4 films pour créer le quiz.");
}

// Initialiser les tableaux si nécessaires
if (!isset($_SESSION['used_titles'])) {
    $_SESSION['used_titles'] = [];
}

if (!isset($_SESSION['question_count'])) {
    $_SESSION['question_count'] = 0; // Compteur de questions
}

do {
    $selectedFilm = getRandomFilm();
    if (count($_SESSION['used_titles']) >= count($films)) {
        die("Tous les films ont été utilisés.");
    }
} while (in_array($selectedFilm['titres'], $_SESSION['used_titles']));

// Ajouter le film au tableau utilisé
$_SESSION['used_titles'][] = $selectedFilm['titres'];
$_SESSION['question_count']++; // Incrémenter le compteur de questions

$otherFilms = getOtherFilms($selectedFilm);
$otherOptions = array_slice($otherFilms, 0, 3);
$options = array_merge([$selectedFilm], $otherOptions);
shuffle($options);

function getRandomFilm() {
    global $films;
    return empty($films) ? null : $films[array_rand($films)];
}

function getOtherFilms($currentFilm) {
    global $films;
    return array_filter($films, function($film) use ($currentFilm) {
        return $film['titres'] !== $currentFilm['titres'];
    });
}

if (!isset($selectedFilm) || !isset($options)) {
    die("Des erreurs sont survenues lors de la sélection des films.");
}

if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = 0;
}

// Vérifier si le quiz est terminé
$isQuizFinished = $_SESSION['question_count'] >= 2;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz de Critiques de Films</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Quiz de fou</h1>
    <div id="score">Votre Score: <?php echo $_SESSION['score']; ?></div>

    <?php if (!$isQuizFinished): ?>
        <div id="affiches">
            <?php foreach ($options as $film): ?>
                <img src="<?php echo htmlspecialchars($film['affiches']); ?>" class="film-affiche" 
                     data-titre="<?php echo htmlspecialchars($film['titres']); ?>" onclick="selectFilm(this)">
            <?php endforeach; ?>
        </div>
        <div id="critique"><?php echo htmlspecialchars($selectedFilm['critiques']); ?></div>
        <div id="timerDisplay" style="font-size: 20px;">Temps restant : <span id="timer">30</span> secondes</div>
    <?php else: ?>
        <h2>Quiz Terminé!</h2>
        <?php
        // Enregistrer le score dans la base de données
        $stmt = $pdo->prepare("INSERT INTO scores (playerName, score) VALUES (:playerName, :score)");
        $stmt->execute(['playerName' => $_SESSION['playerName'], 'score' => $_SESSION['score']]);
        ?>
        <form action="results.php" method="post">
            <button type="submit">Voir les Résultats</button>
        </form>
    <?php endif; ?>

    <div id="feedback" style="margin-top: 20px; font-size: 18px;"></div>

    <script>
        let selectedImg = null;
        const correctTitle = '<?php echo htmlspecialchars($selectedFilm["titres"]); ?>';
        let timer = 30;
        let timerId;

        function startTimer() {
            timerId = setInterval(() => {
                if (timer > 0) {
                    timer--;
                    document.getElementById('timer').innerText = timer;
                } else {
                    clearInterval(timerId);
                    showCorrectAnswer();
                }
            }, 1000);
        }

        function selectFilm(img) {
            if (selectedImg) {
                selectedImg.classList.remove('selected');
            }
            selectedImg = img;
            selectedImg.classList.add('selected');
            document.getElementById('feedback').textContent = 'Vous avez sélectionné : ' + selectedImg.dataset.titre;
            // Pas d'affichage immédiat de la réponse, le timer continue
        }

        function showCorrectAnswer() {
            const feedbackElement = document.getElementById('feedback');

            if (selectedImg) {
                let selectedTitle = selectedImg.dataset.titre;

                if (selectedTitle === correctTitle) {
                    feedbackElement.textContent = "Bonne réponse !";
                    updateScore(1);
                } else {
                    feedbackElement.textContent = `Mauvaise réponse. La bonne réponse était : ${correctTitle}.`;
                }
            } else {
                feedbackElement.textContent = `Temps écoulé ! La bonne réponse était : ${correctTitle}.`;
            }

            setTimeout(() => {
                location.reload(); // Recharge la page pour la prochaine question après 10 secondes
            }, 5000);
        }

        function updateScore(points) {
            let scoreDisplay = document.getElementById('score');
            let currentScore = parseInt(scoreDisplay.innerText.split(": ")[1]);
            currentScore += points;
            scoreDisplay.innerText = "Votre Score: " + currentScore;

            // Mise à jour de la session
            <?php $_SESSION['score'] += 1; ?>
        }

        // Démarrer le timer lorsque la page est chargée
        window.onload = startTimer;
    </script>
</body>
</html>