<?php

namespace classes;

require_once('mySQL.php');
require_once('Bitso.php');

use classes\MySQL;

class BitsoWallet extends Bitso 
{
	public $user = null;
	protected $bitsoKey;
	protected $bitsoSecret;

	public function __construct($user = null)
	{
		$mysql = new MySQL();
		$this->user = $user;
		$data  = $mysql->mySQLquery("SELECT * FROM wallet_config WHERE user = $user");
		if ($data){
			$this->bitsoKey 	= $data[0]->bitso_key;
			$this->bitsoSecret  = $data[0]->bitso_secret;

		}
	}

	public function getCurrencysBought()
	{
		$mysql = new MySQL();
		$query = "Select a.book, SUM(amount) as amount, SUM(price * amount) as value, b.file 
				FROM wallet_balance a LEFT JOIN wallet_currencys b ON a.book = b.book 
				WHERE status = 1 and user = ".$this->user." GROUP BY a.book";
		
		return $mysql->mySQLquery($query);
	}

    public function getAverageHistory()
    {
        $mysql = new MySQL();
        $sql = "select * from (
                    select avg(amount) amount, date_format(date, '%Y-%m-%d') as newdate 
                    from wallet_performance where user = ".$this->user." group by newdate order by date desc limit 60
                ) tbl order by newdate asc";
        
        return $mysql->mySQLquery($sql);
    }

    public function getListMyCurrencies($book)
    {
		$mysql = new MySQL();
		$query = "Select *, TIMESTAMPDIFF(HOUR, date, now()) AS time_elapsed 
				FROM wallet_balance WHERE book = '$book' and user = ".$this->user." and status = 1 
				ORDER BY price DESC";

		return $mysql->mySQLquery($query);
    }

	public function getLatestBalance()
	{
		$mysql = new MySQL();
		$query = "SELECT * FROM wallet_performance ORDER BY date DESC LIMIT 1";
		$data  = $mysql->mySQLquery($query);

		$last_balance = 0;
		if(!empty($data)){
			$last_balance = $data[0]->amount;
		}
		return $last_balance;
	}

	public function getCurrentChange($current_balance)
	{
		$mysql = new MySQL();
		$query = "SELECT * FROM wallet_performance ORDER BY id DESC LIMIT 1";
		$data  = $mysql->mySQLquery($query);

		if(!empty($data)){
			$last_price = $data[0]->amount;
		
			$change = (($current_balance - $last_price) / $current_balance) * 100;
			$change = number_format($change, 2);

			return $change;
		}
	}

	public function getAverageTrades()
	{
		$mysql = new MySQL();
		$query = "Select AVG(trades) average FROM (
			SELECT COUNT(*) trades, date_format(date,'%u-%Y') week FROM wallet_balance GROUP BY week) tbl";
		$result = $mysql->mySQLquery($query);

		return $result[0]->average;
	}

	public function getPerformanceIntime($time_elapsed = 12)
	{	
		$mysql = new MySQL();
		$query = "SELECT SUM(difference) as performance FROM (
					SELECT difference FROM wallet_performance ORDER BY date DESC LIMIT $time_elapsed) as AVG";
		$result = $mysql->mySQLquery($query);

		return number_format($result[0]->performance, 2);
	}

	public function getLastBoughtPrices($book = 'btc_mxn')
	{
		$mysql = new MySQL();
		$sql = "select * from wallet_balance where book = '$book' order by date desc limit 1";
		return $mysql->mySQLquery($sql)[0];
	}

	public function getOldestBuy(){
		$mysql = new MySQL();
		$query = "SELECT *, TIMESTAMPDIFF(HOUR, date, now()) as elapsed FROM wallet_balance a 
				  JOIN wallet_currencys b ON a.book = b.book ORDER by date ASC LIMIT 1";
		$data  = $mysql->mySQLquery($query);

		$text  = $this->convertTimeToText($data[0]->elapsed);

		return $text;
	}

	public function getMinimunMaximun($book)
	{
		$mysql = new MySQL();
		$query = "SELECT min(price) minimum, max(price) maximun, ((min(price) - max(price))/max(price))*100 diff, date, volume
				  FROM (SELECT * FROM wallet_analytics WHERE book = '$book' ORDER BY date DESC LIMIT 24) Tbl";
		$data  = $mysql->mySQLquery($query);

		if(!empty($data)){
			return $data[0];
		}				  

	}

	public function getWalletBalances()
	{
		$balances = $this->getBalance();
		$ticker   = $this->getTicker();

		$balanceValue = array();
		foreach ($balances as $key => $value) {

			$book = $value->currency.'_mxn';

			if ($value->currency == 'mxn'){
				$balanceValue[] = [
					'currency' => $value->currency,
					'amount'   => $value->total,
					'value'    => $value->total,
				];
			} else if ($value->total > 0.002){
				if( array_key_exists($book, $ticker) ){
					$balanceValue[] = [
						'currency' => $value->currency,
						'amount'   => $value->total,
						'value'    => $value->total * $ticker[$book],
					];
				} else {
					$book = $value->currency.'_usd';
					if (array_key_exists($book, $ticker)){
						$balanceValue[] = [
							'currency' => $value->currency,
							'amount'   => $value->total,
							'value'    => $value->total * $ticker[$book] * $ticker['usd_mxn'],
						];
					}
				}
			}
		}

		return $balanceValue;

	}


	/*
	ONLY API LEVEL
	*/

	public function getChartPerformance()
	{
		$mysql = new MySQL();	
		$query = "Select date, amount 
				  FROM (SELECT id, DATE_FORMAT(date,'%l.%p') as date, TRUNCATE(amount,2) as amount 
				  FROM wallet_performance WHERE user = ".$this->user." ORDER BY id DESC LIMIT 20) Tbl ORDER BY id ASC";
		return $mysql->mySQLquery($query);
	}

	public function getChartMarketData($book = 'btc_mxn', $limit = 24)
	{
		$mysql = new MySQL();
		$query = "select id, price, volume, date_format(date, '%H:%i') as date from (
					SELECT * FROM wallet_analytics WHERE book = '$book' ORDER by date desc limit $limit
				) tbl order by id asc";
		$result = $mysql->mySQLquery($query);
		return $result;
	}

	public function getChartData($limit = 24)
	{
		$mysql = new MySQL();
		$query = "SELECT * FROM (
					SELECT date, amount, difference 
					FROM wallet_performance ORDER BY date DESC LIMIT $limit) tbl ORDER BY date";
		$result = $mysql->mySQLquery($query);
		return $result;
	}

	public function getWalletMainList()
	{
		$mysql = new MySQL();
		$query = "SELECT a.id, a.book, a.price, date_format(a.date, '%d/%m/%Y') as date, b.currency 
				  FROM wallet_balance a JOIN wallet_currencys b ON a.book = b.book WHERE a.status = 1 
				  ORDER BY a.date DESC";
		return $mysql->mySQLquery($query);
	}

	public function getBalanceHistory($limit = 12)
	{
		$mysql = new MySQL();
		$query = "Select * FROM wallet_performance WHERE user = ".$this->user." ORDER BY date DESC LIMIT $limit";
		return $mysql->mySQLquery($query);
	}

	public function getListTrades()
    {
        $mysql = new MySQL();
        $query = "select book, COUNT(*) trades FROM `wallet_balance` 
        	WHERE status = 1 and user = ".$this->user." GROUP BY book ORDER BY trades";
        return $mysql->mySQLquery($query);
    }

	public function sendWebHook($event, $data) 
	{
	    $curl = curl_init();
	    curl_setopt_array($curl, array(
	      CURLOPT_URL => 'https://maker.ifttt.com/trigger/'.$event.'/json/with/key/b02sH9pYZV0xykH4H8K2wT',
	      CURLOPT_RETURNTRANSFER => true,
	      CURLOPT_ENCODING => '',
	      CURLOPT_MAXREDIRS => 10,
	      CURLOPT_TIMEOUT => 0,
	      CURLOPT_FOLLOWLOCATION => true,
	      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	      CURLOPT_CUSTOMREQUEST => 'POST',
	      CURLOPT_POSTFIELDS => json_encode($data),
	      CURLOPT_HTTPHEADER => array(
	        'Content-Type: application/json'
	      ),
	    ));

	    $response = curl_exec($curl);
	    curl_close($curl);
	    return $response;
	}

	/* 
	FOR TRADE
	*/

	public function updateBuyingPower($data)
	{
		$mysql = new MySQL();
		$query = "SELECT * FROM wallet_available where currency = 'mxn'";

		if (!is_null( $mysql->mySQLquery($query) )) {
			$mysql->mySQLupdate("wallet_available", $data, "currency = 'mxn'");
		}

	}

	/* 
	FOR DEBUG
	*/

	protected function convertTimeToText($time)
	{
		$days   = ($time/24);
		$hours  = ($time%24);
		$string = number_format($days,0) ." days ". $hours ." hours ago"; 

		return $string;
	}

	public function writeLogfile($filename, $content)
	{
		$date = date('dmY');
		$file = fopen('/home/fortechm/test.fortech.mx/crons/'.$filename.'-'.$date.'.log' , 'a+');
		fwrite($file, 'Content: '. $content);
		fclose($file);
	}	
	
}