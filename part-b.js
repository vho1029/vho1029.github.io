document.addEventListener('DOMContentLoaded', () => {
// Victor Ho
// Part-b: Image Card Matching Game
// --- DOM Elements ---
    const setupScreen = document.getElementById('setupScreen');
    const gameScreen = document.getElementById('gameScreen');
    const pairCountSelect = document.getElementById('pairCount');
    const difficultySelect = document.getElementById('difficulty');
    const startGameBtn = document.getElementById('startGameBtn');
    const gameBoard = document.getElementById('gameBoard');
    const timerSpan = document.getElementById('timer');
    const scoreSpan = document.getElementById('score');
    const gameMessage = document.getElementById('gameMessage');
    const leaderboardModal = document.getElementById('leaderboardModal');
    const viewLeaderboardBtn = document.getElementById('viewLeaderboardBtn');
    const closeBtn = document.querySelector('.close-btn');
    const leaderboardList = document.getElementById('leaderboardList');
    
// --- Game State & Settings ---
    let settings = {};
    let cards = [];
    let flippedCards = [];
    let matchedPairs = 0;
    let score = 0;
    let gameTimer;
    let timeLeft;
    let isBoardLocked = false; // Prevents clicking more than 2 cards at once

// --- Image Assets ---
    const IMAGE_SOURCES = [
        'images/img-1.jpg', 'images/img-2.jpg', 'images/img-3.jpg', 'images/img-4.jpg',
        'images/img-5.jpg', 'images/img-6.jpg', 'images/img-7.jpg', 'images/img-8.jpg',
        'images/img-9.jpg', 'images/img-10.jpg', 'images/img-11.jpg', 'images/img-12.jpg'
    ];
    
// --- Event Listeners ---
    startGameBtn.addEventListener('click', initializeGame);
    viewLeaderboardBtn.addEventListener('click', showLeaderboard);
    closeBtn.addEventListener('click', () => leaderboardModal.style.display = 'none');
    window.addEventListener('click', (event) => {
        if (event.target == leaderboardModal) {
            leaderboardModal.style.display = 'none';
        }
    });

/**
 * Sets up and starts the game based on user selections
*/
    function initializeGame() {
// Get user settings 
        const numPairs = parseInt(pairCountSelect.value);
        const memorizationTime = parseInt(difficultySelect.value) * 1000;
        
// Set game time limit
        const timeLimits = { 8: 120, 10: 150, 12: 180 };
        timeLeft = timeLimits[numPairs];
        
        settings = { numPairs, memorizationTime, timeLeft };
        
// Reset game state
        score = 0;
        matchedPairs = 0;
        scoreSpan.textContent = score;
        timerSpan.textContent = timeLeft;
        gameMessage.textContent = '';
        isBoardLocked = true; // Lock board during memorization
        
// Switch screens
        setupScreen.classList.add('hidden');
        gameScreen.classList.remove('hidden');
        
        createBoard();
        
// Memorization Phase 
        gameMessage.textContent = `Memorize the cards! You have ${memorizationTime / 1000} seconds.`;
        revealAllCards(true);
        
        setTimeout(() => {
            revealAllCards(false);
            gameMessage.textContent = 'Find the matching pairs!';
            isBoardLocked = false;
            startTimer();
        }, memorizationTime);
    }
    
    /**
     * Creates the game board with shuffled cards
     */
    function createBoard() {
        gameBoard.innerHTML = '';
        // Adjust grid layout based on number of pairs
        const columns = settings.numPairs === 12 ? 6 : 4;
        gameBoard.style.gridTemplateColumns = `repeat(${columns}, 100px)`;

        // Select, duplicate, and shuffle images
        let selectedImages = IMAGE_SOURCES.slice(0, settings.numPairs);
        let gameImages = [...selectedImages, ...selectedImages];
        gameImages.sort(() => 0.5 - Math.random()); // Shuffle array

        // Create card elements
        gameImages.forEach((imgSrc, index) => {
            const card = document.createElement('div');
            card.classList.add('card');
            card.dataset.id = imgSrc;

            card.innerHTML = `
    <div class="card-face card-front">${index + 1}</div>
    <div class="card-face card-back">
        <img src="${imgSrc}" alt="Card Image">
    </div>
`;
            
            card.addEventListener('click', () => handleCardClick(card));
            gameBoard.appendChild(card);
        });
    }

    /**
     * Shows or hides all cards for the memorization phase
     */
    function revealAllCards(show) {
        const allCards = document.querySelectorAll('.card');
        allCards.forEach(card => card.classList.toggle('flipped', show));
    }

    /**
     * Starts the countdown timer
     */
    function startTimer() {
        gameTimer = setInterval(() => {
            timeLeft--;
            timerSpan.textContent = timeLeft;
            if (timeLeft <= 0) {
                endGame(false); // Player loses if time runs out
            }
        }, 1000);
    }

    /**
     * Handles the logic when a player clicks a card
     */
    function handleCardClick(card) {
        if (isBoardLocked || card.classList.contains('flipped') || card.classList.contains('matched')) {
            return;
        }

        card.classList.add('flipped');
        flippedCards.push(card);

        if (flippedCards.length === 2) {
            isBoardLocked = true; // Lock board while checking for a match
            checkForMatch();
        }
    }

    /**
     * Checks if the two flipped cards are a match
     */
    function checkForMatch() {
        const [card1, card2] = flippedCards;
        
        if (card1.dataset.id === card2.dataset.id) {
            // It's a match! 
            score += 10;
            matchedPairs++;
            card1.classList.add('matched');
            card2.classList.add('matched');
            isBoardLocked = false; // Unlock immediately for matches
            if (matchedPairs === settings.numPairs) {
                endGame(true); // Player wins
            }
        } else {
            // Not a match 
            score -= 5;
            setTimeout(() => {
                card1.classList.remove('flipped');
                card2.classList.remove('flipped');
                isBoardLocked = false; // Unlock after cards flip back
            }, 1000);
        }

        scoreSpan.textContent = score;
        flippedCards = [];
    }

    /**
     * Ends the game, calculates final score, and shows results
     */
    function endGame(isWin) {
        clearInterval(gameTimer);
        isBoardLocked = true;
        
        if (isWin) {
            // Add a simple animation or celebration message 
            gameMessage.innerHTML = '<h2>Congratulations, You Win!</h2>';
            setTimeout(() => {
                const playerName = prompt('You won! Enter your name for the leaderboard:');
                if (playerName) {
                    saveScore(playerName, score);
                }
                showLeaderboard();
                resetToSetup();
            }, 2000);
        } else {
            gameMessage.innerHTML = '<h2>Time\'s Up! You Lost.</h2>';
            setTimeout(() => {
                showLeaderboard();
                resetToSetup();
            }, 3000);
        }
    }

    /**
     * Saves the player's score to localStorage
     */
    function saveScore(name, finalScore) {
        const scores = JSON.parse(localStorage.getItem('memoryGameScores')) || [];
        scores.push({ name, score: finalScore });
        scores.sort((a, b) => b.score - a.score); // Sort descending
        const topScores = scores.slice(0, 5); // Keep only top 5 
        localStorage.setItem('memoryGameScores', JSON.stringify(topScores));
    }
    
    /**
     * Displays the leaderboard from localStorage
     */
    function showLeaderboard() {
        const scores = JSON.parse(localStorage.getItem('memoryGameScores')) || [];
        leaderboardList.innerHTML = ''; // Clear previous list
        
        if (scores.length === 0) {
            leaderboardList.innerHTML = '<li>No scores yet. Be the first!</li>';
        } else {
            scores.forEach(entry => {
                const li = document.createElement('li');
                li.textContent = `${entry.name} - ${entry.score} points`;
                leaderboardList.appendChild(li);
            });
        }
        
        leaderboardModal.style.display = 'block';
    }

    /**
     * Resets the UI back to the initial setup screen
     */
    function resetToSetup() {
        gameScreen.classList.add('hidden');
        setupScreen.classList.remove('hidden');
    }
});