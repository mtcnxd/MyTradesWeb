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
			if ($available[9]['amount'] > 100){
				$amountBuy = number_format( (100 / $ticker[$book]), 7 );

				$response = $bitsoWallet->placeOrder($book,'buy',$ticker[$book], $amountBuy);
				$json_object = json_decode($response);

				$query = "Insert into wallet_test (price) VALUES ('".$ticker[$book]."')";
				$query = "Insert into wallet_test (amount) VALUES (".$amountBuy.")";
				$query = "Insert into wallet_test (response) VALUES (".$json_object.")";

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
