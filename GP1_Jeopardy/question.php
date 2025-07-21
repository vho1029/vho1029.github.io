<?php
session_start();

//start sesh, gather user data and game state

//edge case security check, make sure user is logged in, if not, redirect to login
if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
}

//validation check for required params, needs category and val to display question
if (!isset($_GET['category']) || !isset($_GET['value'])) {
    header("Location: index.php");
    exit();
}

//get question params from url
$category = $_GET['category'];
$value = intval($_GET['value']); //convert to int for security

//game data section, define all categories and questions - RANDOMIZED version with multiple questions per value
//acts as database and matches data in index.php
$categories = array(
    "WORLD CAPITALS" => array(
        200 => array(
            array("q" => "This French capital city is home to the Eiffel Tower and the Louvre Museum.", "a" => "What is Paris?"),
            array("q" => "This American capital city is located in Georgia", "a" => "What is Atlanta?"),
            array("q" => "This German capital was divided by a famous wall from 1961 to 1989.", "a" => "What is Berlin?")
        ),
        400 => array(
            array("q" => "Known for its canals and gondolas, this Italian city serves as the capital of the Veneto region.", "a" => "What is Venice?"),
            array("q" => "This American city is the capital of California", "a" => "What is Sacramento?"),
            array("q" => "This Japanese capital was formerly known as Edo.", "a" => "What is Tokyo?")
        ),
        600 => array(
            array("q" => "This capital of Thailand was formerly known as Siam and is famous for its ornate temples.", "a" => "What is Bangkok?"),
            array("q" => "This Australian capital was planned and built specifically to be the national capital.", "a" => "What is Canberra?"),
            array("q" => "This Canadian capital sits on the Ottawa River in Ontario.", "a" => "What is Ottawa?")
        ),
        800 => array(
            array("q" => "Sitting at 11,942 feet above sea level, this Bolivian capital is one of the world's highest.", "a" => "What is La Paz?"),
            array("q" => "This South African capital is known as the 'Mother City' and sits beneath Table Mountain.", "a" => "What is Cape Town?"),
            array("q" => "This Peruvian capital was founded by Spanish conquistador Francisco Pizarro in 1535.", "a" => "What is Lima?")
        ),
        1000 => array(
            array("q" => "This capital of Bhutan, whose name means 'rice valley,' is the only capital without traffic lights.", "a" => "What is Thimphu?"),
            array("q" => "This island nation's capital shares its name with a type of sock and is located in the South Pacific.", "a" => "What is Nuku'alofa?"),
            array("q" => "This tiny European principality's capital is smaller than New York's Central Park.", "a" => "What is Monaco?")
        )
    ),
    "SCIENCE & NATURE" => array(
        200 => array(
            array("q" => "This organ in the human body pumps blood throughout the circulatory system.", "a" => "What is the heart?"),
            array("q" => "This largest organ in the human body helps regulate temperature.", "a" => "What is the skin?"),
            array("q" => "This gas makes up about 78% of Earth's atmosphere.", "a" => "What is nitrogen?")
        ),
        400 => array(
            array("q" => "H₂O is the chemical formula for this common substance.", "a" => "What is water?"),
            array("q" => "CO₂ is the chemical formula for this greenhouse gas.", "a" => "What is carbon dioxide?"),
            array("q" => "NaCl is the chemical formula for this common seasoning.", "a" => "What is salt?")
        ),
        600 => array(
            array("q" => "This process by which plants convert sunlight into energy produces oxygen as a byproduct.", "a" => "What is photosynthesis?"),
            array("q" => "This force keeps planets in orbit around the sun.", "a" => "What is gravity?"),
            array("q" => "This scientist developed the theory of evolution by natural selection.", "a" => "Who is Charles Darwin?")
        ),
        800 => array(
            array("q" => "These subatomic particles with no electric charge were discovered by James Chadwick in 1932.", "a" => "What are neutrons?"),
            array("q" => "This organ regulates blood sugar through hormone production.", "a" => "What is the pancreas?"),
            array("q" => "This element has the atomic number 79 and symbol Au.", "a" => "What is gold?")
        ),
        1000 => array(
            array("q" => "This quantum mechanical principle states that you cannot simultaneously know both the exact position and momentum of a particle.", "a" => "What is the Heisenberg Uncertainty Principle?"),
            array("q" => "This paradox involving a cat illustrates the strange nature of quantum superposition.", "a" => "What is Schrödinger's Cat?"),
            array("q" => "This theoretical physicist developed the equation E=mc².", "a" => "Who is Albert Einstein?")
        )
    ),
    "LITERATURE" => array(
        200 => array(
            array("q" => "This Dr. Seuss character 'stole Christmas' in a beloved holiday tale.", "a" => "Who is the Grinch?"),
            array("q" => "This boy wizard attends Hogwarts School of Witchcraft and Wizardry.", "a" => "Who is Harry Potter?"),
            array("q" => "This classic children's book features a rabbit named Peter.", "a" => "What is The Tale of Peter Rabbit?")
        ),
        400 => array(
            array("q" => "'It was the best of times, it was the worst of times' opens this Charles Dickens novel.", "a" => "What is A Tale of Two Cities?"),
            array("q" => "'To be or not to be, that is the question' is from this Shakespeare play.", "a" => "What is Hamlet?"),
            array("q" => "'Call me Ishmael' is the famous opening line of this Herman Melville novel.", "a" => "What is Moby Dick?")
        ),
        600 => array(
            array("q" => "This American author wrote 'The Great Gatsby' and 'Tender Is the Night.'", "a" => "Who is F. Scott Fitzgerald?"),
            array("q" => "This British author created the detective Sherlock Holmes.", "a" => "Who is Arthur Conan Doyle?"),
            array("q" => "This American author wrote 'To Kill a Mockingbird.'", "a" => "Who is Harper Lee?")
        ),
        800 => array(
            array("q" => "In this Shakespearean tragedy, the title character delivers the 'To be or not to be' soliloquy.", "a" => "What is Hamlet?"),
            array("q" => "This Russian author wrote 'War and Peace' and 'Anna Karenina.'", "a" => "Who is Leo Tolstoy?"),
            array("q" => "This dystopian novel by George Orwell features Big Brother.", "a" => "What is 1984?")
        ),
        1000 => array(
            array("q" => "This 1922 modernist novel by James Joyce takes place in Dublin over the course of a single day.", "a" => "What is Ulysses?"),
            array("q" => "This epic poem by Homer tells the story of Odysseus's journey home.", "a" => "What is The Odyssey?"),
            array("q" => "This author is known as the grandfather of existentialism", "a" => "Who is Soren Kierkegaard")
        )
    ),
    "POP CULTURE" => array(
        200 => array(
            array("q" => "This animated movie franchise features a cowboy named Woody and a space ranger named Buzz.", "a" => "What is Toy Story?"),
            array("q" => "This Disney movie features a lion cub named Simba.", "a" => "What is The Lion King?"),
            array("q" => "This superhero is known as the 'Man of Steel.'", "a" => "Who is Superman?")
        ),
        400 => array(
            array("q" => "This British band sang 'Bohemian Rhapsody' and 'We Will Rock You.'", "a" => "Who is Queen?"),
            array("q" => "This 'King of Pop' moonwalked his way to fame.", "a" => "Who is Michael Jackson?"),
            array("q" => "This streaming platform is known for its original series and red logo.", "a" => "What is Netflix?")
        ),
        600 => array(
            array("q" => "This streaming series set in Hawkins, Indiana features a parallel dimension called the Upside Down.", "a" => "What is Stranger Things?"),
            array("q" => "This HBO series about dragons and thrones concluded in 2019.", "a" => "What is Game of Thrones?"),
            array("q" => "This Marvel movie brought together Iron Man, Captain America, and Thor for the first time.", "a" => "What is The Avengers?")
        ),
        800 => array(
            array("q" => "This South Korean film became the first non-English language film to win Best Picture at the Oscars in 2020.", "a" => "What is Parasite?"),
            array("q" => "This social media platform is known for its 280-character limit.", "a" => "What is Twitter?"),
            array("q" => "This artist wrote the song Miss Misery for the film Good Will 
            Hunting", "a" => "Who is Elliott Smith?")
        ),
        1000 => array(
            array("q" => "This video game, released in 1980, was the first to feature cutscenes and is considered the first mascot character in gaming.", "a" => "What is Pac-Man?"),
            array("q" => "This artist holds the record for most Grammy wins of all time.", "a" => "Who is Beyoncé?"),
            array("q" => "This streaming service launched by Disney became a major competitor to Netflix.", "a" => "What is Disney+?")
        )
    ),
    "HISTORY" => array(
        200 => array(
            array("q" => "This American president appears on the penny and the five-dollar bill.", "a" => "Who is Abraham Lincoln?"),
            array("q" => "This president appears on Mount Rushmore and the dollar bill.", "a" => "Who is George Washington?"),
            array("q" => "This president is the first African American president", "a" => "Who is Barack Obama?")
        ),
        400 => array(
            array("q" => "This year marks the beginning of World War II with Germany's invasion of Poland.", "a" => "What is 1939?"),
            array("q" => "This year saw the end of World War II with Japan's surrender.", "a" => "What is 1945?"),
            array("q" => "The Berlin Wall fell in this year, symbolizing the end of the Cold War.", "a" => "What is 1989?")
        ),
        600 => array(
            array("q" => "This ancient wonder of the world, built around 2560 BCE, is the only one still largely intact.", "a" => "What is the Great Pyramid of Giza?"),
            array("q" => "This Italian explorer's voyages opened the Americas to European colonization.", "a" => "Who is Christopher Columbus?"),
            array("q" => "This French military leader was exiled to the island of Elba.", "a" => "Who is Napoleon?")
        ),
        800 => array(
            array("q" => "This treaty, signed in 1919, officially ended World War I and imposed harsh penalties on Germany.", "a" => "What is the Treaty of Versailles?"),
            array("q" => "This Roman general crossed the Rubicon and became dictator.", "a" => "Who is Julius Caesar?"),
            array("q" => "This document, signed in 1215, limited the power of the English monarchy.", "a" => "What is the Magna Carta?")
        ),
        1000 => array(
            array("q" => "This Byzantine emperor, ruling from 527-565 CE, attempted to reconquer the Western Roman Empire and codified Roman law.", "a" => "Who is Justinian I?"),
            array("q" => "This Mongol leader created the largest contiguous land empire in history.", "a" => "Who is Genghis Khan?"),
            array("q" => "This plague devastated Europe in the 14th century, killing an estimated third of the population.", "a" => "What is the Black Death?")
        )
    )
);

//validation check for requested question existence
if (!isset($categories[$category][$value])) {
    header("Location: index.php");
    exit();
}

//check for existing answer, create unique ID for question
$questionKey = $category . "_" . $value;
// ?? [] provides empty arr as default if answered array doesn't exist
if (in_array($questionKey, $_SESSION['answered'] ?? [])) {
    header("Location: index.php");
    exit();
}

// RANDOMIZER: Select a random question from the available options
$questions = $categories[$category][$value];
$randomIndex = array_rand($questions);
$selectedQuestion = $questions[$randomIndex];

// gather question text and answer for display
$question = $selectedQuestion['q'];
$correctAnswer = $selectedQuestion['a'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeopardy - Question</title>
    <link rel="stylesheet" href="styles.css">
</head>
<!-- body class for question page styling -->
<body class="question-page">
    <!-- Full-screen overlay creates modal effect -->
    <div class="question-modal-overlay">
        <!-- modal container for the question -->
        <div class="question-modal">
            <!-- header shows category and dollar value -->
            <div class="question-header">
                <!-- htmlspecialchars prevents XSS attacks by escaping HTML -->
                <h2><?php echo htmlspecialchars($category); ?></h2>
                <h3>$<?php echo $value; ?></h3>
            </div>

            <!-- main question content -->
            <div class="question-content">
                <!-- display the RANDOMLY SELECTED question text -->
                <p class="question-text"><?php echo htmlspecialchars($question); ?></p>

                <!-- answer submission form -->
                <!-- submits to index.php for answer processing -->
                <form method="POST" action="index.php" class="answer-form">
                    <!-- hidden fields pass category, value, and correct answer to index.php -->
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                    <input type="hidden" name="value" value="<?php echo $value; ?>">
                    <input type="hidden" name="correct_answer" value="<?php echo htmlspecialchars($correctAnswer); ?>">

                    <!-- answer input field -->
                    <div class="form-group">
                        <label for="answer">Your Answer:</label>
                        <!-- placeholder reminds player of Jeopardy format, autofocus adjusts cursor to field automatically -->
                        <input type="text" id="answer" name="answer" placeholder="Remember to phrase as a question..." required autofocus>
                    </div>

                    <!-- action buttons -->
                    <div class="button-group">
                    	<!-- submit button to post form -->
                    	<button type="submit" class="submit-btn">Submit Answer</button>
                    	<!-- cancel link returns to game board without submitting answer -->
                    	<a href="index.php" class="cancel-btn">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

