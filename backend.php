<?php

require_once ('classes/functions.php'); 

use classes\MySQL;

$option = $_POST["option"];
$mysql  = new MySQL();

switch($option){
	case 'bitso_save':
		$amount = $_POST["amount"];
		$price  = $_POST["price"];
		$book   = $_POST["book"];
		
		$vars = array(
			"amount" =>"'$amount'", 
			"price"	 =>"'$price'", 
			"book"	 =>"'$book'"
		);
		
		$exec = $mysql->mySQLinsert('wallet_balance', $vars);
		
		if ($exec)
			echo "El registro se guardo con exito!";
		else 
			echo "Ocurrio un error";

	break;
	
	case 'delete_db':
		$id = $_POST["id"];
		$date = date('Y-m-d');
		$exec = $mysql->mySQLquery("UPDATE wallet_balance SET status = 0, sell_date = '$date' WHERE id = $id");
		
		if ($exec)
			echo "El registro se elimino con exito!";
		else 
			echo "Ocurrio un error";		
	break;

	case 'modal_books':
		$book = $_POST['book'];
		$data = $mysql->mySQLquery("SELECT * FROM wallet_balance WHERE book = '$book' AND status = 1 ORDER BY date ASC");
		
		echo "<table class='table'>";
		foreach ($data as $key => $value) {
			$date = new DateTime($value->date);
			echo "<tr>";
			echo "	<td>". $date->format('d-m-Y (h:i:s a)') ."</td>";
			echo "	<td class='text-end'>". convertMoney($value->price) ."</td>";
			echo "<tr>";
		}
		echo "</table>";
	break;

	case 'save_favorit':
		$book   = $_POST["book"];
		
		$vars = array(
			"book"	 =>"'$book'"
		);
		
		$exec = $mysql->mySQLinsert('wallet_balance', $vars);
		
		if ($exec)
			echo "El registro se guardo con exito!";
		else 
			echo "Ocurrio un error";

	break;
}

