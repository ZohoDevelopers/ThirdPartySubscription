<?php 

require '../lib/APIHelper.php';

class UpgradeSubscriptionMirror
{

	public $reqBody;
	public $apiHelper;
	public $json;
	public function __construct($reqBody)
	{
		$this->reqBody = $reqBody;
		$this->apiHelper = new APIHelper("../config/setup.ini");
		$this->json = array();
		$this->authenticate();
	}
	private function authenticate()
	{
		$zapiKey = (string) $_GET["zapikey"];
		$setupZapiKey = (string) $this->apiHelper->utils->setupIni["zapikey"];
		if(strcmp($zapiKey,$setupZapiKey) != 0)
		{
			die("Authentication Error");
			exit();
		}
	}
	public function setParameters()
	{
		$data = $this->reqBody["data"];
		if($data != NULL)
		{
			$subscriptionObj = $data["subscription"];
			if($subscriptionObj != NULL)
			{
				$customerObj = $subscriptionObj["customer"];
				$planObj= $subscriptionObj["plan"];
				$addonArray = $subscriptionObj["addons"];
				$customFieldsObj = $customerObj["custom_fields"];

				$email = $customerObj["email"];
				$planId = $planObj["plan_code"];
				$payperiod = 1;
				if(strpos($planId,"_Y") === true)
				{
					$payPeriod = 4;
				}
				$noOfUsers = 0;
				$amount = $subscriptionObj["amount"];
				$subscriptionId = $subscriptionObj["subscription_id"];

				$profileData = array();
				for($i=0;$i<count($addonArray);$i++)
				{
					$addonObject = $addonArray[$i];
					$addonCode = $addonObject["addon_code"];
					$quantity = $addonObject["quantity"];
					$profileData[$addonCode] = $quantity;
					$noOfUsers += $quantity;
				}

				for($i=0;$i<count($customFieldsObj);$i++)
				{
					$currentCustomField = $customFieldsObj[$i];
					$label = $currentCustomField["label"];
					$value = $currentCustomField["value"];
					if($label === "zgid")
					{
						$this->json["ZGID"] = $value;
					}
					else if($label === "zuid")
					{
						$this->json["ZUID"] = $value;
					}
				}

				$this->json["PLAN_ID"] = explode("_",$planId)[1];
				$this->json["NO_OF_USERS"] = $noOfUsers;
				$this->json["FREQUENCY"] = (string)$payperiod;
				$this->json["SUBSCRIPTION_ID"] = $subscriptionId;
				$this->json["CUSTOM_PRICING_JSON"] = $profileData;
				$this->json["STORAGE_UNITS"] = 1;
			}
		}
	}
	public function process()
	{
		$callBackResult = $this->apiHelper->callPlatform("PUT",$this->json["PLAN_ID"],json_encode($this->json),$this->json["ZGID"]);
		echo $callBackResult;
	}

}
$request_body = file_get_contents('php://input');
$request_body = json_decode($request_body,true);
$upgradeSub = new UpgradeSubscriptionMirror($request_body);
$upgradeSub->setParameters();
$upgradeSub->process();


?>