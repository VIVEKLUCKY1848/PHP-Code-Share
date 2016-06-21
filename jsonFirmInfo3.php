<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

function get_remote_data($url, $post_paramtrs=false)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    if($post_paramtrs)
    {
        curl_setopt($c, CURLOPT_POST,TRUE);
        curl_setopt($c, CURLOPT_POSTFIELDS, "var1=bla&".$post_paramtrs );
    }
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:33.0) Gecko/20100101 Firefox/33.0");
    curl_setopt($c, CURLOPT_COOKIE, 'CookieName1=Value;');
    curl_setopt($c, CURLOPT_MAXREDIRS, 10);
    $follow_allowed= ( ini_get('open_basedir') || ini_get('safe_mode')) ? false:true;
    if ($follow_allowed)
    {
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    }
    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 9);
    curl_setopt($c, CURLOPT_REFERER, $url);
    curl_setopt($c, CURLOPT_TIMEOUT, 60);
    curl_setopt($c, CURLOPT_AUTOREFERER, true);
    curl_setopt($c, CURLOPT_ENCODING, 'gzip,deflate');
    $data=curl_exec($c);
    $status=curl_getinfo($c);
    curl_close($c);
    preg_match('/(http(|s)):\/\/(.*?)\/(.*\/|)/si',  $status['url'],$link); $data=preg_replace('/(src|href|action)=(\'|\")((?!(http|https|javascript:|\/\/|\/)).*?)(\'|\")/si','$1=$2'.$link[0].'$3$4$5', $data);   $data=preg_replace('/(src|href|action)=(\'|\")((?!(http|https|javascript:|\/\/)).*?)(\'|\")/si','$1=$2'.$link[1].'://'.$link[3].'$3$4$5', $data);
    if($status['http_code']==200)
    {
        return $data;
    }
    elseif($status['http_code']==301 || $status['http_code']==302)
    {
        if (!$follow_allowed)
        {
            if (!empty($status['redirect_url']))
            {
                $redirURL=$status['redirect_url'];
            }
            else
            {
                preg_match('/href\=\"(.*?)\"/si',$data,$m);
                if (!empty($m[1]))
                {
                    $redirURL=$m[1];
                }
            }
            if(!empty($redirURL))
            {
                return  call_user_func( __FUNCTION__, $redirURL, $post_paramtrs);
            }
        }
    }
    return "ERRORCODE22 with $url!!<br/>Last status codes<b/>:".json_encode($status)."<br/><br/>Last data got<br/>:$data";
}

//Data, connection, auth
//http://data.brreg.no/enhetsregisteret/enhet.{format}?page={side}&size={antall per page} & $ filter = {filter}
$format = $_REQUEST['format'];
$page = $_REQUEST['side'];
$size = $_REQUEST['antall_per_page'];
$filterSet = $_REQUEST['filter'];
$filterData = explode(':', $filterSet);

$filterParam = $filterData[0];
$filterType = $filterData[1];
$filterValue = $filterData[2];
## echo "<pre/>";print_r($_REQUEST);die;
if($filterParam == "name"):
	$filterParam = "navn";
endif;

if($filterType == "startswith"):
	$filterset = "startswith($filterParam,'".$filterValue."')";
elseif($filterType == "equalto"):
	$filterset = "$filterParam eq '".$filterValue."'";
endif;

$endpointUrl = 'http://data.brreg.no/enhetsregisteret/enhet.'.$format.'?page='.$page.'&size='.$size.'&$filter='.$filterset;
echo "<pre/>";print_r($endpointUrl);

/*$jsonData = json_decode(stream_get_contents(fopen($endpointUrl, 'rb')));
echo "<pre/>";print_r($jsonData);die;*/

$firmsData = get_remote_data($endpointUrl);
echo "<pre/>";print_r($firmsData);die;

$ch = curl_init($endpointUrl);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');

$data = curl_exec($ch);

if(curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
	var_dump($data);die;
}

curl_close($ch);

$finalData = json_decode($data);
$firms = $finalData->data;
## echo "<pre/>";print_r(count($firms));
echo "<pre/>";print_r($firms);die;

foreach($firms as $firm):
	echo $firm->navn."<br/>";
endforeach;
?>
