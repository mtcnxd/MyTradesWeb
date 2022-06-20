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
	
	<?php

	$bitsoWallet = new BitsoWallet();
	$orders = $bitsoWallet->getOpenOrders();
	$trades = $bitsoWallet->getUserTrades();

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
							<h6 class="card-header-title">Active orders</h6>
							<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
						</div>
						<table class="table table-borderless table-hover fs-6">
							<thead>
								<tr class="table-custom text-uppercase fs-7">
									<th scope="col">#</td>
									<th scope="col">Amount</td>
									<th scope="col" class="text-end">Value</td>									
									<th scope="col" class="text-center">Book</td>
									<th scope="col" class="text-end">Price</td>					
									<th scope="col" class="text-end">Side</td>
									<th scope="col" class="text-end">&nbsp;</td>
								</tr>
							</thead>

							<?php		
							foreach($orders as $number => $row){
								echo "<tr>";								
								echo "<td>". ($number + 1) ."</td>";
								echo "<td>". $row->original_amount ."</td>";							
								echo "<td class='text-end'>". convertMoney($row->original_value) ."</td>"; 
								echo "<td class='text-center'>". $row->book ."</td>";
								echo "<td class='text-end'>". convertMoney($row->price) ."</td>";
								echo "<td class='text-end'>". $row->side ."</td>";
								echo 	"<td class='text-center align-middle'>
											<a id='". $row->oid ."' onclick='erase(this.id)'>
											<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' width='16' height='16'>
												<path fill-rule='evenodd' d='M2.343 13.657A8 8 0 1113.657 2.343 8 8 0 012.343 13.657zM6.03 4.97a.75.75 0 00-1.06 1.06L6.94 8 4.97 9.97a.75.75 0 101.06 1.06L8 9.06l1.97 1.97a.75.75 0 101.06-1.06L9.06 8l1.97-1.97a.75.75 0 10-1.06-1.06L8 6.94 6.03 4.97z'>
												</path>
											</svg></a>
										</td>";
								echo "</tr>";									
							}						
							?>					
						</table>					
					</div>	<!-- Card -->
					
				</div> <!-- Col -->
				
				<div class="col-md-3">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-body">
							<div class="align-items-center row">
								<div class="col">
							    	<h6 class="card-title text-muted text-uppercase fs-7">
							    		AVG week orders
							    	</h6>
							    	<h5 class="card-subtitle mb-2 fs-6">
							    		<?=getAverageTrades()->average;?>
							    	</h5>
						    	</div>
						    	<div class="col-auto">
									<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
								</div>							    	
					    	</div>
					  	</div>
					</div>
				</div>

			</div>

			<div class="col-md-12 mb-4">
				
			</div>	<!-- Col-12 -->
			
			<div class="col-md-12 mb-4">
				<div class="card rounded border border-custom shadow-sm">
					<div class="card-header">
						<h6 class="card-header-title">History</h6>
						<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
					</div>

					<div class="table-responsive">
						<table class="table table-borderless table-hover fs-6">
							<thead>
								<tr class="table-custom text-uppercase fs-7">
									<th scope="col">Date</td>
									<th scope="col">Time elapsed</td>
									<th scope="col">Side</td>
									<th scope="col">Book</td>
									<th scope="col" class="text-end">Amount</td>
									<th scope="col" class="text-end">Price</td>
									<th scope="col" class="text-center">Action</td>									
								</tr>
							</thead>
							
							<?php					
							foreach($trades as $cell => $row){
								$today  = new DateTime(date('d-m-Y'));
								$date   = new DateTime($row->created_at);
								$amount = ($row->major - $row->fees_amount);
								$price  = $row->price;
								$book   = $row->book;
								$diff   = $date->diff($today);
								$date->modify('-6 hours');
								
								echo "<tr id='$cell'>";							
								echo "<td>". $date->format('d-m-Y (h:i a)') ."</td>";
								echo "<td>". $diff->days ." days </td>";
								echo "<td>". $row->side ."</td>";
								echo "<td><a href='#' onclick=\"open_book_details('".$row->book."', this)\">". $row->book ."</a></td>";
								echo "<td id='amount' class='text-end'>". $amount.' '.$row->major_currency ."</td>";			
								echo "<td id='price' class='text-end'>". convertMoney($price).' '.$row->minor_currency ."</td>";
								if($row->side == 'buy'){
									echo '<td class="text-center">
											<a href="#" onclick="bitso_save('.$amount.','.$price.',\''.$book.'\')">
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#777777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-save"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
											</a>
										  </td>';								
								}
								echo "</tr>";
							}						
							?>								
							
						</table>
					</div>	<!-- Table -->
				</div>	<!-- Card -->
			</div>	<!-- Col-12 -->
		</div> 	<!-- Container -->

		<!-- Modal -->
		<div class="modal fade" id="modal_books" tabindex="-1" aria-labelledby="modal_books" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title text-uppercase" id="modal_title">Last boughts</h6>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
				<div id="modalBooksContent"></div>	
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
				</div>
				</div>
			</div>
		</div>
		
		<!-- Toast -->
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
	
	function open_book_details(book, button){
		var modalBooks = new bootstrap.Modal(document.getElementById('modal_books'));
		var modalTitle = $("#modal_title");

		$("#modalBooksContent").load('backend.php', {
			option:'modal_books',
			book:book
		}, function (){
			modalTitle.text ('Book (' + book.replace("_", " / ") + ')') ;
			modalBooks.show();
		});
	}

	function bitso_save(amount, price, book){
		var toastLive = document.getElementById('liveToast');		
		var toast = new bootstrap.Toast(toastLive);		
		
		$.post("backend.php",{
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