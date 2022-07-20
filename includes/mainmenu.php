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
            	<img src="<?=$icon?>" alt="mdo" width="32" height="32" class="rounded-circle">
          	</a>
          	<ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
			  	<li><a class="dropdown-item" href="#"><?=$user?></a></li>
	            <li><a class="dropdown-item" href="#">Profile</a></li>
	            <li><a class="dropdown-item" href="#">Sign out</a></li>
				</ul>
        </div>
  	</div>
</div>