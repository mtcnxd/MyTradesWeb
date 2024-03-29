<?php
session_start();
require_once ('classes/BitsoWallet.php');
require_once ('classes/Helpers.php');
require_once ('classes/functions.php'); 

use classes\BitsoWallet;
use classes\Helpers;
use classes\MySQL;

if (!$_SESSION || !$_SESSION['userid']) {
	header('Location:index.php');
}

$userId = $_SESSION['userid'];
$user = $_SESSION['name'];
$icon = "";

if (Helpers::isApiConfigured($userId)){
	$bitsoWallet = new BitsoWallet($userId);
	$userData = $bitsoWallet->getUserInformation();

	if ($userData){
		$user = $userData->first_name ." ". $userData->last_name;
		$icon = $userData->gravatar_img;	
	}
}

?>

<html>
	<head>
		<title>Bitso Wallet (<?=$user?>)</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- CSS only -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" 
				rel="stylesheet" 
				integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" 
				crossorigin="anonymous">
		<link href="css/custom.css" rel="stylesheet"/>				
				
		<!-- JavaScript Bundle with Popper -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" 
				integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" 
				crossorigin="anonymous">
		</script>		
	</head>
	
	<body>
		<header class="p-3 mb-3 border-bottom border-custom bg-white shadow-sm">
			<?php include('includes/mainmenu.php') ?>
		</header>	
		
		<div class="container">
			<div class="col-md-12 mb-4">
				<div class="card rounded border border-custom shadow-sm">
					<div class="card-header">
						<h6 class="card-header-title">System logs</h6>
						<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
					</div>

					<div class="table-responsive">
						<table class="table table-borderless table-hover fs-6">

							<thead>
								<tr class="table-custom text-uppercase fs-7">
									<th scope="col">#</td>
									<th scope="col">Date</td>
									<th scope="col">Error</td>									
								</tr>
							</thead>

							<?php

							$fcontent = Helpers::openLogfile();
							$textrow = explode('[', $fcontent);

							$i = 1;
							foreach($textrow as $textline){
								$fcols = explode(']', $textline);

								if (!empty($textline)){
									echo "<tr>";
									echo "	<td>".	$i ."</td>";
									echo "	<td>".	$fcols[0] ."</td>";
									echo "	<td>".	$fcols[1] ."</td>";
									echo "</tr>";
									$i++;
								}
							}
							
							/*
							Place and order params, book, side, price, amount

							$response = $bitsoWallet->placeOrder('mana_mxn','buy',15.0, 1);
							$json_object = json_decode($response);

							if ( isset($json_object->error->message) ){
								echo $json_object->error->message;
							}
							*/

							// Cancel an order by oid
							//$response = $bitsoWallet->cancelOpenOrder('mGNTjUN2GMWMNYxY');

							?>
							
						</table>
					</div>	<!-- Table -->
				</div>	<!-- Card -->
			</div>	<!-- Col-12 -->
		
		</div> 	<!-- Container -->
	</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
