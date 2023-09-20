<?php
	require_once('./database_functions.php');
	if (isset($_POST['deleteUsersTable'])) {
		dropTable('users');
	}
	if (isset($_POST['deleteUser'])) {
		removeUser($_POST['deleteUser']);
	}
?>

<html>
	<head>
		<title>Delete Users Table Page</title>
	</head>
	<body>
		<form method="post">
			<input type="submit" name="deleteUsersTable" value="Delete Users Table (DO NOT CLICK)"/>
		</form>
		<form method="post">
		    <input type="text" name="deleteUser">
			<input type="submit" value="Delete User (DO NOT CLICK)"/>
		</form>
	</body>
<html>