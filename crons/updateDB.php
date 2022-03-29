<?php

require_once ("test.fortech.mx/classes/BitsoWallet.php");
require_once ("test.fortech.mx/classes/mySQL.php");

use classes\BitsoWallet;
use classes\MySQL;

$bitsoWallet = new BitsoWallet();

$balances_array = $bitsoWallet->getAccountBalance();
$latestBalance  = $bitsoWallet->getLatestBalance();
$currentBalance = array_sum(array_values($balances_array));

$walletChange = (($currentBalance - $latestBalance) / $currentBalance) * 100;
$walletChange = number_format($walletChange, 2);

$mysql   = new MySQL();
$query   = "INSERT INTO wallet_performance(amount, difference) VALUES ('$currentBalance', '$walletChange')";
$mysql->mySQLquery($query);