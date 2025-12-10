<?php
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
$user = trim($_POST["username"] ?? "");
$pass = trim($_POST["password"] ?? "");
if ($user !== "" && $pass !== "") {
header("Location: home.php");
exit;
} else {
$error = "Please enter a username and password.";
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | My Website</title>
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
input[type="text"],
input[type="password"] {
width: 100%;
padding: 10px;
border-radius: 4px;
border: 1px solid #ccc;
margin-bottom: 15px;
box-sizing: border-box;
}
button {
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
opacity: 0.9;
}
.error {
color: #c00;
font-size: 14px;
margin-bottom: 10px;
text-align: center;
}
</style>
</head>
<body>
<div class="card">
<h1>Member Login</h1>
<?php if ($error !== ""): ?>
<div class="error"><?php echo $error; ?></div>
<?php endif; ?>
<form method="post" action="">
<label for="username">Username</label>
<input type="text" id="username" name="username" required>

<label for="password">Password</label>
<input type="password" id="password" name="password" required>

<button type="submit">Log In</button>
</form>
</div>
</body>
</html>