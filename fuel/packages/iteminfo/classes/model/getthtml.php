<?php
namespace Iteminfo;

/**
 * HTMLデータ取得
 * 
 * @author higuchi
 */
class Model_Gethtml extends \Model {

    /**
     * URLからHTMLソース取得
     * 
     * @access public
     * @param string $url
     * @return mixed
     * @author higuchi
     */
    public static function getData($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
        $res = curl_exec($curl);
        $header = curl_getinfo($curl);
        $code = $header["http_code"];
        if ($code >= 400) {
            // 404error
            return false;
        }
        if (! empty($res) && $code == 200) {
            return $res;
        } else {
            // TimeOutError etc..
            return false;
        }
    }

}