<?php

require_once('mySQL.php');

use classes\MySQL;

/*************************************************************/
/****************** DEFINICION DE VARIABLES ******************/
/*************************************************************/

$bitsoKey 	 = "TMJEPCYmIv";
$bitsoSecret = "d181cda5b0f939ee1b42e7b45ebd93e5";
$nonce 		 = (integer)round(microtime(true) * 10000 * 100);
$HTTPMethod  = "GET";

/*************************************************************/
/****************** BLOQUE DE FUNCIONES **********************/
/*************************************************************/

function create_curl($curl_url, $authHeader)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $curl_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, "true");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: '. $authHeader,'Content-Type: application/json'));
	
	$balance_result = curl_exec($ch);
	$json_string    = json_decode($balance_result);
	$orders_json    = $json_string->payload;		
	return $orders_json;
}

function showHtmlRow($gain_lost, $bought, $current)
{
	if ($current!= 0){
		$percent = (($current - $bought)/$current)*100;
		$percent = number_format($percent,2);
		if ($gain_lost > 0){
			echo 	"<td class='text-end bg-success text-white' data-bs-toggle='tooltip' data-bs-placement='right' title='$percent%'> ~".
					 	convertMoney($gain_lost) 
					 ."</td>";
		} else if ($gain_lost < 0){ 
			echo 	"<td class='text-end bg-danger text-white' data-bs-toggle='tooltip' data-bs-placement='right' title='$percent%'> ~". 
						convertMoney($gain_lost) 
					."</td>";
		} else {
			echo 	"<td class='text-end'>". convertMoney($gain_lost) ."</td>";
		}
	} else {
		echo 	"<td class='text-end'> 0.00 </td>";
	}
}

function extractCurrency($book)
{
	return substr($book, 0, strpos($book, '_'));
}

function convertMoney($number)
{
	$money = number_format($number, 2);
	return "$". $money;
}

function convertFloat($string)
{
	return strval($string);
}

function icon_percent($change)
{
	if ($change < 0) {
		echo '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-down"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>';
	} else {
		echo '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#00aa00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>';
	}
}

function time_elapsed($hours)
{
	if ($hours/24 >= 1){
		$string = number_format($hours/24) ." d";
	} else {
		$string = $hours ." h";
	}
	return $string;
}

function time_elapsed_str($time)
{
	$days   = ($time/24);
	$hours  = ($time%24);
	$string = number_format($days,0) ." days ". $hours ." hours ago"; 

	return $string;
}

function select_oldest_buy()
{
	$mysql = new MySQL();
	$query = "SELECT *, TIMESTAMPDIFF(HOUR, date, now()) as elapsed FROM wallet_balance a 
			  JOIN wallet_currencys b ON a.book = b.book ORDER by date ASC LIMIT 1";
	$data  = $mysql->mySQLquery($query);
	return $data; 
}

function select_max()
{
	$mysql = new MySQL();	
	$query = "SELECT book, SUM(price * amount) as value FROM wallet_balance GROUP BY book ORDER by value DESC LIMIT 1";
	$data  = $mysql->mySQLquery($query);
	return $data;
}

function select_min()
{
	$mysql = new MySQL();
	$query = "SELECT book, SUM(price * amount) as value FROM wallet_balance GROUP BY book ORDER by value ASC LIMIT 1";
	$data  = $mysql->mySQLquery($query);
	return $data;
}

function getCurrentChange($current_price)
{
	$mysql = new MySQL();
	$query = "SELECT * FROM wallet_performance ORDER BY id DESC LIMIT 1";
	$data  = $mysql->mySQLquery($query);

	if(!empty($data)){
		$last_price = $data[0]->amount;
	
		$change = (($current_price - $last_price) / $current_price) * 100;
		$change = number_format($change, 2);

		return $change;
	}
	
}

function getChange()
{
	$mysql = new MySQL();
	$query = "SELECT * FROM wallet_performance ORDER BY id DESC LIMIT 2";
	$data  = $mysql->mySQLquery($query);
	
	$values = array();

	if (!empty($data)){
		foreach ($data as $key => $value) {
			$values[$key] = $value->amount;
		}

		$current_price 	= $values[0];
		$last_price 	= $values[1];
		
		$change = (($current_price - $last_price) / $current_price) * 100;
		$change = number_format($change, 2);

		return $change;		
	}

}

function getHistory($hours = 12)
{
	$mysql = new MySQL();
	$query = "SELECT * FROM wallet_performance ORDER BY id DESC LIMIT $hours";
	$result = $mysql->mySQLquery($query);

	return $result;
}

function getPerformanceIntime($time_elapsed = 12)
{
	$mysql = new MySQL();
	$query = "SELECT SUM(difference) as performance FROM (
				SELECT difference FROM wallet_performance ORDER BY date DESC LIMIT $time_elapsed) as AVG";
	$result = $mysql->mySQLquery($query);

	return number_format($result[0]->performance, 2);
}

function getAverageTrades()
{
	$mysql = new MySQL();
	$query = "SELECT AVG(trades) average FROM (
		SELECT COUNT(*) trades, date_format(date,'%u-%Y') week FROM wallet_balance GROUP BY week) tbl";
	$result = $mysql->mySQLquery($query);

	return $result[0];
}

function getListTrades()
{
	$mysql = new MySQL();
	$query = "SELECT book, COUNT(*) trades FROM `wallet_balance` WHERE status = 1 GROUP BY book ORDER BY trades";
	$result = $mysql->mySQLquery($query);

	return $result;
}

function getUserData($username)
{
	$mysql = new MySQL();
	$query = "SELECT * FROM wallet_users a JOIN wallet_config b ON a.id = b.id_user 
			  WHERE username = '$username'";

	$result = $mysql->mySQLquery($query);

	return $result;
}