<?php

namespace Helpers;

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
}