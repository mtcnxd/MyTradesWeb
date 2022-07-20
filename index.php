<?php
require_once('classes/functions.php');

use classes\MySQL;

if($_POST){
	$username = $_POST['username'];
	$password = $_POST['password'];

	$mysql  = new MySQL();
	$query 	= "SELECT * FROM wallet_users a LEFT JOIN wallet_config b ON a.id = b.user 
			   WHERE username = '$username' and password = '$password'";
	$result = $mysql->mySQLquery($query);

	foreach($result as $value){
		if ($username == $value->username && $password == $value->password){
			session_start();
			$_SESSION['userid'] = $value->id;
			$_SESSION['name']   = $value->username;
			$_SESSION['email']  = $value->email;
			
			header('Location: ticker.php');

		} else {
			header('Location: index.php');
		}
	}
}
?>

<html>
	<head>
		<title>API Bitso Test</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- CSS only -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" 
				rel="stylesheet" 
				integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" 
				crossorigin="anonymous">
				
		<!-- JavaScript Bundle with Popper -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" 
				integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" 
				crossorigin="anonymous">
		</script>
	</head>
	
	<body class="bg-light">
		<div class="col-md-5 p-2 position-absolute top-50 start-50 translate-middle">
			<div class="header">
				<h3>Login</h3>
				<hr>
				<form action="index.php" method="post">
					<div class="col-md-12 shadow-sm p-3 mb-5 rounded border bg-white" id="bg_actions">
						<p>Please, fill the form for login.</p>		
						<div class="mb-3 row">
							<label for="staticEmail" class="col-sm-2 col-form-label">Username</label>
							<div class="col-sm-10">
								<input type="text" name="username" id="username" class="form-control" placeholder="Username" autocomplete="off"/>
							</div>
						</div>
							
						<div class="mb-3 row">
							<label for="staticEmail" class="col-sm-2 col-form-label">Password</label>			
							<div class="col-md-10">
								<input type="password" name="password" id="password" class="form-control" placeholder="Password" autocomplete="off"/>
							</div>
						</div>
							
						<div class="d-grid gap-2 d-md-flex justify-content-md-end">
							<button class="btn btn-primary" type="submit">Login</button>
						  	<button class="btn btn-primary" type="reset">Cancel</button>
						</div>
					</div>
				</form>
			</div> <!--// Header //-->
		</div> <!--// Container //-->
	</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" 
	integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" 
	crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js">
</script>