<?php
session_start();

// Edge case security check: ensure a user is logged in.
if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
}

// Load scores from file instead of just session
function loadScores() {
    $scores_file = 'scores.txt';
    $scores = array();
    
    if (file_exists($scores_file)) {
        $lines = file($scores_file, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            if (trim($line) === '') continue;
            list($username, $score) = explode(':', $line);
            $scores[$username] = intval($score);
        }
    }
    return $scores;
}

// Get scores from file for leaderboard display
$scores = loadScores();

// Merge with any session scores that might not be saved yet
if (isset($_SESSION['scores'])) {
    foreach ($_SESSION['scores'] as $username => $score) {
        $scores[$username] = $score;
    }
}

// Sort the scores in descending order (highest first).
// arsort() maintains the key-value association
if (!empty($scores)) {
    arsort($scores);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeopardy! - Leaderboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="leaderboard-page">
    <div class="container">
        <h1 class="game-title">Leaderboard</h1>

        <div class="leaderboard-container">
            <?php if (!empty($scores)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Player</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($scores as $username => $score): 
                        ?>
                            <tr<?php echo ($username === $_SESSION['username']) ? ' style="background-color: rgba(255, 195, 0, 0.2);"' : ''; ?>>
                                <td><?php echo $rank++; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($username); ?>
                                    <?php if ($username === $_SESSION['username']): ?>
                                        <span style="color: #ffc300;"> (You)</span>
                                    <?php endif; ?>
                                </td>
                                <td>$<?php echo $score; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-scores">No scores recorded yet. Play a game to see the leaderboard!</p>
            <?php endif; ?>
        </div>

        <footer>
            <div class="game-options">
                <a href="index.php" class="option-btn">Back to Game</a>
            </div>
        </footer>
    </div>
</body>
</html>
