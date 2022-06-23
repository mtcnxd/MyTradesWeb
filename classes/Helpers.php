<?php

namespace classes;

require_once('mySQL.php');

use classes\MySQL;

class Helpers
{
    static function sendWebHook($event, $data) 
    {
        $url = 'https://maker.ifttt.com/trigger/'.$event.'/with/key/b02sH9pYZV0xykH4H8K2wT';        
        $payload = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_POST, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;

    }

    static function extractCurrency($book)
    {
        return substr($book, 0, strpos($book, '_'));
    }

    static function convertMoney($number)
    {
        $money = number_format($number, 2);
        return "$". $money;
    }

    static function time_elapsed($hours)
    {
        if ($hours/24 >= 1){
            $string = number_format($hours/24) ." d";
        } else {
            $string = $hours ." h";
        }
        return $string;
    }

    static function time_elapsed_str($time)
    {
        $days   = ($time/24);
        $hours  = ($time%24);

        if ($days > 30){
            $month  = $days/30;
        }

        $string = number_format($days,0) ." days ". $hours ." hours ago"; 

        return $string;
    }

    static function getCurrencysFavorites()
    {
        $mysql = new MySQL();
        $query = 'select * from wallet_favorites';
        return $mysql->mySQLquery($query);
    }

    static function getListTrades()
    {
        $mysql = new MySQL();
        $query = "select book, COUNT(*) trades FROM `wallet_balance` WHERE status = 1 GROUP BY book ORDER BY trades";
        return $mysql->mySQLquery($query);
    }

    static function getUserData($username)
    {
        $mysql = new MySQL();
        $query = "select * from wallet_users a JOIN wallet_config b ON a.id = b.id_user 
                  WHERE username = '$username'";

        return $mysql->mySQLquery($query);
    }

    static function getAverageTrades()
    {
        $mysql = new MySQL();
        $query = "select AVG(trades) average from (
            SELECT COUNT(*) trades, date_format(date,'%u-%Y') week FROM wallet_balance GROUP BY week) tbl";
        $result = $mysql->mySQLquery($query);

        return $result[0];
    }

    static function openLogfile()
    {
        $fcontent = "";
        $filename = 'error_log';

        if (file_exists($filename)){
            $file = fopen($filename, 'r');
            $fcontent = fread($file, filesize($filename));;
        }
        
        return $fcontent;
    }

    static function getLastPriceChange()
    {
        $mysql = new MySQL();
        $query = "select * from wallet_performance ORDER BY id DESC LIMIT 2";
        $data  = $mysql->mySQLquery($query);
        
        $values = array();

        if (!empty($data)){
            foreach ($data as $key => $value) {
                $values[$key] = $value->amount;
            }

            $current_price  = $values[0];
            $last_price     = $values[1];
            
            $change = (($current_price - $last_price) / $current_price) * 100;
            $change = number_format($change, 2);

            return $change;     
        }

    }

    static function getOldestBuy()
    {
        $mysql = new MySQL();
        $query = "select *, TIMESTAMPDIFF(HOUR, date, now()) as elapsed FROM wallet_balance a 
                  JOIN wallet_currencys b ON a.book = b.book ORDER by date ASC LIMIT 1";
        
        return $mysql->mySQLquery($query);
    }


}