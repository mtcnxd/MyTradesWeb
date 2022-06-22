<?php

namespace Helpers;

class Helpers
{
    protected function select_min()
    {
        $mysql = new MySQL();
        $query = "SELECT book, SUM(price * amount) as value FROM wallet_balance GROUP BY book ORDER by value ASC LIMIT 1";
        $data  = $mysql->mySQLquery($query);
        return $data[0];
    }

    protected function select_max()
    {
        $mysql = new MySQL();	
        $query = "SELECT book, SUM(price * amount) as value FROM wallet_balance GROUP BY book ORDER by value DESC LIMIT 1";
        $data  = $mysql->mySQLquery($query);
        return $data[0];
    }

    public function sendWebHook($event, $data) 
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
}