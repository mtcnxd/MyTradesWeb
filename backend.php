<?php

require_once ('classes/functions.php'); 

use classes\MySQL;

$option = $_POST["option"];
$mysql  = new MySQL();

switch($option){
	case 'insert_db':
		$amount = $_POST["amount"];
		$price  = $_POST["price"];
		$book   = $_POST["book"];
		$userId = $_POST["userid"];
		
		$vars = array(
			"amount" =>"'$amount'", 
			"price"	 =>"'$price'", 
			"book"	 =>"'$book'",
			"user"   =>"'$userId'"
		);
		
		$exec = $mysql->mySQLinsert('wallet_balance', $vars);
		
		if ($exec)
			echo "El registro se guardo con exito!";
		else 
			echo "Ocurrio un error: ";

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

	case 'save_new_favorit':
		$book   = $_POST["book"];
		$userid = $_POST["user"];
		
		$vars = array(
			"book"   => "'$book'",
			"user" => "'$userid'",
		);
		
		if ($mysql->mySQLinsert("wallet_favorites", $vars)){
			$message = [
				'success' => true,
				'message' => 'El registro se guardo con exito'
			];
		} else {
			$message = [
				'success' => false,
				'message' => "Ocurrio un error: ". $mysql->getQueryResult()
			];
		}
		echo json_encode($message);

	break;	

	case 'save_favorit':
		$book   = $_POST["book"];
		$status = $_POST["status"];
		
		$vars = array(
			"status" => $status == 'true' ? 'checked' : null
		);
		
		if ($mysql->mySQLupdate("wallet_favorites", $vars, "book = '$book'"))
			echo "El registro se guardo con exito! ". $mysql->getQueryResult();
		else 
			echo "Ocurrio un error: ". $mysql->getQueryResult();

	break;

	case 'save_config':
		$user 		 = $_POST["user"];
		$bitsoKey    = $_POST["bitsoKey"];
		$bitsoSecret = $_POST["bitsoSecret"];
		
		$vars = array(
			"bitso_key"	  =>"$bitsoKey", 
			"bitso_secret" =>"$bitsoSecret"
		);
		
		if ($mysql->mySQLupdate('wallet_config', $vars, 'user = '.$user))
			echo "La configuracion se guardo con exito!";
		else 
			echo "Ocurrio un error: ". var_dump($vars);
	break;

	case 'cancell_order':

	break;
}

