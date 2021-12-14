<?php
/*
* import checksum generation utility
* You can get this utility from https://developer.paytm.com/docs/checksum/
*/
require_once("PaytmChecksum.php");
$custId = $_GET['custId'];
$orderId = $_GET["orderId"];
$amount = $_GET["amount"];
$paytmParams = array();

$paytmParams["body"] = array(
	"requestType" => "Payment",
	"mid"  => "lykteV79460944320676",
	"websiteName"  => "DEFAULT",
	"orderId"  => $orderId,
	"callbackUrl"  => "https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=$orderId",
	"txnAmount"  => array(
		"value"  => $amount,
		"currency" => "INR",
	 ),
	"userInfo" => array(
		"custId"=> $custId,
	),
	"enablePaymentMode" => array([
		array("mode" => "UPI")
	])
);

/*
* Generate checksum by parameters we have in body
* Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
*/
$checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "#eFD5kJQmMpmWB#q");

$paytmParams["head"] = array(
"signature" => $checksum
);

$post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

/* for Staging */
$url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=lykteV79460944320676&orderId=$orderId";

/* for Production */
// $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=lykteV79460944320676&orderId=$orderId";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
$response = curl_exec($ch);
print_r($response);
// $data = json_decode($response, true);
// print_r($data['body']['resultInfo']['resultStatus']);
// print_r($data['body']['txnToken']);
?>