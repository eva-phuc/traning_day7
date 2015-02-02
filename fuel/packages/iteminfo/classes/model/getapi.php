<?php
namespace Iteminfo;

/**
 * APIデータ取得
 * 
 * @author higuchi
 */
class Model_Getapi extends \Model {

    
    /**
     * APIを実行しデータ取得
     * 
     * @access public
     * @param string $url
     * @param array $setParams
     * @param string $mime
     * @return mixed
     * @author higuchi
     */
    public static function getData($url, $setParams=array(), $mime = 'xml') {
        

        if (! empty($setParams)) {
            $url .= '?'.http_build_query($setParams);
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
        $res = curl_exec($curl);
        $header = curl_getinfo($curl);
        $code = $header["http_code"];
        if ($code >= 400 || $res === false) {
            // 404error
            return false;
        }
        
        if ($code == 200) {
            switch($mime){
                case 'xml': 
                    $res = simplexml_load_string($res);
                    $res = self::_xml2arr($res);
                    break;
                
                case 'serialize':
                    $res = unserialize($res);
                    break;
                
                default:
                    break;
            }
            return $res;
        } else {
            return false;
        }
    }
    
    
    /**
     * XMLオブジェクトを配列化
     * 
     * @access public
     * @param objct $xmlobj
     * @return array
     * @author higuchi
     */
    private static function _xml2arr($xmlobj) {
        $arr = array();
        if (is_object($xmlobj)) {
            $xmlobj = get_object_vars($xmlobj);
        } else {
            $xmlobj = $xmlobj;
        }

        foreach ($xmlobj as $key => $val) {
            if (is_object($xmlobj[$key])) {
                $arr[$key] = self::_xml2arr($val);
            } else if (is_array($val)) {
                foreach($val as $k => $v) {
                    if (is_object($v) || is_array($v)) {
                        $arr[$key][$k] = self::_xml2arr($v);
                    } else {
                        $arr[$key][$k] = $v;
                    }
                }
            } else {
                $arr[$key] = $val;
            }

        }
        return $arr;
    }

}