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
$user   = $_SESSION['name'];
$icon   = null;
$result = null;
$lastChange = null;
$wallet_data = array();
$chart_data  = array();

if (Helpers::isApiConfigured($userId)){
	$bitsoWallet = new BitsoWallet($userId);
	$userData = $bitsoWallet->getUserInformation();

	if ($userData){
		$user = $userData->first_name ." ". $userData->last_name;
		$icon = $userData->gravatar_img;	
	}

	$result = $bitsoWallet->getCurrencysBought();
	$wallet_data  = $bitsoWallet->getChartPerformance();
	$ticker_array = $bitsoWallet->getFullTicker();
	$lastChange = $bitsoWallet->getLastPriceChange();
		
	foreach ($ticker_array as $key => $value) {
		$currencys_prices[$key] = $value['last'];
		$currencys_percen[$key] = $value['change'];
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
			<div class="col-md-12 shadow-sm mb-4 bg-white">
				<div class="card border-custom">
					<div class="card-header">
						<h6 class="card-header-title">Your Assets</h6>
						<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
					</div>				
					
					<div class="table-responsive">
						<table class="table table-bg table-borderless table-hover fs-6">
							<thead>
								<tr class="table-custom text-uppercase fs-7">
									<th scope="col"></th>
									<th scope="col">Book</th>
									<th scope="col" class="text-end">Amount</th>
									<th scope="col" class="text-end">Current Price</th>
									<th scope="col" class="text-end">Change</th>					
									<th scope="col" class="text-end">Bought Value</th>
									<th scope="col" class="text-end">Current Value</th>
									<th scope="col" class="text-end">Gain/Lost</th>
								</tr>
							</thead>
							<?php		
							$t_value = 0;
							$t_bought = 0;
							$t_gain_lost = 0;

							if ($result){
								foreach ($result as $bought) {
									$change 	   = $currencys_percen[$bought->book]/$currencys_prices[$bought->book] * 100;
									$current_value = $currencys_prices[$bought->book] * $bought->amount;
									$gain_lost 	   = ($current_value - $bought->value);
									
									echo "<tr>";
									echo 	"<td class='text-center'><img src='currencys/$bought->file' width='20px' height='20px'></td>";
									echo 	"<td><a href='currentbook.php?book=$bought->book' class='link-dark'>".$bought->currency."</a><span class='text-muted'> (". $bought->book .")</span></td>";
									echo 	"<td class='text-end'>". number_format($bought->amount,8)."</td>";
									echo 	"<td class='text-end'>". convertMoney($currencys_prices[$bought->book]) ."</td>";
									echo 	"<td class='text-end'>". number_format($change,2)."% "; icon_percent($change) ."</td>";
									echo 	"<td class='text-end'>". convertMoney($bought->value) ."</td>";
									echo 	"<td class='text-end'>". convertMoney($current_value) ."</td>";
											showHtmlRow($gain_lost, $bought->value, $current_value);
									echo "</tr>";
									
									$t_value 	 += $bought->value;
									$t_bought	 += $current_value;
									$t_gain_lost += $gain_lost;
								}
							}
							?>
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td class="text-end fw-bold"><?=convertMoney($t_value);?></td>
								<td class="text-end fw-bold"><?=convertMoney($t_bought);?></td>
								<td class="text-end fw-bold"><?=convertMoney($t_gain_lost);?></td>				
							</tr>
						</table>
					</div>		<!-- Table-responsive -->
				</div> 		<!-- Card -->
			</div>		<!-- Col-12 -->
			
			<div class="row mb-4">
				<div class="col">
					<div class="col-md-12">
						<div class="card rounded border border-custom shadow-sm">
							<div class="card-header">
								<h6 class="card-header-title">Wallet performance</h6>
								<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
							</div>
							<?php

							if ($wallet_data){
								foreach ($wallet_data as $chart) {
									$chart_data[$chart->date] = $chart->amount;
								}
							}

				    		$min = Helpers::getMinimumInvestment($userId);
							$max = Helpers::getMaximumInvestment($userId);
							?>					
							<canvas class="p-3" id="myChart" width="250" height="100"></canvas>
						</div>
					</div>					
				</div>
				
				<div class="col">
					<div class="row mb-4">
						<div class="col-md-6">
							<div class="card border-custom shadow-sm">
								<div class="card-body">
									<div class="align-items-center row">
										<div class="col">
									    	<h6 class="card-title text-muted text-uppercase fs-7">
									    		<?="More investment MXN: ". extractCurrency($min->book);?>
									    	</h6>
									    	<h5 class="card-subtitle mb-2 fs-6">
									    		<?=convertMoney($min->value);?>
									    	</h5>
								    	</div>
								    	<div class="col-auto">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d60f0f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trending-down"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline></svg>
										</div>							    	
							    	</div>
							  	</div>													
							</div>
						</div>								
						
						<div class="col-md-6">
							<div class="card border-custom shadow-sm">
								<div class="card-body">
									<div class="align-items-center row">
										<div class="col">
									    	<h6 class="card-title text-muted text-uppercase fs-7">
									    		<?="More investment USD: ". extractCurrency($max->book);?>
									    	</h6>
									    	<h5 class="card-subtitle mb-2 fs-6">
									    		<?=convertMoney($max->value);?>
									    	</h5>
								    	</div>
								    	<div class="col-auto">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#32a852" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trending-up"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
										</div>							    	
							    	</div>
							  	</div>													
							</div>
						</div>	<!-- Col-6 -->
					</div>		<!-- Row -->
					
					<div class="row mb-4">
						<div class="col-md-6">
							<div class="card border-custom shadow-sm">
								<div class="card-body">
									<div class="align-items-center row">
										<div class="col">
									    	<h6 class="card-title text-muted text-uppercase fs-7">
									    		<?php
									    		$oldest_buy = Helpers::getOldestBuy($userId);
												echo "Oldest buy: ". $oldest_buy->currency ." (". $oldest_buy->book .")";
									    		?>
									    	</h6>
									    	<h5 class="card-subtitle mb-2 fs-6">
									    		<?=time_elapsed_str($oldest_buy->elapsed);?>
									    	</h5>
								    	</div>
								    	<div class="col-auto">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
										</div>							    	
							    	</div>
							  	</div>													
							</div>
						</div>

						<div class="col-md-6">
							<div class="card border-custom shadow-sm">
								<div class="card-body">
									<div class="align-items-center row">
										<div class="col">
									    	<h6 class="card-title text-muted text-uppercase fs-7">
									    		Last wallet performance
									    	</h6>
									    	<h5 class="card-subtitle mb-2 fs-6">
									    		<?php
												if($lastChange){
													echo $lastChange;
												}
												?>
									    	</h5>
								    	</div>
								    	<div class="col-auto">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#32a852" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trending-up"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
										</div>							    	
							    	</div>
							  	</div>													
							</div>
						</div>	<!-- Col-6 -->

					</div>	<!-- Row -->					
				</div>	<!-- Col -->
			</div>	<!-- Row -->
		</div> 	<!-- Container -->
		
		<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
			<div class="toast align-items-center text-black bg-warning border-0" id="liveToast" role="alert" aria-live="assertive" aria-atomic="true">		
		    	<div class="d-flex">
			    	<div class="toast-body">
			    		<p id="message" class="mt-1 mb-1">Mensaje</p>
			    	</div>
					<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>			    	
		    	</div>  		    	
		  	</div>
		</div>	
		
	</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script language="JavaScript">
	var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
	var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
		return new bootstrap.Dropdown(dropdownToggleEl)
	})

	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	})

	$(document).ready(function(){
		var amount = document.getElementById("amount");
		var price  = document.getElementById("price");
		var value  = document.getElementById("value");
		
		amount.addEventListener('keyup', function(){
			var plus = amount.value * price.value;
			value.value = plus;
			console.log(plus);
		});
		
		price.addEventListener('keyup', function(){
			var plus = amount.value * price.value;
			value.value = plus;
			console.log(plus);
		});		

	});
	
	function insert_db(){
		var toastLive = document.getElementById('liveToast');		
		var toast = new bootstrap.Toast(toastLive);	
		var amount = $("#amount").val();
		var price  = $("#price").val();
		var book   = $("#book").val();
		
		$("#bg_actions").load("backend.php",{
			option:'insert_db',
			amount:amount,
			price:price,
			book:book
		}, function(response){
			$("#message").text("Message: " + response);			
		    toast.show();
		});
	}
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
<script>
const ctx = document.getElementById('myChart').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['<?=implode("','", array_keys($chart_data));?>'],
        datasets: [{
            label: 'Wallet Balance',
            data: [<?=implode(",", array_values($chart_data));?>],
            fill: true,
            backgroundColor: 'rgba(252, 186, 3, 0.2)',
            borderColor: 'rgba(252, 186, 3, 1)',
            borderWidth: 1,
            hoverOffset: 1,
            pointRadius: 2,
            tension: 0.4
       }]
    },
    options: {
		responsive: true,
    	plugins: {
	      	legend: {
	        	position: 'none',
	        	align:'center',
	        	labels:{
	        		padding:25,
	        		boxWidth: 18,
	        		boxHeight: 17
	        	}
	      	}
      	}
    }
});
</script>
 