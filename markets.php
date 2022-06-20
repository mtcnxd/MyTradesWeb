<?php
session_start();
require_once ('classes/functions.php'); 
require_once ('classes/BitsoWallet.php'); 

use classes\BitsoWallet;

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
						<h6 class="card-header-title">Favorit markets</h6>
						<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
					</div>				
					
					<div class="table-responsive">
						<table class="table table-borderless table-hover fs-6">
							<thead>
								<tr class="table-custom text-uppercase fs-7">
									<th scope="col">Book</th>
									<th scope="col" class="text-end">Last buy price</th>
									<th scope="col" class="text-end">Current Price</th>
									<th scope="col" class="text-end">Change</th>									
									<th scope="col" class="text-end">Low Price</th>
									<th scope="col" class="text-end">Change (24h)</th>
									<th scope="col" class="text-end">Minimun (24h)</th>
									<th scope="col" class="text-end">Volume (24h)</th>
								</tr>
							</thead>

							<?php

							$bitsoWallet = new BitsoWallet();
							$bitsoTicker = $bitsoWallet->getFullTicker();

							$favorits 	 = ['btc_mxn','bch_mxn','ltc_mxn','mana_mxn','eth_mxn'];

							foreach ($bitsoTicker as $key => $value) {

								if (in_array($key, $favorits)){

									$change_percent = ($value['change']/ $value['last']) * 100;
									$change_percent = number_format($change_percent, 2);

									$last_buy_price = $bitsoWallet->getLatestCurrencySell($key);
									$change_24 		= $bitsoWallet->getMinimunMaximun($key);
									$calc_entry		= (($value['last'] - $change_24->minimum)/$value['last']) *100;

									$row_down = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-down"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg></td>';

									$row_up = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#00aa00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg></td>';

									echo "<tr>";
									echo 	"<td>". $key ."</td>";
									echo 	"<td class='text-end'>". convertMoney( $last_buy_price ) ."</td>";
									echo 	"<td class='text-end'>". convertMoney( $value['last'] ) ."</td>";
									if ($change_percent < 0){
									echo 	"<td class='text-end text-danger'>". $change_percent .'%'. $row_down;
									} else {
									echo 	"<td class='text-end text-success'>". $change_percent .'%'. $row_up;
									}									
									echo 	"<td class='text-end'>". convertMoney( $value['low'] ) ."</td>";
									if ($calc_entry < 0){
									echo 	"<td class='text-end text-danger'>". number_format( $calc_entry,2 ) ."% </td>";
									} else {
									echo 	"<td class='text-end text-success'>". number_format( $calc_entry,2 ) ."% </td>";
									}
									echo 	"<td class='text-end'>". convertMoney($change_24->minimum) ."</td>";
									echo 	"<td class='text-end'>". convertMoney($change_24->volume) ."</td>";
									echo "</tr>";									

								}
							}

							?>
						</table>
					</div>	<!-- Table-responsive -->

				</div>	<!-- Card -->

			</div>	<!-- Col-12 -->			
					
		</div> 	<!-- Container -->
		
	</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>