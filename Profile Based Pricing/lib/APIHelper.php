<?php

include 'Utils.php';

class APIHelper
{
	public $utils;
	public function __construct()
	{
		$args = func_get_args();
		switch(func_num_args())
        {
	        case 0:
	            $this->utils = new Utils("config/setup.ini");
	        break;
	        case 1:
	            $this->construct1($args[0]);
	        break;
	        default:break;
        }
	}
	public function construct1($path)
	{
		$this->utils = new Utils($path);
	}
	public function getAllPlans($productName)
	{
		$productObject = $this->getAllProducts();
		if($productObject != NULL)
		{
			$productId = "";
			for($i=0;$i<count($productObject);$i++)
			{
				$currentProduct = $productObject[$i];
				if($currentProduct["name"] == $productName)
				{
					$productId = $currentProduct["product_id"];
				}
			}
			$url = $this->utils->setupIni["baseUrl"]."".$this->utils->setupIni["getAllPlans"].$productId;
			$planObject = $this->utils->makeApiCall("GET",$url,null,"SUBSCRIPTIONS");	
			$planObject = json_decode($planObject,true);
			if($this->utils->checkJsonResponse($planObject))
			{
				return $planObject["plans"];
			}
		}
		return NULL;
		
	}

	public function getAllProducts()
	{
		$url = $this->utils->setupIni["baseUrl"]."".$this->utils->setupIni["getAllProducts"];
		$productObject = $this->utils->makeApiCall("GET",$url,null,"SUBSCRIPTIONS");
		$productObject = json_decode($productObject,true);
		if($this->utils->checkJsonResponse($productObject))
		{
			return $productObject["products"];
		}
		return NULL;

	}

	public function getPlan($planCode)
	{
		$url = $this->utils->setupIni["baseUrl"]."".$this->utils->setupIni["getPlan"].$planCode;
		$planObject = $this->utils->makeApiCall("GET",$url,null,"SUBSCRIPTIONS");
		$planObject = json_decode($planObject,true);
		if($this->utils->checkJsonResponse($planObject))
		{
			return $planObject["plan"];
		}
	}
	public function getAddonsForPlan($planCode)
	{
		$url = $this->utils->setupIni["baseUrl"]."".$this->utils->setupIni["getAddons"].$planCode;
		$addonsObject = $this->utils->makeApiCall("GET",$url,null,"SUBSCRIPTIONS");
		$addonsObject = json_decode($addonsObject,true);
		if($this->utils->checkJsonResponse($addonsObject))
		{
			return $addonsObject["addons"];
		}
	}

	public function generateHostedPage($params)
	{
		$url = $this->utils->setupIni["baseUrl"]."".$this->utils->setupIni["generateHostedPage"];
		$hostedPageObj = $this->utils->makeApiCall("POST",$url,$params,"SUBSCRIPTIONS");
		$hostedPageObj = json_decode($hostedPageObj,true);
		if($this->utils->checkJsonResponse($hostedPageObj))
		{
			return $hostedPageObj["hostedpage"];
		}
		return NULL;
	}

	public function getSubscription($subscriptionId)
	{
		$url = $this->utils->setupIni["baseUrl"]."".$this->utils->setupIni["getSubscription"].$subscriptionId;
		$subscriptionObject = $this->utils->makeApiCall("GET",$url,null,"SUBSCRIPTIONS");
		$subscriptionObject = json_decode($subscriptionObject,true);
		if($this->utils->checkJsonResponse($subscriptionObject))
		{
			return $subscriptionObject["subscription"];
		}
		return NULL;
	}

	public function updateSubscription($subscriptionId,$subscriptionInfo)
	{
		$url = $this->utils->setupIni["baseUrl"]."".$this->utils->setupIni["updateSubscription"].$subscriptionId;
		$subscriptionObject = $this->utils->makeApiCall("PUT",$url,$subscriptionInfo,"SUBSCRIPTIONS");
		$subscriptionObject = json_decode($subscriptionObject,true);
		if($this->utils->checkJsonResponse($subscriptionObject))
		{
			return $subscriptionObject["subscription"];
		}
		return NULL;
	}

	public function updateCard($params)
	{
		$url = $this->utils->setupIni["baseUrl"]."".$this->utils->setupIni["updateCard"];
		$cardObject = $this->utils->makeApiCall("POST",$url,$params,"SUBSCRIPTIONS");
		$cardObject = json_decode($cardObject,true);
		if($this->utils->checkJsonResponse($cardObject))
		{
			return $cardObject["hostedpage"];
		}
		return NULL;
	}
	public function callPlatform($type,$planId,$json,$zgid)
	{
		$url = $this->utils->setupIni[$planId];
		$url = str_replace("{CUSTOMERZGID}",$zgid, $url);
		$callbackObject = $this->utils->makeApiCall($type,$url,$json,"PLATFORM");
		return $callbackObject;
	}
	public function getHostedpage($hostedPageId)
	{
		$url = $this->utils->setupIni["baseUrl"]."".$this->utils->setupIni["getHostedpage"].$hostedPageId;
		$hostedPageObj = $this->utils->makeApiCall("GET",$url,null,"SUBSCRIPTIONS");
		$hostedPageObj = json_decode($hostedPageObj,true);
		if($this->utils->checkJsonResponse($hostedPageObj))
		{
			return $hostedPageObj["data"];
		}
		return NULL;
	}
	public function cancelSubscription($subscriptionId)
	{
		$url = $this->utils->setupIni["baseUrl"]."".$this->utils->setupIni["cancelSub"];
		$url = str_replace("{SID}",$subscriptionId, $url);
		$cancelSubObj = $this->utils->makeApiCall("POST",$url,null,"SUBSCRIPTIONS");
		$cancelSubObj = json_decode($cancelSubObj,true);
		if($this->utils->checkJsonResponse($cancelSubObj))
		{
			return $cancelSubObj["subscription"];
		}
		return NULL;
	}
}
?>