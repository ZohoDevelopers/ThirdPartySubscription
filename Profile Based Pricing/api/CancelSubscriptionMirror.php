<?php 

require '../lib/APIHelper.php';

class CancelSubscriptionMirror
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
				$customFieldsObj = $customerObj["custom_fields"];
				$planId = $planObj["plan_code"];
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
				$this->json["CUSTOM_PRICING_JSON"] = json_encode(array());
			}
		}
	}
	public function process()
	{
		$callBackResult = $this->apiHelper->callPlatform("DEL",$this->json["PLAN_ID"],json_encode($this->json),$this->json["ZGID"]);
		echo $callBackResult;
	}

}
$request_body = file_get_contents('php://input');
$request_body = json_decode($request_body,true);
$cancelSub = new CancelSubscriptionMirror($request_body);
$cancelSub->setParameters();
$cancelSub->process();
echo "Success";


?>