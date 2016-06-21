<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
//Data, connection, auth
//http://data.brreg.no/enhetsregisteret/enhet.{format}?page={side}&size={antall per page} & $ filter = {filter}
$format = $_REQUEST['format'];
$page = $_REQUEST['side'];
$size = $_REQUEST['antall_per_page'];
$filterSet = $_REQUEST['filter'];
$filterData = explode(':', $filterSet);
$filterParam = $filterData[0];
$filterValue = $filterData[1];
## echo "<pre/>";print_r($_REQUEST);die;
if($filterParam == "name"):
	$filterParam = "navn";
endif;
$filterset = "startswith($filterParam,'".$filterValue."')";
$endpointUrl = 'http://data.brreg.no/enhetsregisteret/enhet.'.$format.'?page='.$page.'&size='.$size.'&$filter='.$filterset;
## echo "<pre/>";print_r($endpointUrl);
$ch = curl_init($endpointUrl);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');

$data = curl_exec($ch);
curl_close($ch);

$finalData = json_decode($data);
$firms = $finalData->data;
## echo "<pre/>";print_r(count($firms));
## echo "<pre/>";print_r($firms);die;

foreach($firms as $firm):
	echo $firm->navn."<br/>";
endforeach;
?>
