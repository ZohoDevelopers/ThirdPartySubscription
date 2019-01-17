<?php
include '../APIHelper.php';

$hostedPageUrl;
if(isset($_POST["Purchase"]))
{
	$purchase = new Purchase();
	$purchase->getFormValues();
	$purchase->process();

}
else
{
	echo "Error in Processing Request";
	die();
}


class Purchase
{
	public $apiHelper;
	private $planCode;
	private $addons;
	private $payFrequency;
	private $appDomain;

	public function __construct()
	{
		$this->apiHelper = new APIHelper("../../config/setup.ini");
		$this->addons = array();
	}

	public function getFormValues()
	{
		$planCode = $_POST["planCode"];
		$payFrequency = $_POST["typeToggle"] === "on" ? 4 : 1;
		$appDomain = explode("_", $planCode)[0];
		$addons = array();
		foreach($_POST as $key => $value)
		{ 
    		if(strpos($key,"addonSelect_") === 0)
    		{
    			$addonCodeSplit = explode('_', $key);
    			$addonCode = $addonCodeSplit[2]."_".$addonCodeSplit[3];
    			if($payFrequency === 4)
    			{
    				$addonCode = $addonCode."_Y";
    			}
    			$addons[$addonCode] = $value;
    		}
		}
		$this->setPlanCode($planCode);
		$this->setAddons($addons);
		$this->setPayFrequency($payFrequency);
		$this->setAppDomain($appDomain);
	}

	public function process()
	{
		$obj = array();
		$customerObj = $this->generateCustomerObject();
		$planObj = array();
		$addonArr = array();

		$planObj["plan_code"] = $this->getPayFrequency() == 4 ? $this->getPlanCode()."_Y" : $this->getPlanCode();

		$addons = $this->getAddons();
		$count = 0;
		foreach($addons as $key => $value)
		{ 
    		$tempArray = array();
    		$tempArray["addon_code"] = $key;
    		$tempArray["quantity"] = (int)$value;
    		$addonArr[$count++] = $tempArray;
		}
		$obj["customer"] = $customerObj;
		$obj["plan"] = $planObj;
		if(count($addonArr) > 0)
		{
			$obj["addons"] = $addonArr;
		}
		$obj["redirect_url"] = $this->apiHelper->utils->setupIni["redirectUrl"];

		$hostedPageResponse = $this->apiHelper->generateHostedPage(json_encode($obj));
		$GLOBALS['hostedPageUrl'] = $hostedPageResponse["url"];

	}
	private function generateCustomerObject()
	{
		$customerData = $this->apiHelper->utils->getCustomerData();
		unset($customerData["Code"]);
    	return $customerData;

	}

	public function setPlanCode($planCode)
	{
		$this->planCode = $planCode;
	}
	public function getPlanCode()
	{
		return $this->planCode;
	}
	public function setPayFrequency($payFrequency)
	{
		$this->payFrequency = $payFrequency;
	}
	public function getPayFrequency()
	{
		return $this->payFrequency;
	}
	public function setAppDomain($appDomain)
	{
		$this->appDomain = $appDomain;
	}
	public function getAppDomain()
	{
		return $this->appDomain;
	}
	public function setAddons($addons)
	{
		$this->addons = $addons;
	}
	public function getAddons()
	{
		return $this->addons;
	}
}
?>

<html>
<body>
	 
	<?php echo'<iframe src="'.$GLOBALS['hostedPageUrl'].'" id="subscriptionsFrame" style="width:100%;height:100%"></iframe>'; ?>
</body>
</html>