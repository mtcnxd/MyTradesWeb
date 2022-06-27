<?php

require_once ("test.fortech.mx/classes/BitsoWallet.php");
require_once ("test.fortech.mx/classes/mySQL.php");

use classes\BitsoWallet;
use classes\MySQL;

$prices = $bitsoWallet->getLastBoughtPrices('ltc_mxn');
$ticker = $bitsoWallet->getTicker();

$percent = (($ticker['ltc_mxn'] - $prices->price)/$ticker['ltc_mxn']) *100 ;

if ($percent > 6){
	echo "Vender ". $prices->amount ." LTC";

} else if ($percent < -5){
	$mysql = new MySQL();
	$query = "Insert Into wallet_test(price) VALUES ('".$ticker['ltc_nxd']."')";
	$mysql->mySQLquery($query);
}