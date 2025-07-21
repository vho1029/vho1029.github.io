<?php
session_start();

// if true login, redirect to game, else throw error
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

// check if user was redirected from successful registration
if (isset($_GET['registered']) && $_GET['registered'] === 'success') {
    $success = "Registration successful! You can now login.";
}

// login form submission handling
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    //validation edge case
    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        // if edge case passes, read users from file
        $users_file = 'users.txt';
        $valid_login = false;

        if (file_exists($users_file)) {
            $users = file($users_file, FILE_IGNORE_NEW_LINES);
            foreach ($users as $user) {
                list($stored_username, $stored_password) = explode(':', $user);
                if ($username === $stored_username && password_verify($password, $stored_password)) {
                    $valid_login = true;
                    break;
                }
            }
        }
        
        if ($valid_login) {
            $_SESSION['username'] = $username;
            
            // set remember me cookie if checked
            if (isset($_POST['remember'])) {
                setcookie('jeopardy_user', $username, time() + (86400 * 30), "/");
            }
            
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
}

// perform check for remember me cookie
if (isset($_COOKIE['jeopardy_user']) && !isset($_SESSION['username'])) {
    $cookie_username = $_COOKIE['jeopardy_user'];
    // proceed to auto-fill username
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>This Is Jeopardy! - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-page">
    <div class="login-container">
        <h1 class="game-title login-title">This Is JEOPARDY!</h1>
        
        <form class="login-form" method="POST" action="login.php">
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required autofocus
                       value="<?php echo isset($cookie_username) ? htmlspecialchars($cookie_username) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember Me</label>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
            
            <p class="register-link">
                New player? <a href="register.php">Register here</a>
            </p>
        </form>
    </div>
</body>
</html>
