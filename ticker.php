<?php
session_start();
require_once ('classes/functions.php'); 

use classes\MySQL;

if (!$_SESSION) {
	header('Location:index.php');
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
	curl_setopt($ch, CURLOPT_URL, 'https://api.bitso.com/v3/ticker/');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, "true");
	
	$ticker_result = curl_exec($ch);
	$ticker_json   = json_decode($ticker_result);
	$ticker_array  = $ticker_json->payload;
	
	$currencys_prices = array();
	$currencys_percen = array();
	
	foreach ($ticker_array as $key => $value) {
		if( strpos($value->book, "_mxn") or strpos($value->book, "_usd") ){
			$currencys_prices[$value->book] = $value->last;
			$currencys_percen[$value->book] = $value->change_24;			
		}
	}
	?>	

	<body>
		<header class="p-3 mb-3 border-bottom border-custom bg-white shadow-sm">
			<div class="container">
		    	<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
			        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
			        	<li><a href="ticker.php" class="nav-link px-3 link-dark">Ticker</a></li>
			        	<li><a href="markets.php" class="nav-link px-3 link-dark">Markets</a></li>
			          	<li><a href="balance.php" class="nav-link px-3 link-dark">Balances</a></li>
			          	<li><a href="orders.php" class="nav-link px-3 link-dark">Orders</a></li>
			          	<li><a href="system.php" class="nav-link px-3 link-dark">System</a></li>
			          	<li><a href="profile.php" class="nav-link px-3 link-dark">Profile</a></li>
			        </ul>
		
			        <div class="dropdown text-end">
			        	<a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
			            	<img src="https://pbs.twimg.com/profile_images/1389588502703218688/l4ex-JeJ_400x400.jpg" alt="mdo" width="32" height="32" class="rounded-circle">
			          	</a>
			          	<ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
						  	<li><a class="dropdown-item" href="#"><?=$_SESSION['name']?></a></li>
				            <li><a class="dropdown-item" href="#">Profile</a></li>
				            <li><a class="dropdown-item" href="#">Sign out</a></li>
          				</ul>
			        </div>
		      	</div>
			</div>
		</header>		
		
		<div class="container">
			<div class="col-md-12 shadow-sm mb-4 bg-white">
				<div class="card border-custom">
					<div class="card-header">
						<h6 class="card-header-title">Ticker</h6>
						<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
					</div>				
					
					<div class="table-responsive">
						<table class="table table-borderless table-hover fs-6">
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
							
							$mysql = new MySQL();
							$query = "SELECT a.book, SUM(amount) as amount, SUM(price * amount) as value, b.file 
									  FROM wallet_balance a LEFT JOIN wallet_currencys b 
									  ON a.book = b.book WHERE status = 1 GROUP BY a.book";
							
							$data  = $mysql->mySQLquery($query);
		
							foreach ($data as $key => $value) {
								$change = $currencys_percen[$value->book]/$currencys_prices[$value->book] * 100;
								$current_value = $currencys_prices[$value->book] * $value->amount;
								$gain_lost = $current_value - $value->value;
								
								echo "<tr>";
								echo 	"<td class='text-center'><img src='currencys/$value->file' width='20px' height='20px'></td>";
								echo 	"<td><a href='currentbook.php?book=$value->book' class='link-secondary'>". $value->book ."</a></td>";
								echo 	"<td class='text-end'>". number_format($value->amount,8)."</td>";
								echo 	"<td class='text-end'>". convertMoney($currencys_prices[$value->book]) ."</td>";
								echo 	"<td class='text-end'>". number_format($change,2)."% "; icon_percent($change) ."</td>";
								echo 	"<td class='text-end'>". convertMoney($value->value) ."</td>";
								echo 	"<td class='text-end'>". convertMoney($current_value) ."</td>";
										showHtmlRow($gain_lost, $value->value, $current_value);
								echo "</tr>";
								
								$t_value 	 += $value->value;
								$t_bought	 += $current_value;
								$t_gain_lost += $gain_lost;
							}

				    		$min = select_min();
							$max = select_max();
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
				</div> 			<!-- Card -->
			</div>				<!-- Col-12 -->
			
			<div class="row mb-4">
				<div class="col">
					<div class="col-md-12">
						<div class="card rounded border border-custom shadow-sm">
							<div class="card-header">
								<h6 class="card-header-title">Wallet performance</h6>
								<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
							</div>
							<?php 
							$chart_data = array();
							$query = "SELECT date, amount 
									  FROM (SELECT id, DATE_FORMAT(date,'%l.%p') as date, TRUNCATE(amount,2) as amount 
									  FROM wallet_performance ORDER BY id DESC LIMIT 20) Tbl ORDER BY id ASC";
							$data = $mysql->mySQLquery($query);
							foreach ($data as $key => $chart) {
								$chart_data[$chart->date] = $chart->amount;
							} 
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
									    		<?="Less investment: ". $min[0]->book;?>
									    	</h6>
									    	<h5 class="card-subtitle mb-2 fs-6">
									    		<?=convertMoney($min[0]->value);?>
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
									    		<?="More investment: ". $max[0]->book;?>
									    	</h6>
									    	<h5 class="card-subtitle mb-2 fs-6">
									    		<?=convertMoney($max[0]->value);?>
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
									    		$oldest_buy = select_oldest_buy();
												echo "Oldest buy: ". $oldest_buy[0]->currency ." (". $oldest_buy[0]->book .")";
									    		?>
									    	</h6>
									    	<h5 class="card-subtitle mb-2 fs-6">
									    		<?=time_elapsed_str($oldest_buy[0]->elapsed);?>
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
									    		<?=getChange().' %';?>
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
</script>
 