<?php
session_start();
require_once ('classes/functions.php');
require_once ('classes/BitsoWallet.php'); 
require_once ('classes/Helpers.php');

use classes\BitsoWallet;
use classes\Helpers;
use classes\MySQL;

if (!$_SESSION || !$_SESSION['userid']) {
	header('Location:index.php');
}

$userId = $_SESSION['userid'];
$user = $_SESSION['name'];
$icon = "";
$data = null;
$book = $_GET['book'];

if (Helpers::isApiConfigured($userId)){
	$bitsoWallet = new BitsoWallet($userId);
	$userData = $bitsoWallet->getUserInformation();

	$user = $userData->first_name ." ". $userData->last_name;
	$icon = $userData->gravatar_img;

	$data = $bitsoWallet->getListMyCurrencies($book);
}
?>

<html>
	<head>
		<title>Bitso Wallet (<?=$_SESSION['name']?>)</title>
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
	
	<?php	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.bitso.com/v3/ticker/?book='.$book);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, "true");
	
	$result = curl_exec($ch);
	$json_object = json_decode($result);
	$array_currency = $json_object;
	$currency_data  = $array_currency->payload; 
	$current_price  = $currency_data->last;
	?>		
	
	<body>
		<header class="p-3 mb-3 border-bottom border-custom bg-white shadow-sm">
			<?php include('includes/mainmenu.php') ?>
		</header>	
		
		<div class="container">
			<div class="row mb-4">

				<div class="col-md-3">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-body">
							<div class="align-items-center row">
								<div class="col">
							    	<h6 class="card-title text-muted text-uppercase fs-7">
							    		Minimum
							    	</h6>
							    	<h5 class="card-subtitle mb-2 fs-6">
							    		<?=convertMoney($currency_data->low);?>
							    	</h5>
						    	</div>
						    	<div class="col-auto">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#777777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
								</div>							    	
					    	</div>
					  	</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-body">
							<div class="align-items-center row">
								<div class="col">
							    	<h6 class="card-title text-muted text-uppercase fs-7">
							    		Current <?=extractCurrency($book)?> price
							    	</h6>
							    	<h5 class="card-subtitle mb-2 fs-6">
							    		<?=convertMoney($current_price)?>
							    	</h5>
						    	</div>
						    	<div class="col-auto">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#777777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
								</div>							    	
					    	</div>
					  	</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-body">
							<div class="align-items-center row">
								<div class="col">
							    	<h6 class="card-title text-muted text-uppercase fs-7">
							    		Maximum
							    	</h6>
							    	<h5 class="card-subtitle mb-2 fs-6">
							    		<?=convertMoney($currency_data->high);?>
							    	</h5>
						    	</div>
						    	<div class="col-auto">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#777777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
								</div>							    	
					    	</div>
					  	</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-body">
							<div class="align-items-center row">
								<div class="col">
							    	<h6 class="card-title text-muted text-uppercase fs-7">
							    		Volume
							    	</h6>
							    	<h5 class="card-subtitle mb-2 fs-6">
							    		<?=convertMoney($currency_data->volume);?>
							    	</h5>
						    	</div>
						    	<div class="col-auto">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#777777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
								</div>							    	
					    	</div>
					  	</div>
					</div>
				</div>				

			</div>

			<div class="col-md-12 shadow-sm mb-4 bg-white">
				<div class="card border-custom">
					<div class="card-header">
						<h6 class="card-header-title">Currency</h6>
					</div>
					<div class="table-responsive">
						<table class="table table-borderless table-hover fs-6">
							<thead>
								<tr class="table-custom text-uppercase fs-7">
									<th scope="col" class="text-end">Amount</td>
									<th scope="col" class="text-end">Buy price</td>
									<th scope="col" class="text-end">Difference</td>
									<th scope="col" class="text-end">Bought value</td>
									<th scope="col" class="text-end">Current value</td>
									<th scope="col" class="text-center">Time elapsed</td>							
									<th scope="col" class="text-end">Gain/Lost</td>
									<th scope="col" class="text-center">Action</td>
								</tr>
							</thead>
							<?php
							$total_gain   = 0;
							$total_amount = 0;
							$total_bought = 0;
							$total_value  = 0;

							if ($data){
								foreach ($data as $key => $value) {
									$bought_value  = $value->amount * $value->price;
									$current_value = $value->amount * $current_price;
									$gain_lost 	   = $current_value - $bought_value;
									$buying_date   = new DateTime($value->date);
									
									echo "<tr>";
									echo 	"<td class='text-end'>". $value->amount ."</td>";
									echo 	"<td class='text-end'>". convertMoney($value->price) ."</td>";
									echo 	"<td class='text-end'>". convertMoney($current_price - $value->price) ."</td>";
									echo 	"<td class='text-end'>". convertMoney($bought_value) ."</td>";				
									echo 	"<td class='text-end'>". convertMoney($current_value) ."</td>";
									echo 	"<td class='text-center' data-bs-toggle='tooltip' data-bs-placement='right' title='".$buying_date->format('d-m-Y h:i a')."'>". time_elapsed($value->time_elapsed) ."</td>";
											showHtmlRow($gain_lost, $value->price, $current_price);
									echo 	'<td class="text-center align-middle">
												<a id="'. $value->id .'" onclick="erase(this.id)">
													<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#777777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
												</a>
											</td>';
									echo "</tr>";
									
									$total_amount += $value->amount;						
									$total_bought += $bought_value; 
									$total_value  += $current_value;
									$total_gain   += $gain_lost;
									
								}								

							}	
							?>
						</table>
					</div> 	<!-- Table-responsive -->
				</div> 	<!-- Card -->
			</div>	<!-- Col -->
			
			<div class="row mb-4">
				<div class="col-md-3">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-body">
							<div class="align-items-center row">
								<div class="col">
							    	<h6 class="card-title text-muted text-uppercase fs-7">Amount</h6>
							    	<h5 class="card-subtitle mb-2 fs-6 text-uppercase">
							    		<?=$total_amount;?> 
							    		<?=extractCurrency($book);?>
							    	</h5>
						    	</div>
						    	<div class="col-auto">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#777777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
								</div>							    	
					    	</div>
					  	</div>
					</div>
				</div>
				
				<div class="col-md-3">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-body">
							<div class="align-items-center row">
								<div class="col">
							    	<h6 class="card-title text-muted text-uppercase fs-7">Bought value</h6>
							    	<h5 class="card-subtitle mb-2 fs-6"><?=convertMoney($total_bought);?></h5>
							  	</div>
							  	<div class="col-auto">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#777777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
							  	</div>
						  	</div>
					  	</div>
					</div>
				</div>
				
				<div class="col-md-3">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-body">
							<div class="align-items-center row">
								<div class="col">
						    		<h6 class="card-title text-muted text-uppercase fs-7">Current value</h6>
						    		<h5 class="card-subtitle mb-2 fs-6"><?=convertMoney($total_value);?></h5>
					    		</div>
					    		<div class="col-auto">
					    			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#777777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
					    		</div>
					    	</div>
					  	</div>
					</div>
				</div>
				
				<div class="col-md-3">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-body">
							<div class="align-items-center row">
								<div class="col">
						    		<h6 class="card-title text-muted text-uppercase fs-7">Gain/Lost</h6>
						    		<h5 class="card-subtitle mb-2 fs-6"><?=convertMoney($total_gain);?></h5>
						    	</div>
					    		<div class="col-auto">
					    			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#777777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
					    		</div>
					    	</div>
					  	</div>
					</div>
				</div>						
			</div>		<!-- Row -->
		</div> 			<!-- Container -->
	</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script language="JavaScript">
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	})


	function erase(id){
		var id = id;
		
		$.post("backend.php",{
			option:'delete_db',
			id:id
			}, function(response){
			console.log(response);
			history.go(0);
		});
	}
</script>