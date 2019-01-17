<?php 
require '../APIHelper.php';

if(isset($_POST["data"]))
{
	$handler = new Handler();
	$handler->getFormValues();
	$handler->process();
}

class Handler
{
	public $apiHelper;
	private $subscriptionId;
	private $planCode;
	private $type;
	private $json;

	public function __construct()
	{
		$this->apiHelper = new APIHelper("../../config/setup.ini");
		$this->json = array();
	}

	public function getFormValues()
	{
		$data = $_POST["data"];
		for($i=0;$i<count($data);$i++)
		{
			$object = $data[$i];
			$name = $object["name"];
			$value = $object["value"];
			switch($name)
			{
				case "subscriptionId" : $this->subscriptionId = $value;break;
				case "planCode" : $this->planCode = $value;break;
				case "type" : $this->type = $value;break;
				default : $this->json[$name] = $value;break;
			}
		}
	}
	public function process()
	{
		$data = array();
		$json = $this->generateUpdateSubscriptionObject($this->type);
		if($this->type === "CancelSubscription")
		{
			$subscriptionResponse = $this->apiHelper->cancelSubscription($this->subscriptionId,json_encode($json));
		}
		else
		{
			$subscriptionResponse = $this->apiHelper->updateSubscription($this->subscriptionId,json_encode($json));
		}
		if($subscriptionResponse == NULL)
		{
			echo "Error in Updating Subscription";
		}
		else
		{
			echo "Success in Updating Subscription";
		}
	}

	public function generateUpdateSubscriptionObject($type)
	{
		$finalJson = array();
		$map = array();
		if($type === "DowngradeUsers")
		{
			$map = $this->getDowngradedMap();
		}
		else if($type === "UpgradeUsers")
		{
			$map = $this->getUpgradedMap();
		}
		else if($type === "CancelSubscription")
		{
			return $finalJson;
		}
		$planObj = array();
		$planObj["plan_code"] = $this->planCode;
		$addons = array();
		$count = 0;
		foreach($map as $key => $value) 
		{
			$object =array();
			$object["addon_code"] = $key;
			$object["quantity"] = $value;
			$addons[$count++] = $object;
		}
		$finalJson["plan"] = $planObj;
		$finalJson["addons"] = $addons;
		return $finalJson;
	}

	private function getDowngradedMap()
	{
		$map = array();
		foreach($this->json as $key => $value) 
		{
			$isReduceUsers = strpos($key,"reduceUsers_");
			$isPurchasedCount = strpos($key,"purchasedCount_");
			$isAddonCode = strpos($key,"addonCode_");

			if($isReduceUsers === 0 || $isPurchasedCount === 0)
			{
				continue;
			}
			else if($isAddonCode === 0)
			{
				$addonCode = substr($key,10);
				$purchasedCount = (int)$this->json["purchasedCount_".$addonCode];
				$reduceCount = (int)$this->json["reduceUsers_".$addonCode];
				$map[$addonCode] = $purchasedCount - $reduceCount;
			}
		}
		return $map;
	}
	private function getUpgradedMap()
	{
		$map = array();
		foreach($this->json as $key => $value)
		{
			$isPurchasedUsers = strpos($key,"purchasedUsers_");
			$isAddUsers = strpos($key,"addUsers_");
			$isAddonCode = strpos($key,"addonCodeAddUsers_");
			if($isPurchasedUsers === 0 || $isAddUsers === 0)
			{
				continue;
			}
			else if($isAddonCode === 0)
			{
				$addonCode = substr($key,18);
				$purchasedCount = (int)$this->json["purchasedUsers_".$addonCode];
				$reduceCount = (int)$this->json["addUsers_".$addonCode];
				$map[$addonCode] = $purchasedCount + $reduceCount;
			}

		}
		return $map;
	}
}
?>