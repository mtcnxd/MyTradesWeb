<?php
session_start();
require_once ('classes/functions.php');

use classes\MySQL;

if (!$_SESSION) {
	header('Location:index.php');
}

$result = getUserData($_SESSION['name']);

foreach ($result as $key => $value) {
	$name    = $value->name;
	$email   = $value->email;
	$notify1 = $value->notify_01;
	$notify2 = $value->notify_02;
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
			<div class="row mb-4">
				<div class="col">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-header">
							<h6 class="card-header-title">Profile</h6>
							<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
						</div>

						<div class="p-4">
							<form action="" method="post" name="profile">
								<div class="mb-3">
									<label for="name" class="form-label">Full name</label>
									<input type="text" name="name" id="name" value="<?=$name;?>" class="form-control">
								</div>								
								<div class="mb-3">
									<label for="username" class="form-label">Username</label>
									<input type="text" name="username" id="username" value="<?=$_SESSION['name']?>" class="form-control">
								</div>
								<div class="mb-3">
									<label for="email" class="form-label">Email</label>
									<input type="text" name="email" id="email" value="<?=$_SESSION['email']?>" class="form-control" >
								</div>
								<div class="mb-3">
									<label for="password" class="form-label">New password</label>
									<input type="text" name="password" id="password" class="form-control">
								</div>
								<div class="mb-3">
									<input type="button" id="confirm" value="Save" class="btn btn-primary" >
								</div>								
							</form>
						</div>
					</div>	
				</div>	<!-- Col -->

				<div class="col">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-header">
							<h6 class="card-header-title">Profile statistics</h6>
							<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
						</div>
						<div class="card-body">
							<ul class="list-group">
								<?php
								$listTrades = getListTrades();

								foreach($listTrades as $key => $list){
									echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
									echo 	"<a href='currentbook.php?book=$list->book'>". $list->book ."</a>";
									echo 	"<span class='badge bg-primary rounded-pill'>".$list->trades."</span>";
									echo "</li>";
								}

								?>
							</ul>							
						</div>
					</div>
				</div>	<!-- Col -->
			</div>	<!-- row -->

			<div class="row mb-4">
				<div class="col-md-6">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-header">
							<h6 class="card-header-title">Configuration</h6>
							<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
						</div>

						<div class="p-4">
							<form action="" method="post" name="configuration">
								<div class="mb-3">
									<label for="username" class="form-label">API</label>
									<input type="text" name="username" id="username" value="<?=$_SESSION['key']?>" class="form-control">
								</div>
								<div class="mb-3">
									<label for="email" class="form-label">Secret</label>
									<input type="text" name="email" id="email" value="<?=$_SESSION['secret']?>" class="form-control" >
								</div>
								<div class="mb-3 form-check">
									<input type="checkbox" class="form-check-input" <?=$notify1;?> id="sendnotify1">
								    <label class="form-check-label" for="sendnotify">Notify when balance goes down 3% in last hour</label>
								</div>
								<div class="mb-3 form-check">
									<input type="checkbox" class="form-check-input" <?=$notify2;?> id="sendnotify2">
								    <label class="form-check-label" for="sendnotify">Notify when balance goes up 3% in last hour</label>
								</div>								
								<div class="mb-3">
									<input type="button" id="confirm" value="Save" class="btn btn-primary" >
								</div>
							</form>
						</div>

					</div>	<!-- Card -->
				</div>	<!-- Col -->


				<div class="col-md-6">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-header">
							<h6 class="card-header-title">Favorit Currrencys</h6>
							<svg class="card-header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
						</div>

						<div class="p-4">
							<form action="" method="post" name="configuration">

								<div class="mb-3 form-check">
									<input type="checkbox" class="form-check-input" <?=$notify2;?> id="sendnotify2">
								    <label class="form-check-label" for="sendnotify"><?=$notify2;?></label>
								</div>								
													
								<div class="mb-3">
									<input type="button" id="confirm" value="Save" class="btn btn-primary" >
								</div>
							</form>
						</div>

					</div>	<!-- Card -->
				</div>	<!-- Col -->


			</div> 	<!-- Row -->
		</div>	<!-- Container -->

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
	
	function open_book_details(book, button)
	{
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

	function bitso_save(amount, price, book)
	{
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