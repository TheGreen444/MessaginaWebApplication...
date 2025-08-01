<?php
$filePath = "../dxb/up/abcdefdryz.txt";
$message = "";
$showLogin = true; // default to login form
if (isset($_GET["login"])) {
    $showLogin = true;
// Validate cookie if exists
if (isset($_COOKIE["abcdefdryz"])) {
    $decoded = base64_decode($_COOKIE["abcdefdryz"]);
    if ($decoded && strpos($decoded, ":") !== false) {
        list($username, $password) = explode(":", $decoded, 2);
        
        // Check credentials
        $valid = false;
        if (file_exists($filePath)) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $parts = explode(":", $line);
                if (count($parts) >= 2 && $parts[0] === $username && $parts[1] === $password) {
                    $valid = true;
                    break;
                }
            }
        }
        
        // Redirect if valid
        if ($valid) {
            header("Location: ../kk");
            exit();
        }
    }
}


} elseif (isset($_GET["register"])) {
    $showLogin = false;
} 

// Register handling
if (isset($_POST["register"])) {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
    $email    = isset($_POST["email"])    ? trim($_POST["email"])    : "";

    // Validate fields
    if ($username == "" || $password == "" || $email == "") {
        $message   = "Please fill in all required fields.";
        $showLogin = false;
    } elseif (strpos($username, ":") !== false) {
        $message   = "Username cannot contain ':' character.";
        $showLogin = false;
    } else {
        $exists     = false;
        $highestId  = 4443; // base before first ID

        if (file_exists($filePath)) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Format: username:password:email::userid
                $parts = explode(":", $line);
                if (count($parts) >= 5) { // account for the extra empty field
                    $fileUser  = $parts[0];
                    $fileEmail = $parts[2];
                    $userId    = intval($parts[4]); // ID is at index 4

                    if ($fileUser == $username) {
                        $message = "Username already taken.";
                        $exists  = true;
                        break;
                    }
                    if ($fileEmail == $email) {
                        $message = "Email already registered.";
                        $exists  = true;
                        break;
                    }

                    if ($userId > $highestId) {
                        $highestId = $userId;
                    }
                }
            }
        }
        if (!$exists) {
            $newId   = $highestId + 1;
            $newLine = $username . ":" . $password . ":" . $email . "::" . $newId . PHP_EOL;
            file_put_contents($filePath, $newLine, FILE_APPEND | LOCK_EX);
  header("Location: /redrr/?login&registered=1");
    exit();
            
        }
    }
}

if (isset($_GET['registered']) && $_GET['registered'] == '1') {

    $message = "Registration successful! Please log in.";
    if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $message = "Registration successful! Please log in.";

    // Remove ?registered=1 from the URL so it doesn't show again on refresh
    echo "<script>
        if (window.history.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.delete('registered');
            window.history.replaceState({}, document.title, url.toString());
        }
    </script>";
}

}


// Login handling
if (isset($_POST["login"])) {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";

    if ($username === "" || $password === "") {
        $message = "Please enter username and password.";
    } else {
        $found = false;
        if (file_exists($filePath)) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Format: username:password:email:veryFryed
                list($fileUser, $filePass) = explode(":", $line, 3);

                if ($fileUser === $username && $filePass === $password) {
                    $found = true;
                    break;
                }
            }
        }

        if ($found) {
            // Login success - do your stuff here later
            $message = "Login successful!";
            setcookie("abcdefdryz", base64_encode($username . ":" . $password), time() + (90 * 24 * 60 * 60), "/");
            header("Location: ../kk");
exit();
        } else {
            $message = "Invalid username or password.";
        }
    }
}




?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>kk/login?register</title>

  <style>
    html {
      height: 100%;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: sans-serif;
      background: #0d131d;
      color: #fff;
    }

    .login-box {
      position: absolute;
      top: 50%;
      left: 50%;
      width: 400px;
      padding: 40px;
      transform: translate(-50%, -50%);
      background: rgba(0, 0, 0, 0.5);
      box-sizing: border-box;
      box-shadow: 0 15px 25px rgba(0, 0, 0, 0.6);
      border-radius: 10px;
      text-align: center;
    }

    .login-box h2 {
      margin: 0 0 30px;
      padding: 0;
      color: #fff;
      text-align: center;
    }

    .login-box .user-box {
      position: relative;
    }

    .login-box .user-box input {
      width: 100%;
      padding: 10px 0;
      font-size: 16px;
      color: #fff;
      margin-bottom: 30px;
      outline: none;
      background: transparent;
      border: none;
      border-bottom: 1px solid #fff;
      box-sizing: border-box;
    }

    .login-box .user-box label {
      position: absolute;
      top: 0;
      left: 0;
      padding: 10px 0;
      font-size: 16px;
      color: #fff;
      pointer-events: none;
      transition: 0.5s;
    }

    .login-box .user-box input:focus~label,
    .login-box .user-box input:valid~label {
      top: -20px;
      left: 0;
      color: #03f484;
      font-size: 12px;
    }

    /* Submit button */
    .login-box button.submit-btn {
      width: 100%;
      background: transparent;
      border: 2px solid #666e68;
      color: #fff;
      font-size: 16px;
      padding: 10px 0;
      text-transform: uppercase;
      letter-spacing: 4px;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      margin-top: 10px;
      border-radius: 5px;
      box-shadow: none;
      transition: 0.5s;
      font-weight: bold;
    }

    .login-box button.submit-btn:hover {
      color: #03f484;
    }

    .login-box button.submit-btn span {
      position: absolute;
      display: block;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .login-box button.submit-btn:hover span {
      opacity: 1;
    }

    .login-box button.submit-btn span:nth-child(1) {
      top: 0;
      left: -100%;
      width: 100%;
      height: 2px;
      background: linear-gradient(90deg, transparent, #03f484);
      animation: btn-anim1 1s linear infinite;
    }

    .login-box button.submit-btn span:nth-child(2) {
      top: -100%;
      right: 0;
      width: 2px;
      height: 100%;
      background: linear-gradient(180deg, transparent, #03f484);
      animation: btn-anim2 1s linear infinite;
      animation-delay: 0.25s;
    }

    .login-box button.submit-btn span:nth-child(3) {
      bottom: 0;
      right: 100%;
      width: 100%;
      height: 2px;
      background: linear-gradient(270deg, transparent, #03f484);
      animation: btn-anim3 1s linear infinite;
      animation-delay: 0.5s;
    }

    .login-box button.submit-btn span:nth-child(4) {
      bottom: -100%;
      left: 0;
      width: 2px;
      height: 100%;
      background: linear-gradient(360deg, transparent, #03f484);
      animation: btn-anim4 1s linear infinite;
      animation-delay: 0.75s;
    }

    @keyframes btn-anim1 {
      0% {
        left: -100%;
      }
      50%,
      100% {
        left: 100%;
      }
    }

    @keyframes btn-anim2 {
      0% {
        top: -100%;
      }
      50%,
      100% {
        top: 100%;
      }
    }

    @keyframes btn-anim3 {
      0% {
        right: -100%;
      }
      50%,
      100% {
        right: 100%;
      }
    }

    @keyframes btn-anim4 {
      0% {
        bottom: -100%;
      }
      50%,
      100% {
        bottom: 100%;
      }
    }

    /* Toggle text below form */
    .toggle-text {
      margin-top: 25px;
      font-size: 14px;
      color: #ccc;
    }

    .toggle-text a {
      color: #fff;
      cursor: pointer;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .toggle-text a:hover {
      text-decoration: underline;
      color: #03f484;
    }

    /* Message box */
    .message {
  position: absolute;
  top: 24%;           /* vertically center */
  left: 50%;          /* horizontally center */
  transform: translate(-50%, -50%);
  z-index: 444;

  /* start invisible */
  opacity: 0;
  animation: fadeInOut 2s forwards; /* total 2 seconds animation */
}

/* Message box styles */
.message-box {
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: bold;
  font-family: Arial, sans-serif;
  font-size: 18px;
  user-select: none;
  pointer-events: none;
}

.message-success {
  background-color: #4caf50;
  color: white;
}

.message-error {
  background-color: #f44336;
  color: white;
}

/* Animation: fade in -> stay -> fade out */
@keyframes fadeInOut {
  0% {
    opacity: 0;
    transform: translate(-50%, -60%) scale(0.9);
  }
  20% {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
  80% {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
  100% {
    opacity: 0;
    transform: translate(-50%, -40%) scale(0.9);
  }
}
  </style>
</head>

<body>
   <div class="message" id="messageDiv">
    <?php if ($message !== ""): ?>
      <div class="message-box <?php echo (strpos($message, 'success') !== false) ? 'message-success' : 'message-error'; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="login-box" id="form-container">

    <!-- Login Form -->
    <form id="login-form" autocomplete="off" method="post" style="display: <?php echo $showLogin ? 'block' : 'none'; ?>;">
      <h2>Login Form</h2>
      <div class="user-box">
        <input type="text" id="login-username" name="username" required />
        <label for="login-username">&nbsp;&nbsp;Username</label>
      </div>
      <div class="user-box">
        <input type="password" id="login-password" name="password" required />
        <label for="login-password">&nbsp;&nbsp;Password</label>
      </div>
      <button type="submit" name="login" class="submit-btn">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        Login
      </button>
      <div class="toggle-text">
        Don't have an account?
        <a href="./?register" id="show-register">Register</a>
      </div>
    </form>

    <!-- Register Form -->
    <form id="register-form" autocomplete="off" method="post" style="display: <?php echo $showLogin ? 'none' : 'block'; ?>;">
      <h2>Register Form</h2>
      <div class="user-box">
        <input type="text" id="register-username" name="username" required />
        <label for="register-username">&nbsp;&nbsp;Username</label>
      </div>
      <div class="user-box">
        <input type="password" id="register-password" name="password" required />
        <label for="register-password">&nbsp;&nbsp;Password</label>
      </div>
      <div class="user-box">
        <input type="email" id="register-email" name="email" required />
        <label for="register-email">&nbsp;&nbsp;Recovery Email</label>
      </div>
      <button type="submit" name="register" class="submit-btn">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        Register
      </button>
      <div class="toggle-text">
        Already have an account?
        <a href="./?login" id="show-login">Login</a>
      </div>
    </form>
  </div>
</body>
<script>
  setTimeout(() => {
  const msg = document.getElementById('messageDiv');
  if (msg) msg.style.display = 'none';
}, 1500);
</script>
</html>
<?php
$filePath = "../dxb/up/abcdefdryz.txt";
$message = "";
$showLogin = true; // default to login form
if (isset($_GET["login"])) {
    $showLogin = true;
// Validate cookie if exists
if (isset($_COOKIE["abcdefdryz"])) {
    $decoded = base64_decode($_COOKIE["abcdefdryz"]);
    if ($decoded && strpos($decoded, ":") !== false) {
        list($username, $password) = explode(":", $decoded, 2);
        
        // Check credentials
        $valid = false;
        if (file_exists($filePath)) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $parts = explode(":", $line);
                if (count($parts) >= 2 && $parts[0] === $username && $parts[1] === $password) {
                    $valid = true;
                    break;
                }
            }
        }
        
        // Redirect if valid
        if ($valid) {
            header("Location: ../kk");
            exit();
        }
    }
}


} elseif (isset($_GET["register"])) {
    $showLogin = false;
} 

// Register handling
if (isset($_POST["register"])) {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
    $email    = isset($_POST["email"])    ? trim($_POST["email"])    : "";

    // Validate fields
    if ($username === "" || $password === "" || $email === "") {
        $message   = "Please fill in all required fields.";
        $showLogin = false;
    } elseif (strpos($username, ":") !== false) {
        $message   = "Username cannot contain ':' character.";
        $showLogin = false;
    } else {
        $exists     = false;
        $highestId  = 4443; // base before first ID

        if (file_exists($filePath)) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Format: username:password:email::userid
                $parts = explode(":", $line);
                if (count($parts) >= 5) { // account for the extra empty field
                    $fileUser  = $parts[0];
                    $fileEmail = $parts[2];
                    $userId    = intval($parts[4]); // ID is at index 4

                    if ($fileUser === $username) {
                        $message = "Username already taken.";
                        $exists  = true;
                        break;
                    }
                    if ($fileEmail === $email) {
                        $message = "Email already registered.";
                        $exists  = true;
                        break;
                    }

                    if ($userId > $highestId) {
                        $highestId = $userId;
                    }
                }
            }
        }

        if (!$exists) {
            $newId   = $highestId + 1;
            $newLine = $username . ":" . $password . ":" . $email . "::" . $newId . PHP_EOL;
            file_put_contents($filePath, $newLine, FILE_APPEND | LOCK_EX);

            $message   = "Registration successful!";
            $showLogin = true;
        }
    }
}



// Login handling
if (isset($_POST["login"])) {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";

    if ($username === "" || $password === "") {
        $message = "Please enter username and password.";
    } else {
        $found = false;
        if (file_exists($filePath)) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Format: username:password:email:veryFryed
                list($fileUser, $filePass) = explode(":", $line, 3);

                if ($fileUser === $username && $filePass === $password) {
                    $found = true;
                    break;
                }
            }
        }

        if ($found) {
            // Login success - do your stuff here later
            $message = "Login successful!";
            setcookie("abcdefdryz", base64_encode($username . ":" . $password), time() + (90 * 24 * 60 * 60), "/");
            header("Location: ../kk");
exit();
        } else {
            $message = "Invalid username or password.";
        }
    }
}




?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>kk/login?register</title>

  <style>
    html {
      height: 100%;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: sans-serif;
      background: #0d131d;
      color: #fff;
    }

    .login-box {
      position: absolute;
      top: 50%;
      left: 50%;
      width: 400px;
      padding: 40px;
      transform: translate(-50%, -50%);
      background: rgba(0, 0, 0, 0.5);
      box-sizing: border-box;
      box-shadow: 0 15px 25px rgba(0, 0, 0, 0.6);
      border-radius: 10px;
      text-align: center;
    }

    .login-box h2 {
      margin: 0 0 30px;
      padding: 0;
      color: #fff;
      text-align: center;
    }

    .login-box .user-box {
      position: relative;
    }

    .login-box .user-box input {
      width: 100%;
      padding: 10px 0;
      font-size: 16px;
      color: #fff;
      margin-bottom: 30px;
      outline: none;
      background: transparent;
      border: none;
      border-bottom: 1px solid #fff;
      box-sizing: border-box;
    }

    .login-box .user-box label {
      position: absolute;
      top: 0;
      left: 0;
      padding: 10px 0;
      font-size: 16px;
      color: #fff;
      pointer-events: none;
      transition: 0.5s;
    }

    .login-box .user-box input:focus~label,
    .login-box .user-box input:valid~label {
      top: -20px;
      left: 0;
      color: #03f484;
      font-size: 12px;
    }

    /* Submit button */
    .login-box button.submit-btn {
      width: 100%;
      background: transparent;
      border: 2px solid #666e68;
      color: #fff;
      font-size: 16px;
      padding: 10px 0;
      text-transform: uppercase;
      letter-spacing: 4px;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      margin-top: 10px;
      border-radius: 5px;
      box-shadow: none;
      transition: 0.5s;
      font-weight: bold;
    }

    .login-box button.submit-btn:hover {
      color: #03f484;
    }

    .login-box button.submit-btn span {
      position: absolute;
      display: block;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .login-box button.submit-btn:hover span {
      opacity: 1;
    }

    .login-box button.submit-btn span:nth-child(1) {
      top: 0;
      left: -100%;
      width: 100%;
      height: 2px;
      background: linear-gradient(90deg, transparent, #03f484);
      animation: btn-anim1 1s linear infinite;
    }

    .login-box button.submit-btn span:nth-child(2) {
      top: -100%;
      right: 0;
      width: 2px;
      height: 100%;
      background: linear-gradient(180deg, transparent, #03f484);
      animation: btn-anim2 1s linear infinite;
      animation-delay: 0.25s;
    }

    .login-box button.submit-btn span:nth-child(3) {
      bottom: 0;
      right: 100%;
      width: 100%;
      height: 2px;
      background: linear-gradient(270deg, transparent, #03f484);
      animation: btn-anim3 1s linear infinite;
      animation-delay: 0.5s;
    }

    .login-box button.submit-btn span:nth-child(4) {
      bottom: -100%;
      left: 0;
      width: 2px;
      height: 100%;
      background: linear-gradient(360deg, transparent, #03f484);
      animation: btn-anim4 1s linear infinite;
      animation-delay: 0.75s;
    }

    @keyframes btn-anim1 {
      0% {
        left: -100%;
      }
      50%,
      100% {
        left: 100%;
      }
    }

    @keyframes btn-anim2 {
      0% {
        top: -100%;
      }
      50%,
      100% {
        top: 100%;
      }
    }

    @keyframes btn-anim3 {
      0% {
        right: -100%;
      }
      50%,
      100% {
        right: 100%;
      }
    }

    @keyframes btn-anim4 {
      0% {
        bottom: -100%;
      }
      50%,
      100% {
        bottom: 100%;
      }
    }

    /* Toggle text below form */
    .toggle-text {
      margin-top: 25px;
      font-size: 14px;
      color: #ccc;
    }

    .toggle-text a {
      color: #fff;
      cursor: pointer;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .toggle-text a:hover {
      text-decoration: underline;
      color: #03f484;
    }

    /* Message box */
    .message {
  position: absolute;
  top: 24%;           /* vertically center */
  left: 50%;          /* horizontally center */
  transform: translate(-50%, -50%);
  z-index: 444;

  /* start invisible */
  opacity: 0;
  animation: fadeInOut 2s forwards; /* total 2 seconds animation */
}

/* Message box styles */
.message-box {
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: bold;
  font-family: Arial, sans-serif;
  font-size: 18px;
  user-select: none;
  pointer-events: none;
}

.message-success {
  background-color: #4caf50;
  color: white;
}

.message-error {
  background-color: #f44336;
  color: white;
}

/* Animation: fade in -> stay -> fade out */
@keyframes fadeInOut {
  0% {
    opacity: 0;
    transform: translate(-50%, -60%) scale(0.9);
  }
  20% {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
  80% {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
  100% {
    opacity: 0;
    transform: translate(-50%, -40%) scale(0.9);
  }
}
  </style>
</head>

<body>
   <div class="message" id="messageDiv">
    <?php if ($message !== ""): ?>
      <div class="message-box <?php echo (strpos($message, 'success') !== false) ? 'message-success' : 'message-error'; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="login-box" id="form-container">

    <!-- Login Form -->
    <form id="login-form" autocomplete="off" method="post" style="display: <?php echo $showLogin ? 'block' : 'none'; ?>;">
      <h2>Login Form</h2>
      <div class="user-box">
        <input type="text" id="login-username" name="username" required />
        <label for="login-username">&nbsp;&nbsp;Username</label>
      </div>
      <div class="user-box">
        <input type="password" id="login-password" name="password" required />
        <label for="login-password">&nbsp;&nbsp;Password</label>
      </div>
      <button type="submit" name="login" class="submit-btn">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        Login
      </button>
      <div class="toggle-text">
        Don't have an account?
        <a href="./?register" id="show-register">Register</a>
      </div>
    </form>

    <!-- Register Form -->
    <form id="register-form" autocomplete="off" method="post" style="display: <?php echo $showLogin ? 'none' : 'block'; ?>;">
      <h2>Register Form</h2>
      <div class="user-box">
        <input type="text" id="register-username" name="username" required />
        <label for="register-username">&nbsp;&nbsp;Username</label>
      </div>
      <div class="user-box">
        <input type="password" id="register-password" name="password" required />
        <label for="register-password">&nbsp;&nbsp;Password</label>
      </div>
      <div class="user-box">
        <input type="email" id="register-email" name="email" required />
        <label for="register-email">&nbsp;&nbsp;Recovery Email</label>
      </div>
      <button type="submit" name="register" class="submit-btn">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        Register
      </button>
      <div class="toggle-text">
        Already have an account?
        <a href="./?login" id="show-login">Login</a>
      </div>
    </form>
  </div>
</body>
<script>
  setTimeout(() => {
  const msg = document.getElementById('messageDiv');
  if (msg) msg.style.display = 'none';
}, 1500);
</script>
</html>
