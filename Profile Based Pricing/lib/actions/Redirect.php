<?php 
require '../APIHelper.php';
if(isset($_GET["hostedpage_id"]))
{
	$apiHelper = new APIHelper();
	$apiHelper = new APIHelper("../../config/setup.ini");
	$hostedPageId = $_GET["hostedpage_id"];
	$url = $apiHelper->utils->setupIni["platformUrl"]."?hostedPageId=".$hostedPageId;
	header('Location: '.$url);
}
?>