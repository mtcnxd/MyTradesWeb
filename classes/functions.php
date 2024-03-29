<?php

require_once('mySQL.php');

use classes\MySQL;

function showHtmlRow($gain_lost, $bought, $current)
{
	if ($current!= 0){
		$percent = (($current - $bought)/$current)*100;
		$percent = number_format($percent,2);
		if ($gain_lost > 0){
			echo 	"<td class='text-end text-white' data-bs-toggle='tooltip' data-bs-placement='right' title='$percent%'>
						<span class='badge bg-success'> ~ ".convertMoney($gain_lost) ."</span>
					 </td>";
		} else if ($gain_lost < 0){ 
			echo 	"<td class='text-end text-white' data-bs-toggle='tooltip' data-bs-placement='right' title='$percent%'>
						<span class='badge bg-danger'> ~ ".convertMoney($gain_lost) ."</span>
					</td>";
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
