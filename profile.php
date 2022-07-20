<?php
session_start();
require_once ('classes/Helpers.php');
require_once ('classes/BitsoWallet.php');

use classes\MySQL;
use classes\Helpers;
use classes\BitsoWallet;

if (!$_SESSION) {
	header('Location:index.php');
}

$userData = Helpers::getUserData($_SESSION['name']);

foreach ($userData as $data) {
	$name    = $data->name;
	$email   = $data->email;
	$notify1 = $data->notify_01;
	$notify2 = $data->notify_02;
}

$userId = $_SESSION['userid'];
$bitsoWallet = new BitsoWallet($userId);
$userData = $bitsoWallet->getUserInformation();

$user = $userData->first_name ." ". $userData->last_name;
$icon = $userData->gravatar_img;
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
			<?php include('includes/mainmenu.php') ?>
		</header>			
		
		<div class="container">
			<div class="row mb-4">
				<div class="col">
					<div class="card rounded border border-custom shadow-sm">
						<div class="card-header">
							<h6 class="card-header-title">Profile</h6>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M12 2.5a5.25 5.25 0 00-2.519 9.857 9.005 9.005 0 00-6.477 8.37.75.75 0 00.727.773H20.27a.75.75 0 00.727-.772 9.005 9.005 0 00-6.477-8.37A5.25 5.25 0 0012 2.5z"></path></svg>
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
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M13 7.5a1 1 0 11-2 0 1 1 0 012 0zm-3 3.75a.75.75 0 01.75-.75h1.5a.75.75 0 01.75.75v4.25h.75a.75.75 0 010 1.5h-3a.75.75 0 010-1.5h.75V12h-.75a.75.75 0 01-.75-.75z"></path><path fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11 11-4.925 11-11S18.075 1 12 1zM2.5 12a9.5 9.5 0 1119 0 9.5 9.5 0 01-19 0z"></path></svg>
						</div>
						<div class="card-body">
							<ul class="list-group list-group-flush">
								<?php
								$listTrades = Helpers::getListTrades();

								foreach($listTrades as $list){
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
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M0 13C0 6.373 5.373 1 12 1s12 5.373 12 12v8.657a.75.75 0 01-1.5 0V13c0-5.799-4.701-10.5-10.5-10.5S1.5 7.201 1.5 13v8.657a.75.75 0 01-1.5 0V13z"></path><path d="M8 19.75a.75.75 0 01.75-.75h6.5a.75.75 0 010 1.5h-6.5a.75.75 0 01-.75-.75z"></path><path fill-rule="evenodd" d="M5.25 9.5a1.75 1.75 0 00-1.75 1.75v3.5c0 .966.784 1.75 1.75 1.75h13.5a1.75 1.75 0 001.75-1.75v-3.5a1.75 1.75 0 00-1.75-1.75H5.25zm.22 1.47a.75.75 0 011.06 0L9 13.44l2.47-2.47a.75.75 0 011.06 0L15 13.44l2.47-2.47a.75.75 0 111.06 1.06l-3 3a.75.75 0 01-1.06 0L12 12.56l-2.47 2.47a.75.75 0 01-1.06 0l-3-3a.75.75 0 010-1.06z"></path></svg>
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
								    <label class="form-check-label" for="sendnotify">Notify when balance goes down 1.2% in last hour</label>
								</div>
								<div class="mb-3 form-check">
									<input type="checkbox" class="form-check-input" <?=$notify2;?> id="sendnotify2">
								    <label class="form-check-label" for="sendnotify">Notify when balance goes up 1.2% in last hour</label>
								</div>
								<div class="mb-3 form-check">
									<input type="checkbox" class="form-check-input" <?=$notify2;?> id="sendnotify2">
								    <label class="form-check-label" for="sendnotify">Auto buy status</label>
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
							<h6 class="card-header-title">Favorit currrencys</h6>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill-rule="evenodd" d="M5 3.75C5 2.784 5.784 2 6.75 2h10.5c.966 0 1.75.784 1.75 1.75v17.5a.75.75 0 01-1.218.586L12 17.21l-5.781 4.625A.75.75 0 015 21.25V3.75zm1.75-.25a.25.25 0 00-.25.25v15.94l5.031-4.026a.75.75 0 01.938 0L17.5 19.69V3.75a.25.25 0 00-.25-.25H6.75z"></path></svg>
						</div>

						<div class="p-4">
							<form action="" method="post" name="configuration">
								<ul class="list-group list-group-flush mb-3">
								<?php
								$favorites = Helpers::getCurrencysFavorites();
								foreach ($favorites as $data) {
									echo '<li class="list-group-item">';
									echo '<div class="form-check form-switch">
											  <input class="form-check-input" type="checkbox" role="switch" id="'.$data->book.'" onclick="save_favorit(this.id)" checked>
											  <label class="form-check-label" for="'.$data->book.'">'.$data->book.'</label>
										  </div>';
									echo '</li>';
								}

								?>							
								</ul>
													
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
		option:'bitso_save',
		amount:amount,
		price:price,
		book:book
	}, function(response){
		$("#message").text("Message: " + response);
		toast.show();			
	});
}

function save_favorit(book)
{
	$.post("backend.php",{
		option:'save_favorit',
		book:book

	}, function(response){
		$("#message").text("Message: " + response);
		toast.show();			
	});
}


</script>