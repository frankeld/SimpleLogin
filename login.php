<?php
	require 'setup.php';
	
	if (isset($_SESSION['username'])) { //Person is already logged in
		header('Location: member.php');
		exit("You are already logged in. Redirecting to member page..."); //Automatically closes MySQL connection and sends to logged in page
	}

	if (isset($_POST["login"]) and fieldExist()) {
		//Login code copied on 2/24/18 from MySQL2-BasicLogin
		$checkUser =  mysqli_prepare($databaseSQL, "SELECT password FROM userDB WHERE username=?;");
		mysqli_stmt_bind_param($checkUser, 's', $name);

		$name = $_POST["username"]; //Grabs name and password entered from POST
		$password = $_POST["passwrd"];

		mysqli_stmt_execute($checkUser); //System of prepared execution prevents SQL Injection
		mysqli_stmt_store_result($checkUser);
		mysqli_stmt_bind_result($checkUser, $userPasswordHash);
		mysqli_stmt_fetch($checkUser);
		if (password_verify($_POST["passwrd"], $userPasswordHash)) {
			echo "Logged in.";
			$_SESSION['username'] = $name;
			header('Location: member.php');
			exit("Welcome. Redirecting to member page..."); //Automatically closes MySQL connection and sends to logged in page
		} else {
			echo "Username or password incorrect.";
		}
		mysqli_stmt_free_result($checkUser);
		mysqli_stmt_close($checkUser);
	} else { // Form generation if submit has not been pressed
		echo '<!DOCTYPE HTML>
		<html>
		<head><meta charset="utf-8">
		<title>David Frankel\'s Fancy Login Page</title>
		<meta name="description" content="Less basic login!">
		<meta name="author" content="David Frankel"></head>
		<body>';
		echo '<form action="login.php" method="post">
			Username: <input type="text" name="username" required><br>
			Password: <input type="password" name="passwrd" required><br>
			<input type="submit" value="Login" name="login">
			</form>';
		echo '<form action="register.php">I don\'t have an account.<input type="submit" value="Take me to registration!" /></form>';
	}

	mysqli_close($databaseSQL); //Closes socket to MySQL! Important!

	function fieldExist() {
		if ($_POST["username"] !== "" and $_POST["passwrd"] !== "") {
			return true;
		} else {
			echo "You did not enter anything. Try again.";
			return false;
		}
	}
?>