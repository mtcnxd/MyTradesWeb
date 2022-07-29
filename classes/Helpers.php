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

    static function isApiConfigured($userId)
    {
        $mysql = new MySQL();
        $query = 'select * from wallet_config WHERE user = '.$userId;

        if ($result = $mysql->mySQLquery($query)[0]){
            if ($result->bitso_key != "" and $result->bitso_secret != "")
                return true;

        } else {
            return false;
        }
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

    static function getCurrencysFavoritesList($userId)
    {
        $mysql = new MySQL();
        $query = "select * from wallet_favorites where user = ".$userId;
        return $mysql->mySQLquery($query);
    }    

    static function getCurrencysFavorites($userId)
    {
        $mysql = new MySQL();
        $query = "select * from wallet_favorites where status = 'checked' and user = ".$userId;
        $favoritesChecked = $mysql->mySQLquery($query);

        foreach ($favoritesChecked as $key => $value) {
            $favoritesList[] = $value->book;
        }

        return $favoritesList;
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

    static function getUserConfig($userId)
    {
        $mysql = new MySQL();
        $query = "select * FROM wallet_users a JOIN wallet_config b ON a.id = b.user WHERE a.id = ".$userId;
        return $mysql->mySQLquery($query);
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

    static function getOldestBuy($user)
    {
        $mysql = new MySQL();
        $query = "select *, TIMESTAMPDIFF(HOUR, date, now()) as elapsed FROM wallet_balance a 
                  JOIN wallet_currencys b ON a.book = b.book WHERE a.status = true 
                  and  a.user = ".$user." ORDER by date ASC LIMIT 1";
        
        return $mysql->mySQLquery($query)[0];
    }

    static function getChangeCurrency($book = 'btc_mxn', $time = 24)
    {
        $mysql = new MySQL();
        $query = "select * from wallet_analytics where book = '$book' and date > now() - interval $time hour limit 1";
        $prices_array['old_price'] = $mysql->mySQLquery($query)[0];

        $query = "select * from wallet_analytics where book = '$book' order by date desc limit 1";
        $prices_array['new_price'] = $mysql->mySQLquery($query)[0];

        return $prices_array;
    }


}