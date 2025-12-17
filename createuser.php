<?php
session_start();
$error = "";
$success = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");
    if ($email !== "" && $password !== "") {
        $_SESSION["user"] = ["email"=>$email,"password"=>$password];
        $success = "User created. You can log in.";
    } else {
        $error = "Please enter an email and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create User</title>
  <style>
    body{
      font-family:Arial,sans-serif;
      background:#f4f4f4;
      display:flex;
      align-items:center;
      justify-content:center;
      height:100vh;
      margin:0;
    }
    .card{
      background:#fff;
      padding:30px;
      border-radius:8px;
      box-shadow:0 2px 8px rgba(0,0,0,0.1);
      width:320px;
    }
    h1{
      text-align:center;
      margin-bottom:20px;
      font-size:22px;
    }
    label{
      display:block;
      margin-bottom:5px;
      font-size:14px;
    }
    input[type="email"],
    input[type="password"]{
      width:100%;
      padding:10px;
      border-radius:4px;
      border:1px solid #ccc;
      margin-bottom:15px;
      box-sizing:border-box;
    }
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
    button:hover{
      opacity:.9;
    }
    .error{
      color:#c00;
      font-size:14px;
      margin-bottom:10px;
      text-align:center;
    }
    .success{
      color:#0a7a1f;
      font-size:14px;
      margin-bottom:10px;
      text-align:center;
    }
    .small{
      text-align:center;
      margin-top:12px;
      font-size:13px;
    }
    .small a{
      color:#007bff;
      text-decoration:none;
    }
  </style>
</head>
<body>
  <div class="card">
    <h1>Create User</h1>
    <?php if ($error !== ""): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success !== ""): ?>
      <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="post" action="">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Create User</button>
    </form>
    <div class="small">
      <a href="login.php">Back to Login</a>
    </div>
  </div>
</body>
</html>
