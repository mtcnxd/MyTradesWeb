<?php
session_start();
require_once ('classes/functions.php'); 
require_once ('classes/BitsoWallet.php'); 
require_once ('classes/Helpers.php'); 

use classes\Helpers;
use classes\BitsoWallet;

if (!$_SESSION || !$_SESSION['userid']) {
	header('Location:index.php');
}

$userId = $_SESSION['userid'];

if (!Helpers::isApiConfigured($userId)){
	$bitsoWallet = new BitsoWallet($userId);
	$userData = $bitsoWallet->getUserInformation();

	$user = $userData->first_name ." ". $userData->last_name;
	$icon = $userData->gravatar_img;
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
			<div class="row">
				<div class="col-md-12">
					<div class="card border border-custom shadow-sm rounded mb-4">
						<div class="card-header">
							<h6 class="card-header-title">History Chart</h6>
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart"><line x1="12" y1="20" x2="12" y2="10"></line><line x1="18" y1="20" x2="18" y2="4"></line><line x1="6" y1="20" x2="6" y2="16"></line></svg>
						</div>
						<div class="card-body">
							<canvas class="p-3" id="allHistory" height="300" width="1100"></canvas>							
						</div>
					</div>
				</div>	<!-- col-12 -->
			</div>

			<div class="row">
				<div class="col">
					<div class="card rounded border border-custom shadow-sm mb-4">
						<div class="card-header">
							<h6 class="card-header-title">Balances</h6>
							<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
						</div>
						
						<ul class="list-group list-group-flush">
						<?php
						$value_total   = 0;
						$buy_power     = 0;
						$chart_data    = array();
						$balance_array = $bitsoWallet->getWalletBalances();
						
						foreach ($balance_array as $balance){
							$chart_data[$balance['currency']] = $balance['value'];
							$value_total += $balance['value'];

							if ($balance['currency'] == 'mxn' || $balance['currency'] == 'usd'){
								$buy_power += $balance['value'];
							} 

							echo "<li class='list-group-item list-group-item-action'>";
							echo "	<div class='ms-2'>
								  		<div class='fw-bold text-uppercase'>". $balance['currency'] ."</div>
										<div class='row'>
											<div class='col-md-6'>". $balance['amount'] ."</div>
											<div class='col-md-6 text-end'>". convertMoney($balance['value']) ."</div>
										</div> 
							  		</div>";
							echo "</li>";
						}
						
						$buy_power_percent = ($buy_power/$value_total) * 100;
						?>
						</ul>
					</div>	<!-- Card -->

					<div class="row">
						<div class="col-md-6">
							<div class="card border-custom shadow-sm mb-4">
								<div class="card-body">
									<div class="align-items-center row">
										<div class="col">
											<h6 class="card-title text-muted text-uppercase fs-7">
												Buying power <?=" (".number_format($buy_power_percent,2) ."%)";?>
											</h6>
											<h5 class="card-subtitle mb-2 fs-6">
												<?=convertMoney($buy_power);?>
											</h5>			
										</div>
										<div class="col-auto">					
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
										</div>
									</div>
								</div>
							</div>	<!-- Card -->
						</div>

						<div class="col-md-6">
							<div class="card border-custom shadow-sm mb-4">
								<div class="card-body">
									<div class="align-items-center row">
										<div class="col">
											<h6 class="card-title text-muted text-uppercase fs-7">
												Total wallet
											</h6>
											<h5 class="card-subtitle mb-2 fs-6">
												<?=convertMoney($value_total);?>
											</h5>			
										</div>
										<div class="col-auto">					
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
										</div>
									</div>
								</div>
							</div>
						</div> <!-- col-md-6 -->
					</div>	<!-- row -->

					<div class="row">
						<div class="col-md-6">
							<div class="card border-custom shadow-sm mb-4">
								<div class="card-body">
									<div class="align-items-center row">
										<div class="col">
											<h6 class="card-title text-muted text-uppercase fs-7">
												Current performance
											</h6>
											<h5 class="card-subtitle mb-2 fs-6">
												<?=getCurrentChange($value_total) .'%';?>
											</h5>			
										</div>
										<div class="col-auto">					
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-percent"><line x1="19" y1="5" x2="5" y2="19"></line><circle cx="6.5" cy="6.5" r="2.5"></circle><circle cx="17.5" cy="17.5" r="2.5"></circle></svg>
										</div>
									</div>
								</div>								
							</div>
						</div>

						<div class="col-md-6">
							<div class="card border-custom shadow-sm mb-4">
								<div class="card-body">
									<div class="align-items-center row">
										<div class="col">
											<h6 class="card-title text-muted text-uppercase fs-7">
												Performance last 24 hours
											</h6>
											<h5 class="card-subtitle mb-2 fs-6">
												<?=getPerformanceIntime(24) .'%';?>
											</h5>			
										</div>
										<div class="col-auto">					
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-percent"><line x1="19" y1="5" x2="5" y2="19"></line><circle cx="6.5" cy="6.5" r="2.5"></circle><circle cx="17.5" cy="17.5" r="2.5"></circle></svg>
										</div>
									</div>
								</div>								
							</div>
						</div>
					</div>	<!-- row -->
				</div>	<!-- col -->

			
				<div class="col">
					<div class="row">
						<div class="col-md-12">
							<div class="card border border-custom shadow-sm rounded mb-4">
								<div class="card-header">
									<h6 class="card-header-title">Distribution</h6>
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
								</div>
								<div class="card-body">
									<canvas class="p-3" id="myChart" width="250" height="100"></canvas>							
								</div>
							</div>
						</div>	<!-- col -->
					</div> <!-- row -->


					<div class="row">
						<div class="col">
							<div class="card border border-custom shadow-sm rounded mb-4">
								<div class="card-header">
									<h6 class="card-header-title">History last 24 hours</h6>
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
								</div>

								<div class="card-body">
									<?php
									//$historyData = $bitsoWallet->getBalanceHistory(24);

									echo "<table class='table table-hover'>";
									foreach($historyData as $key => $value){
										$newDate = new DateTime($value->date);
										echo "<tr>";
										echo "    <td class='fw-bold'>". ($key + 1) ."</td>";
										echo "    <td>". $newDate->format('h:i:s a') ."</td>";								
										echo "    <td>". convertMoney($value->amount) ."</td>";
										echo "    <td class='fw-bold text-end'>". $value->difference ."% </td>";
										echo "</tr>";
									}
									echo "</table>";

									?>
								</div>
							</div>				
						</div>
					</div> <!-- row -->
				</div>	<!-- col -->
			</div>	<!-- row -->

		</div> 	<!-- container -->
	</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
<script>
const ctx = document.getElementById('myChart').getContext('2d');
const distributionChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['<?=implode("','", array_keys($chart_data));?>'],
        datasets: [{
            label: 'Wallet Balance',
            data: [<?=implode(",", array_values($chart_data));?>],
            backgroundColor: [
                'rgba(235, 52, 95, 0.7)',
                'rgba(129, 247, 166, 0.7)',
                'rgba(252, 232, 96, 0.7)',
                'rgba(184, 184, 184, 0.7)',
                'rgba(235, 52, 192, 0.7)',
                'rgba(78, 189, 245, 0.7)',
                'rgba(59, 176, 40, 0.7)',
                'rgba(52, 235, 122, 0.7)'
            ],
            borderColor: [
                'rgba(235, 52, 95, 1)',
                'rgba(129, 247, 166, 1)',
                'rgba(252, 232, 96, 1)',
                'rgba(184, 184, 184, 1)',
                'rgba(235, 52, 192, 1)',
                'rgba(78, 189, 245, 1)',
                'rgba(59, 176, 40, 1)',
                'rgba(52, 235, 122, 1)'
            ],
            borderWidth: 1,
            hoverOffset: 5
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

<?php
//$dataChart = $bitsoWallet->getAverageHistory();
foreach ($dataChart as $key => $amount) {
	$values[$key]  = $amount->amount;
	$labels[$key]  = $amount->newdate;
}
?>

const historyDiv = document.getElementById('allHistory').getContext('2d');
const historyChart = new Chart(historyDiv, {
    type: 'line',
    data: {
        labels: <?=json_encode( $labels );?>,
        datasets: [{
            label: 'Wallet Balance',
            data: <?=json_encode( $values );?>,
            borderColor: 'rgba(128, 247, 104, 1)',
            backgroundColor: 'rgba(128, 247, 104, 0.5)',
            borderWidth: 1,
            pointRadius: 0,
            hoverOffset: 5,
            fill: true
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

