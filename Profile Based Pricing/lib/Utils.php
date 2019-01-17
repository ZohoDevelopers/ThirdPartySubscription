<?php

include 'Constants.php';
session_start();

class Utils
{
	public $setupIni;
	public function __construct()
	{
		$args = func_get_args();
		switch(func_num_args())
        {
	        case 0:
	            $this->setupIni = parse_ini_file("../config/setup.ini");
	        break;
	        case 1:
	            $this->construct1($args[0]);
	        break;
	        default:break;
        }
	}
	public function construct1($path)
	{
		$this->setupIni = parse_ini_file($path);
	}

	public function makeApiCall($method,$url,$params,$type)
	{
		 $curl = curl_init();
		 if($type != "PLATFORM")
		 {
			 $header = array(
	    	'Authorization: Zoho-authtoken '. $this->setupIni["authToken"],
	    	'Content-Type: application/json;charset=UTF-8',
	    	'X-com-zoho-subscriptions-organizationid: '. $this->setupIni["organizationId"]
			);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		}
		else
		{
			 $header = array(
	    	'Content-Type: application/json;charset=UTF-8',
	    	'Content-Length: ' . strlen($params)
			);
			curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
		}
		 curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
		 if($method == "POST" || $method == "PUT" || $method == "DEL")
		 {
		 	if($params != NULL)
		 	{
		 		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		 	}
		 	 if($method == "PUT")
		 	{
		 	 	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
		 	}
		 	else if($method == "POST")
		 	{
		 		curl_setopt($curl,CURLOPT_POST,1);
		 	}
		 	else if($method == "DEL")
		 	{
		 		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
		 	}
		 }
		 curl_setopt($curl, CURLOPT_URL,$url);
		 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		 $result = curl_exec($curl);
   		 curl_close($curl);
   		 return $result;
	}

	public function setCustomerData($params)
	{
		$_SESSION[CUSTOMER_DATA] = $params;
	}

	public function getCustomerData()
	{
		return $_SESSION[CUSTOMER_DATA];
	}

	public function checkJsonResponse($json)
	{
		return ($json[CODE] == SUCCESS_CODE && strpos($json[MESSAGE],SUCCESS));
		// return true;
	}
	public function getSpecificCustomerData($key)
	{
		if($key != null)
		{
			return $_SESSION[CUSTOMER_DATA][$key] != null ? $_SESSION[CUSTOMER_DATA][$key] : null;
		}
	}

}

?>
