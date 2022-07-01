<?php

require_once ("test.fortech.mx/classes/BitsoWallet.php");
require_once ("test.fortech.mx/classes/mySQL.php");

use classes\BitsoWallet;
use classes\MySQL;

$bitsoWallet = new BitsoWallet();
$ticker = $bitsoWallet->getTicker();

$books  = ['bat_mxn','mana_mxn'];


foreach ($books as $book) {
	$prices = $bitsoWallet->getLastBoughtPrices($book);	
	$change = (($ticker[$book] - $prices->price)/$ticker[$book]) *100 ;

	if ($change < -5){
		$available = $bitsoWallet->getWalletBalances();

		if($available[9]['currency'] == 'mxn'){
			if ($available[9]['amount'] > 30){
				$amountBuy = number_format( (50 / $ticker[$book]), 8 );

				$response = $bitsoWallet->placeOrder($book,'buy',$ticker[$book], $amountBuy);
				$json_object = json_decode($response);

				$mysql = new MySQL();
				if ( $json_object->success ){
					$message = $json_object->payload->oid;
					$query = "Insert Into wallet_test(price, amount, book, response) 
								Values ('".$ticker[$book]."','$amountBuy', '$book', '$message')";
				} else {
					$message = $json_object->error->message;
					$query = "Insert Into wallet_test(price, amount, book, response) 
								Values ('".$ticker[$book]."','$amountBuy', '$book', '$message')";
					
				}
				$mysql->mySQLquery($query);

			} else {
				echo "You don't have mxn available to buy crypto.";
			}
		}
	}
}


$prices = $bitsoWallet->getLastBoughtPrices('ltc_mxn');
$percent = (($ticker['ltc_mxn'] - $prices->price)/$ticker['ltc_mxn']) *100 ;

if ($percent < -5){
	$mysql = new MySQL();
	$query = "Insert Into wallet_test(price, book) VALUES ('".$ticker['ltc_mxn']."', 'ltc_mxn')";
	$mysql->mySQLquery($query);
}



$prices = $bitsoWallet->getLastBoughtPrices('bat_mxn');
$percent = (($ticker['bat_mxn'] - $prices->price)/$ticker['bat_mxn']) *100 ;

if ($percent < -5){
	$mysql = new MySQL();
	$query = "Insert Into wallet_test(price, book) VALUES ('".$ticker['bat_mxn']."', 'bat_mxn')";
	$mysql->mySQLquery($query);
}