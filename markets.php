<?php
session_start();
require_once ('classes/functions.php'); 
require_once ('classes/BitsoWallet.php'); 
require_once ('classes/mySQL.php'); 
require_once ('classes/Helpers.php'); 

use classes\BitsoWallet;
use classes\Helpers;
use classes\MySQL;

if (!$_SESSION || !$_SESSION['userid']) {
	header('Location:index.php');
}

if($_GET){
	$book = $_GET['book'];
} else {
	$book = 'btc_mxn';
}

$userId = $_SESSION['userid'];
$user = $_SESSION['name'];
$icon = null;
$bitsoTicker = null;
$prices = null;
$ticker = null;
$percent = null;
$priceData = [0 => 0];

if (Helpers::isApiConfigured($userId)){
	$bitsoWallet = new BitsoWallet($userId);
	$userData = $bitsoWallet->getUserInformation();

	$user = $userData->first_name ." ". $userData->last_name;
	$icon = $userData->gravatar_img;

	$bitsoTicker = $bitsoWallet->getFullTicker();
	$prices 	 = $bitsoWallet->getLastBoughtPrices($book);
	$ticker 	 = $bitsoWallet->getTicker();
	$priceData 	 = $bitsoWallet->getChartMarketData($book, 48);	

	$myPercent 	 = (($ticker[$book] - $prices->price)/$ticker[$book]) *100 ;
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
						<h6 class="card-header-title">Favorit markets</h6>
						<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
					</div>				
					
					<div class="table-responsive">
						<table class="table table-borderless table-hover fs-6">
							<thead>
								<tr class="table-custom text-uppercase fs-7">
									<th scope="col">Book</th>
									<th scope="col" class="text-end">Last buy/sell price</th>
									<th scope="col" class="text-end">Current Price</th>
									<th scope="col" class="text-end">Change</th>									
									<th scope="col" class="text-end">Low Price</th>
									<th scope="col" class="text-end">Last volume</th>
									<th scope="col" class="text-end">Change (24h)</th>
									<th scope="col" class="text-end">Minimun (24h)</th>
								</tr>
							</thead>

							<?php

							$favoritesList = Helpers::getCurrencysFavorites($userId);

							if ($bitsoTicker){
								foreach ($bitsoTicker as $key => $value) {

									if (in_array($key, $favoritesList)){

										$change_percent = ($value['change']/ $value['last']) * 100;
										$change_percent = number_format($change_percent, 2);

										$last_buy_price = $bitsoWallet->getLastBoughtPrices($key)->price;
										$change_24 		= $bitsoWallet->getMinimunMaximun($key);
										$calc_entry		= (($value['last'] - $change_24->minimum)/$value['last']) *100;

										$row_down = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-down"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg></td>';

										$row_up = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#00aa00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg></td>';

										echo "<tr>";
										echo 	"<td><a href='?book=$key' class='link-secondary'>". $key ."</a></td>";
										echo 	"<td class='text-end'>". convertMoney( $last_buy_price ) ."</td>";
										echo 	"<td class='text-end'>". convertMoney( $value['last'] ) ."</td>";
										if ($change_percent < 0){
											echo 	"<td class='text-end text-danger'>". $change_percent .'%'. $row_down;
										} else {
											echo 	"<td class='text-end text-success'>". $change_percent .'%'. $row_up;
										}
										echo 	"<td class='text-end'>". convertMoney( $value['low'] ) ."</td>";
										echo 	"<td class='text-end'>". convertMoney($change_24->volume) ."</td>";
										if ($calc_entry < 0){
											echo 	"<td class='text-end text-danger'>". number_format( $calc_entry,2 ) ."% </td>";
										} else {
											echo 	"<td class='text-end text-success'>". number_format( $calc_entry,2 ) ."% </td>";
										}
										echo 	"<td class='text-end'>". convertMoney($change_24->minimum) ."</td>";
										echo "</tr>";									

									}
								}
							}

							?>
						</table>
					</div>	<!-- Table-responsive -->
				</div>	<!-- Card -->
			</div>	<!-- Col-12 -->	


			<div class="row">
				<div class="col">
					<div class="card border border-custom shadow-sm rounded mb-4">
						<div class="card-header">
							<h6 class="card-header-title">Price</h6>
							<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pie-chart"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
						</div>
						<div class="card-body">
							<canvas class="p-3" id="priceChart" width="250" height="100"></canvas>							
						</div>
					</div>
				</div>

				<div class="col">
					<div class="card border border-custom shadow-sm rounded mb-4">
						<div class="card-header">
							<h6 class="card-header-title">Volume</h6>
							<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pie-chart"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
						</div>
						<div class="card-body">
							<canvas class="p-3" id="volumeChart" width="250" height="100"></canvas>							
						</div>
					</div>
				</div>
			</div> <!-- row -->

			<div class="row">

				<div class="col-md-6">
					<div class="card border border-custom shadow-sm rounded mb-4">
						<div class="card-header">
							<h6 class="card-header-title">Change of book <?=$book?> las 72 hrs</h6>
							<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pie-chart"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
						</div>
						<div class="card-body">
							<?php
							$dataCurrency = Helpers::getChangeCurrency($book, 72);
							$difference = ($dataCurrency['new_price']->price - $dataCurrency['old_price']->price);
							$percent 	= ($difference / $dataCurrency['new_price']->price) *100;

							echo '<ul class="list-group list-group-flush">';
							foreach ($dataCurrency as $row => $data){
								echo '<li class="list-group-item">
										<div class="row">
											<div class="col">'. $data->date .'</div>
											<div class="col text-end">'. convertMoney($data->price) .'</div>
										</div>
									</li>';
							}
								echo '<li class="list-group-item">
										<div class="row">
											<div class="col">Percentage </div>
											<div class="col text-end">'. number_format($percent, 2)  .'%</div>
										</div>
									</li>';
							echo '</ul>';
							?>
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<div class="card border border-custom shadow-sm rounded mb-4">
						<div class="card-header">
							<h6 class="card-header-title">Crypto BOT (Disable)</h6>
							<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pie-chart"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
						</div>
						<div class="card-body">
							<ul class="list-group list-group-flush">
							<?php
							if ($myPercent){
								if ($myPercent > 7){
									echo "<li class='list-group-item'>";
									echo "	<div class='row'>";
									echo "		<div class='col'>".$prices->amount." ". extractCurrency($book) ."</div>";
									echo "		<div class='col text-end'><span class='badge bg-success'>SELL</span></div>";
									echo "	</div>";
									echo "</li>";
									echo "<li class='list-group-item'>";
									echo "	<p>".$myPercent."</p>";
									echo "</li>";

								} else if ($myPercent < -5){
									echo "<li class='list-group-item'>";
									echo "	<div class='row'>";
									echo "		<div class='col'>Current price: ". convertMoney($ticker[$book]) ."</div>";
									echo "		<div class='col text-end'><span class='badge bg-danger'>BUY</span></div>";
									echo "	</div>";
									echo "</li>";
									echo "<li class='list-group-item'>";
									echo "	<p>".$myPercent."</p>";
									echo "</li>";

								} else {
									echo "<p class='card-text'>Waiting for buy: ".$myPercent."</p>";

								}	

							} else {
								echo "<p>Nothing here yet!</p>";
							}

							?>
							</ul>
						</div>
					</div>
				</div>

			</div> <!-- row -->
					
		</div> 	<!-- Container -->
	</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
<script>

<?php
foreach ($priceData as $key => $prices) {
	$priceArray[$key]  = $prices->price;
	$volumeArray[$key] = $prices->volume;
	$dateArray[$key]   = $prices->date;
}
?>

const priceDiv = document.getElementById('priceChart').getContext('2d');
const priceChart = new Chart(priceDiv, {
    type: 'line',
    data: {
        labels: <? echo json_encode($dateArray); ?>,
        datasets: [{
            label: 'Wallet Balance',
            data: <? echo json_encode($priceArray); ?>,
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

const volumeDiv = document.getElementById('volumeChart').getContext('2d');
const volumeChart = new Chart(volumeDiv, {
    type: 'line',
    data: {
        labels: <? echo json_encode($dateArray); ?>,
        datasets: [{
            label: 'Volume Chart',
            data: <? echo json_encode($volumeArray); ?>,
            fill: true,
            backgroundColor: 'rgba(128, 247, 104, 0.2)',
            borderColor: 'rgba(128, 247, 104, 1)',
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