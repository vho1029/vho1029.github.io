//Victor Ho
//Part-a: Number Guessing Game

// Wait for the DOM to fully load before running the script
document.addEventListener('DOMContentLoaded', () => {

    // DOM Element References
    const guessInput = document.getElementById('guessInput');
    const guessButton = document.getElementById('guessButton');
    const message = document.getElementById('message');
    const guessesLeftSpan = document.getElementById('guessesLeft');
    const clockDiv = document.getElementById('clock');
    
    // Sound Effect References
    const winSound = document.getElementById('winSound');
    const loseSound = document.getElementById('loseSound');
    const clickSound = document.getElementById('clickSound');
    const backgroundMusic = document.getElementById('backgroundMusic');

    // Game State Variables
    let secretNumber;
    let remainingGuesses;
    let gameActive = true;
    let isFirstGuess = true; // Flag to handle audio autoplay policy
    let secondsElapsed = 0;
    let clockInterval;

    /**
     * Starts a new game by resetting variables and the UI
     */
    function newGame() {
        secretNumber = Math.floor(Math.random() * 100) + 1;
        remainingGuesses = 10;
        gameActive = true;
        
        guessesLeftSpan.textContent = remainingGuesses;
        message.textContent = '';
        guessInput.value = '';
        guessInput.disabled = false;
        guessButton.disabled = false;
        
        // Restore background music volume for the new game
        backgroundMusic.volume = 1.0;

        secondsElapsed = 0;
        clearInterval(clockInterval);
        startClock();
    }

    /**
     * Handles the player's guess
     */
    function handleGuess() {
        if (!gameActive) return;

        // Start background music on the first user interaction to comply with browser policies
        if (isFirstGuess) {
            backgroundMusic.play();
            isFirstGuess = false;
        }

        clickSound.play(); // Cartoon whistle for each guess

        const playerGuess = parseInt(guessInput.value, 10);

        if (isNaN(playerGuess) || playerGuess < 1 || playerGuess > 100) {
            message.textContent = 'Please enter a number between 1 and 100.';
            return;
        }

        remainingGuesses--;
        guessesLeftSpan.textContent = remainingGuesses;

        if (playerGuess === secretNumber) {
            winGame();
        } else if (playerGuess > secretNumber) {
            message.textContent = 'Too high! Guess again.';
        } else {
            message.textContent = 'Too low! Guess again.';
        }

        if (remainingGuesses <= 0 && playerGuess !== secretNumber) {
            loseGame();
        }
    }
    
    /**
     * Logic for winning the game
     */
    function winGame() {
        message.textContent = `Correct! The number was ${secretNumber}. A new game will start.`;
        backgroundMusic.volume = 0.3; // Lower background music volume
        winSound.play(); // Trumpet fanfare for a win
        endGame();
    }

    /**
     * Logic for losing the game
     */
    function loseGame() {
        message.textContent = `You lost! The secret number was ${secretNumber}. A new game will start.`;
        backgroundMusic.volume = 0.3; // Lower background music volume
        loseSound.play(); // Sad trombone for a loss
        endGame();
    }

    /**
     * Ends the current game and prepares for a new one
     */
    function endGame() {
        gameActive = false;
        guessInput.disabled = true;
        guessButton.disabled = true;
        clearInterval(clockInterval);
        
        setTimeout(newGame, 4000);
    }
    
    /**
     * Starts and updates the game clock
     */
    function startClock() {
        clockInterval = setInterval(() => {
            secondsElapsed++;
            const minutes = Math.floor(secondsElapsed / 60).toString().padStart(2, '0');
            const seconds = (secondsElapsed % 60).toString().padStart(2, '0');
            clockDiv.textContent = `${minutes}:${seconds}`;
        }, 1000);
    }

    // Event Listeners
    guessButton.addEventListener('click', handleGuess);
    guessInput.addEventListener('keyup', (event) => {
        if (event.key === 'Enter') {
            handleGuess();
        }
    });

    // Start the first game
    newGame();
});