<?php

session_start();

// hold error messages for the login form
$error = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get email and password 
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    // If no user has been created 
    if (!isset($_SESSION["user"])) {
        $error = "No user found. Create a user first.";
    } else {
        // Retrieve the stored user
        $u = $_SESSION["user"];

        // Check if the entered email and password match the stored ones
        if ($email === $u["email"] && $password === $u["password"]) {
            // If correct, redirect
            header("Location: home.php");
            exit;
        } else {
            // If incorrect, show error 
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
    /* Basic page layout and font */
    body{
      font-family:Arial,sans-serif;
      background:#f4f4f4;
      display:flex;
      align-items:center;
      justify-content:center;
      height:100vh;
      margin:0;
    }

    /* Card container for the login box */
    .card{
      background:#fff;
      padding:30px;
      border-radius:8px;
      box-shadow:0 2px 8px rgba(0,0,0,0.1);
      width:320px;
    }

    /* Title styling */
    h1{
      text-align:center;
      margin-bottom:20px;
      font-size:22px;
    }

    /* Label styling */
    label{
      display:block;
      margin-bottom:5px;
      font-size:14px;
    }

    /* email and password */
    input[type="email"],
    input[type="password"]{
      width:100%;
      padding:10px;
      border-radius:4px;
      border:1px solid #ccc;
      margin-bottom:15px;
      box-sizing:border-box;
    }

    /* Log In and Create User */
    button{
      width:100%;
      padding:10px;
      border-radius:4px;
      border:none;
      font-size:16px;
      background:#007bff;
      color:#fff;
      cursor:pointer;
    }

    /* Hover effect for buttons */
    button:hover{
      opacity:.9;
    }

    /* Error message styling */
    .error{
      color:#c00;
      font-size:14px;
      margin-bottom:10px;
      text-align:center;
    }

    /* Container for the "Create User" button */
    .small{
      margin-top:12px;
    }

    /* Extra styling */
    .create-btn{
      margin-top:10px;
      background:#28a745;
    }

    
    .small a{
      text-decoration:none;
    }
  </style>
</head>
<body>
  <div class="card">
    
    <h1>Login</h1>

    
    <?php if ($error !== ""): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    
    <form method="post" action="">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Log In</button>
    </form>

    
    <div class="small">
      <a href="create_user.php">
        <button type="button" class="create-btn">Create User</button>
      </a>
    </div>
  </div>
</body>
</html>
