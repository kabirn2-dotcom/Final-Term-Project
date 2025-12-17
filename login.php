<?php
//PHP session stores all user login data across pages
session_start();
$error = "";
//Checks to see if form was submitted 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST["email"] ?? "");
  $password = trim($_POST["password"] ?? "");
  //Check to see if user exists in the system
  if (!isset($_SESSION["user"])) {
    $error = "No user found. Create a user first.";
  } else {
    $u = $_SESSION["user"];
    //Compare email and password to the stored session email and passwords
    if ($email === $u["email"] && $password === $u["password"]) {
      header("Location: home.php");
      exit;
    } else {
      //Error handler
      $error = "Invalid email or password.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    /* Container for login form */
    .card {
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      width: 320px;
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 22px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-size: 14px;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      border-radius: 4px;
      border: 1px solid #ccc;
      margin-bottom: 15px;
      box-sizing:border-box;
    }
    /* Login button */
    button{
      width: 100%;
      padding: 10px;
      border-radius: 4px;
      border: none;
      font-size: 16px;
      background: #007bff;
      color: #fff;
      cursor: pointer;
    }

    button:hover {
      opacity: .9;
    }

    .error {
      color: #c00;
      font-size: 14px;
      margin-bottom: 10px;
      text-align: center;
    }

    .small {
      margin-top: 12px;
    }

    .create-btn {
      margin-top: 10px;
      background: #28a745;
    }

    .small a {
      text-decoration: none;
    }
  </style>
</head>

<body>
    <div class="card">
      <h1>Login</h1>
      <!-- Displays an error to the user -->
      <?php if ($error !== ""): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="post" action="">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Log In</button>
  </form>

  <div class="small">
    <a href="createuser.php"> <!-- References createuser on button press-->
      <button type="button" class="create-btn">Create User</button>
    </a>
  </div>
  </div>
</body>
</html>
