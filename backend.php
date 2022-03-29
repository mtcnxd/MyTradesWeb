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
		
		$vars = array(
			"amount"=>"'$amount'", 
			"price"=>"'$price'", 
			"book"=>"'$book'"
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
	
	case 'sell_currency':
		// Send request
		$ch = curl_init();
		
		$JSONPayload = json_encode(['book'  => 'btc_mxn',
			                        'side'  => 'sell',
			                        'major' => '.0001',
			                        'price' => '1000',
			                        'type'  => 'limit']);		
		
		curl_setopt($ch, CURLOPT_URL, 'https://api.bitso.com/v3/orders/');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, "true");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $JSONPayload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' .  $authHeader,'Content-Type: application/json'));
		
		$result = curl_exec($ch);
		$json_object = json_decode($result);
		$array = $json_object->{'payload'};
		
	break;	
}

