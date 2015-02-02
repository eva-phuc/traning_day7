<?php

// Caution!!: This way will cause some trouble, because $_GET['url'] can not obtain input original URL's GET parameters.
// It's just for your considerations.
// site: fashionwalker, bellemaison
$url = $_GET['url'];

if (empty($url)) {
    exit('no url');
}

$iosUrl = sprintf('http://192.168.33.103/ios/bookmark/api/url?item_detail_url=%s', urlencode($url));
$jsonBody = file_get_contents($iosUrl);
$array = json_decode($jsonBody);
echo $array->html;
?>
