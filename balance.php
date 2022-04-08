<?php
session_start();
require_once ('classes/functions.php'); 

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
	
	// Create signature
	$JSONPayload = "";
	$message = $nonce . $HTTPMethod ."/v3/balance/". $JSONPayload;
	$signature = hash_hmac('sha256', $message, $bitsoSecret);
	
	// Build the auth header
	$format = 'Bitso %s:%s:%s';
	$authHeader =  sprintf($format, $bitsoKey, $nonce, $signature);	 
	
	// Send request
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.bitso.com/v3/balance/');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, "true");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: '. $authHeader,'Content-Type: application/json'));
	
	$balance_result = curl_exec($ch);
	$json_string    = json_decode($balance_result);
	$balance_json   = $json_string->payload;
	$balance_array = $balance_json->balances;
	
	curl_setopt($ch, CURLOPT_URL, 'https://api.bitso.com/v3/ticker/');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, "true");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $authHeader,'Content-Type: application/json'));
	
	$ticker_result = curl_exec($ch);
	$ticker_json = json_decode($ticker_result);
	$ticker_array = $ticker_json->payload;
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
			<div class="row mb-4">
				<div class="col">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-header">
							<h6 class="card-header-title">Balances</h6>
							<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
						</div>
						
						<ul class="list-group list-group-flush">
						<?php
						$value_total   = 0;
						$buy_power     = 0;
						$buy_power_mxn = 0;
						$chart_data    = array();							
						$ticker_currencys = array();
						$balance_mxn   = array();
						
						foreach ($ticker_array as $ticker){
							if( strpos($ticker->book, "_mxn") or strpos($ticker->book, "_usd") ){
								$ticker_currencys[$ticker->book] = $ticker->last;
							}
						}
						
						foreach ($balance_array as $balance){
							if ($balance->total > 0.0002){
								echo "<li class='list-group-item list-group-item-action'>";
								if ($balance->currency == 'mxn'){
									$value_total += $balance->total;
									$balance_mxn[$balance->currency] = $balance->total;
									$buy_power = $balance->total;
									$buy_power_mxn = $balance->total;
									
									echo "<div class='ms-2'>
		  								  	<div class='fw-bold text-uppercase'>". $balance->currency ."</div>
	      									<div class='row'>
	      										<div class='col-md-6'>". $balance->total ."</div>
	      										<div class='col-md-6 text-end'>". convertMoney($balance->total) ."</div>
	      									</div> 
										  </div>";										
								} else {
									$book = $balance->currency ."_mxn";
									if (array_key_exists($book, $ticker_currencys)){
										$total_usd = $balance->total * $ticker_currencys['usd_mxn'];
										$total_mxn = $ticker_currencys[$book] * $balance->total;
										$balance_mxn[$balance->currency] = $total_mxn;
										$chart_data[$balance->currency]  = $total_mxn;
										$value_total += $total_mxn;
										
										if ($balance->currency == 'usd')
											$buy_power += $total_mxn;
											$buy_power_mxn += $total_mxn;
										
										echo "<div class='ms-2'>
		  								  	<div class='fw-bold text-uppercase'>". $balance->currency ."</div>
	      									<div class='row'>
	      										<div class='col-md-6'>". $balance->total ."</div>
	      										<div class='col-md-6 text-end'>". convertMoney($total_mxn) ."</div>
	      									</div> 
										  </div>";		
										
									} else {
										$book = $balance->currency ."_usd";
										$total_usd = $ticker_currencys[$book] * $balance->total;
										$total_mxn = $total_usd * $ticker_currencys['usd_mxn'];
										$balance_mxn[$balance->currency] = $total_mxn;
										$chart_data[$balance->currency]  = $total_mxn;											
										$value_total += $total_mxn;
										
										echo "<div class='ms-2'>
		  								  	<div class='fw-bold text-uppercase'>". $balance->currency ."</div>
	      									<div class='row'>
	      										<div class='col-md-6'>". $balance->total ."</div>
	      										<div class='col-md-6 text-end'>". convertMoney($total_mxn) ."</div>
	      									</div> 
										  </div>";		
									}
								}	
								echo "</li>";
							}
						}
						
						$buy_power = ($buy_power/$value_total) * 100;
						?>
						</ul>
					</div>	<!-- Card -->	
				</div>	<!-- col -->		
			
				<div class="col">
					<div class="row">
						<div class="col-md-12">
							<div class="card border border-custom shadow-sm rounded mb-4">
								<div class="card-header">
									<h6 class="card-header-title">Graphic</h6>
									<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pie-chart"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
								</div>
								<div class="card-body">
									<canvas class="p-3" id="myChart" width="250" height="100"></canvas>							
								</div>
							</div>
						</div>	<!-- col -->
					</div> <!-- row -->


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
												Performance last 36 hours
											</h6>
											<h5 class="card-subtitle mb-2 fs-6">
												<?=getPerformanceIntime(36) .'%';?>
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


					<div class="row">
						<div class="col-md-6">
							<div class="card border-custom shadow-sm mb-4">
								<div class="card-body">
									<div class="align-items-center row">
										<div class="col">
											<h6 class="card-title text-muted text-uppercase fs-7">
												Buying power <?=" (".number_format($buy_power,2) ."%)";?>
											</h6>
											<h5 class="card-subtitle mb-2 fs-6">
												<?=convertMoney($buy_power_mxn);?>
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
						<div class="col">
							<div class="card border border-custom shadow-sm rounded mb-4">
								<div class="card-header">
									<h6 class="card-header-title">History performance</h6>
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
								</div>

								<div class="card-body">
									<?php
									$result = getHistory();

									echo "<table class='table table-hover'>";
									foreach($result as $key => $value){
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
const myChart = new Chart(ctx, {
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
</script>
 