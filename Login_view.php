<?php 
session_start(); 
echo "<pre>"; print_r($_SESSION); echo "</pre>"; 
?>

<html>
	<head>
		<?php require_once('header.php'); ?>
	</head>
<body>
	<div class='container custom-container'>
	<div  class='panel panel-primary'>
		<div class='panel-heading'>
		<div class='pnnel-title'>
		<h2 class=''>Scheduler Login</h2>
		</div>
		</div>

		<!-- User Name divs-->
		<div class='well' style='margin-bottom:0px'>
		
		<!-- Employee ID divs-->
		<form class='form-horizontal' action='login.php' method='post'>
		  <div class='form-group'>
			<label for='Username' class='col-sm-2'/>Employee ID</label>
			<div class='input-group'>
				<div class='input-group-addon'>
					<i class='glyphicon glyphicon-user' aria-hidden='true'></i>
				</div>
				<div class='col-sm-10'>
					<input type='number' class='form-control' id='empID' name='empID' placeholder='#' autofocus>
				</div>
			</div>
		  </div>
		<!-- End Employee ID div -->

		<!-- Password divs-->
		  <div class='form-group'>
			<label for='Password' class='col-sm-2'>Password</label>
			<div class='input-group'>
				<div class='input-group-addon'>
					<span class='glyphicon glyphicon-lock' aria-hidden='true'></span>
				</div>
				<div class='col-sm-10'>
					<input type='password' class='form-control' id='password' name='password' placeholder='Password'>
				</div>
			</div>
		  </div>
		<!-- End Password div-->

		<div class='form-group'>
			<div class=' col-sm-offset-2 col-sm 10'>
				<button id='submitButton' type='submit' class='btn btn-primary'>Sign In</button>
			</div>
		</div>
		</form>
		</div>
	</div>

	<?php 
	if(isset($_SESSION['access']) && $_SESSION['access'] === 'denied'): ?>
	<div class='alert alert-danger' role='alert'>
		<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
		<span class='sr-only'>Error:</span>
		Username or Password is incorrect. Please try again.
	</div>
	<?endif;?>
</div>
</body>
</html>