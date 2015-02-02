<?php

namespace Iteminfo;

/**
 * サイト解析
 * 
 * @author higuchi
 */
class Siteparse {

    private $itemData;

    /**
     * construct
     * 
     * @access public
     * @author higuchi
     */
    public function __construct() {
        \Config::load('sites', 's');
    }

    /**
     * URLを解析して商品情報を取得
     * 
     * @access public
     * @param array $url
     * @return mixed
     * @author bool
     */
    public function setUrl($url) {
        $hosts = \Config::get('s.hosts');

        $arrayUrl = parse_url($url);

        if (empty($arrayUrl['host']))
            return false;
        
        foreach ($hosts as $siteKey => $hostname){
            if($arrayUrl['host'] === $hostname)
                break;
            
            if(is_array($hostname) && in_array($arrayUrl['host'], $hostname))
                break;
        }
        
        if ($siteKey == "")
            return false;
        
        $func = "urlParse" . ucfirst($siteKey);        
        $this->itemData = $this->{$func}($url);
        
        return true;
        //parse_str ( $arrayUrl['query'], $query_arr );
    }

    /**
     * 商品情報の受渡
     * 
     * @access public
     * @return mixed
     * @author higuchi
     */
    public function getData() {
        return $this->itemData;
    }

    /**
     * Yahooショッピングの情報解析
     * 
     * @access private
     * @param array $url
     * @return array
     * @author higuchi
     */
    private static function urlParseYshop($url) {
        $hostUrl = \Config::get('s.urls.yshop');
        $site = \Config::get('s.site');

        // ショップ名と商品IDに解体し商品参照コードを作成
        try {
            $arrayUrl = parse_url($url);
            if (empty($arrayUrl['path']))
                return false;
            $str = explode('/', $arrayUrl['path']);
            if (empty($str[1]) || empty($str[2]))
                return false;
            $shop = $str[1];
            $itemHtml = $str[2];
            $item = explode('.html', $itemHtml);
            $item = $item[0];
            $itemCode = $shop . '_' . $item;

            // 登録用URL
            $regUrl = $hostUrl . $shop . '/' . $itemHtml;

            $siteParams['site'] = $site[1];
            $siteParams['url'] = $regUrl;
            $siteParams['item_code'] = $item;
            $siteParams['shop_code'] = $shop;
            $siteParams['itemcode'] = $itemCode;
        } catch (\Exception $e) {
            \Common\Error::instance()
                    ->set_log($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage())
                    ->set_email($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage(), '[ERROR] API ')
                    ->logging();
            return false;
        }

        // 商品データ取得
        $siteParams['item'] = Model_Site_Yshop::getData($itemCode);


        return $siteParams;
    }

    /**
     * ZOZOTOWNの情報解析
     * 
     * @access private
     * @param array $url
     * @return array
     * @author higuchi
     */
    private static function urlParseZozo($url) {
        $hostUrl = \Config::get('s.urls.zozo');
        $site = \Config::get('s.site');

        // ショップ名と商品IDに解体
        try {
            $arrayUrl = parse_url($url);
            if (empty($arrayUrl['path']))
                return false;
            $str = explode('/', $arrayUrl['path']);
            if (empty($str[1]))
                return false;

            if ($str[1] == 'sp') {
                if (empty($str[3]) || empty($str[5]))
                    return false;
                $shop = $str[3];
                $item = $str[5];
            } else {
                if (empty($str[2]) || empty($str[4]))
                    return false;
                $shop = $str[2];
                $item = $str[4];
            }

            // USEDは対象外
            if ($shop == 'zozoused')
                return false;

            // 登録用URL
            $regUrl = $hostUrl . 'shop/' . $shop . '/goods/' . $item . '/';

            $siteParams['site'] = $site[2];
            $siteParams['url'] = $regUrl;
            $siteParams['item_code'] = $item;
            $siteParams['shop_code'] = $shop;
        } catch (\Exception $e) {
            \Common\Error::instance()
                    ->set_log($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage())
                    ->set_email($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage(), '[ERROR] API ')
                    ->logging();
            return false;
        }

        // 商品データ取得
        $siteParams['item'] = Model_Site_Zozo::getData($regUrl);

        return $siteParams;
    }

    /**
     * NISSENの情報解析
     * 
     * @access private
     * @param array $url
     * @return array     
     */
    private static function urlParseNissen($url) {
        $hostUrl = \Config::get('s.urls.nissen');
        $site = \Config::get('s.site');

        // URL解体
        try {
            // 商品IDを解体
            $arrayUrl = parse_url($url);
            if (empty($arrayUrl['path']))
                return false;

            $str = explode('/', ltrim($arrayUrl['path'], '/'));

            if (count($str) < 4 || $str[0] != 'sho_item')
                return false;

            $item = preg_replace('/[^+\d]/', '', $str[3]);
            // 登録用URL
            $regUrl = $hostUrl . implode('/', $str);
            $siteParams['site'] = $site[9];
            $siteParams['url'] = $regUrl;
            $siteParams['item_code'] = $item;
            $siteParams['shop_code'] = (int) $str[2];

            // 商品データ取得
            $siteParams['item'] = Model_Site_Nissen::getData($regUrl);

            return $siteParams;
        } catch (\Exception $e) {
            \Common\Error::instance()
                    ->set_log($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage())
                    ->set_email($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage(), '[ERROR] API ')
                    ->logging();
            return false;
        }
    }

    /**
     * BELLEMAISONの情報解析
     * 
     * @access private
     * @param array $url
     * @return array     
     */
    private function urlParseBellemaison($url) {
        $hostUrl = \Config::get('s.urls.bellemaison');
        $site = \Config::get('s.site');
        
        // URL解体
        try {
            // 商品IDを解体
            $arrayUrl = parse_url($url);
            if (!isset($arrayUrl['path']) || empty($arrayUrl['path']))
                return false;            
            $str = explode('/', $arrayUrl['path']);
            if (count($str) < 5)
                return false;
            //check special url
            if ($str[1] != 100 && isset($arrayUrl['query'])) {
                $query = explode("KAT_BTGO=", $arrayUrl['query']);
                if (isset($query[1])) {
                    $query = explode("&", $query[1]);
                    $strId = explode("_", $query[0]);
                    $str[4] = $strId[0];
                    array_shift($strId);
                    $str[3] = implode("", $strId);
                }
            }
            $item = $str[3] . '-' . $str[4];
            
            $siteParams = array();
            $siteParams['site'] = $site[10];
            $siteParams['url'] = $url;
            $siteParams['item_code'] = $item;            
            // 商品データ取得
            $siteParams['item'] = Model_Site_Bellemaison::getData($url);

            return $siteParams;
        } catch (\Exception $e) {
            \Common\Error::instance()
                    ->set_log($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage())
                    ->set_email($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage(), '[ERROR] API ')
                    ->logging();
            return false;
        }
    }

    /**
     * MAGASEEKの情報解析
     * 
     * @access private
     * @param array $url
     * @return array
     * @author higuchi
     */
    private static function urlParseMagaseek($url) {
        $hostUrl = \Config::get('s.urls.magaseek');
        $site = \Config::get('s.site');
        // URL解体
        try {
            // 商品IDを解体
            $arrayUrl = parse_url($url);
            if (!isset($arrayUrl['path']) || empty($arrayUrl['path']))
                return false;
            $str = explode('/', ltrim($arrayUrl['path'], '/'));
            if (count($str) < 3 || $str[1] != 'detail')
                return false;
            $item = explode('-', $str[2]);
            $item = preg_replace('/[^+\d]/', '', $item[0]);
                        
            $siteParams['site'] = $site[11];
            $siteParams['url'] = $url;
            $siteParams['item_code'] = $item;

            // 商品データ取得
            $siteParams['item'] = Model_Site_Magaseek::getData($url);

            return $siteParams;
        } catch (\Exception $e) {
            \Common\Error::instance()
                    ->set_log($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage())
                    ->set_email($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage(), '[ERROR] API ')
                    ->logging();
            return false;
        }
    }

    /**
     * STYLIFEの情報解析
     * 
     * @access private
     * @param array $url
     * @return array     
     */
    private static function urlParseStylife($url) {
        $hostUrl = \Config::get('s.urls.stylife');
        $site = \Config::get('s.site');
        // URL解体
        try {
            // 商品IDを解体
            $arrayUrl = parse_url($url);
            if (empty($arrayUrl['path']))
                return false;
            $str = explode('/', ltrim($arrayUrl['path'], '/'));
            if (count($str) < 3 || $str[1] != 'item')
                return false;
            $item = $str[2];
            
            $siteParams['site'] = $site[12];
            $siteParams['url'] = $url;
            $siteParams['item_code'] = $item;

            // 商品データ取得
            $siteParams['item'] = Model_Site_Stylife::getData($url);

            return $siteParams;
        } catch (\Exception $e) {
            \Common\Error::instance()
                    ->set_log($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage())
                    ->set_email($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage(), '[ERROR] API ')
                    ->logging();

            return false;
        }
    }

    private static function urlParseFelissimo($url) {
        $site = \Config::get('s.site');
        try {
            $arrayUrl = parse_url($url);
            if (empty($arrayUrl['path']))
                return false;
            $str = explode('/', $arrayUrl['path']);
            if (empty($str[1]))
                return false;
            $shop = $str[1];
            $query = array();
            $item = '';
            if (isset($arrayUrl['query']))
                parse_str($arrayUrl['query'], $query);
            
            $query = array_change_key_case($query);
            if (isset($query['gcd']))
                $item = $query['gcd'];
            if (isset($str[2]) && strpos($str[2], 'gcd') !== false)
                $item = str_replace("gcd", "", $str[2]);
            if (isset($str[3]) && strpos($str[3], 'gcd') !== false)
                $item = str_replace("gcd", "", $str[3]);
            $siteParams['site'] = $site[5];
            $siteParams['url'] = $url;
            $siteParams['item_code'] = $item;
            $siteParams['shop_code'] = $shop;
        } catch (\Exception $e) {
            \Common\Error::instance()
                    ->set_log($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage())
                    ->set_email($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage(), '[ERROR] API ')
                    ->logging();
            return false;
        }
        $siteParams['item'] = Model_Site_Felissimo::getData($url);
        return $siteParams;
    }

    private static function urlParseLocondo($url) {
        $hostUrl = \Config::get('s.urls.locondo');
        $site = \Config::get('s.site');
        try {
            $arrayUrl = parse_url($url);
            if (empty($arrayUrl['path']))
                return false;
            $str = explode('/', $arrayUrl['path']);
            if (empty($str[1]))
                return false;
            if (empty($str[2]) || $str[2] != 'commodity')
                return false;
            if (empty($str[3]) || empty($str[4]))
                return false;
            $item = $str[4];
            $code = $str[3];
            $regUrl = $hostUrl . 'shop/commodity/' . $code . '/' . $item . '/';
            $siteParams['site'] = $site[6];
            $siteParams['url'] = $regUrl;
            $siteParams['item_code'] = $item;
            $siteParams['shop_code'] = $code;
        } catch (\Exception $e) {
            \Common\Error::instance()
                    ->set_log($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage())
                    ->set_email($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage(), '[ERROR] API ')
                    ->logging();
            return false;
        }
        $siteParams['item'] = Model_Site_Locondo::getData($regUrl);
        return $siteParams;
    }

    private static function urlParseFashionwalker($url) {
        $site = \Config::get('s.site');
        $host = \Config::get('s.hosts.fashionwalker');
        try {
            $arrayUrl = parse_url($url);
            if (empty($arrayUrl['path']))
                return false;
            $query = '';
            $item = '';
            if (isset($arrayUrl['query']))
                parse_str($arrayUrl['query'], $query);
            if (isset($query['PC']))
                $item = $query['PC'];
            $siteParams['site'] = $site[7];
            $url = str_replace($host[1], $host[0], $url);
            $siteParams['url'] = $url;
            $siteParams['item_code'] = $item;
        } catch (\Exception $e) {
            \Common\Error::instance()
                    ->set_log($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage())
                    ->set_email($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage(), '[ERROR] API ')
                    ->logging();
            return false;
        }
        $siteParams['item'] = Model_Site_Fashionwalker::getData($url);
        return $siteParams;
    }

    private static function urlParseDinos($url) {
        $hostUrl = \Config::get('s.urls.dinos');
        $site = \Config::get('s.site');
        try {
            $arrayUrl = parse_url($url);
            if (empty($arrayUrl['path']))
                return false;
            $query = '';
            $param = '';
            if (isset($arrayUrl['query']))
                parse_str($arrayUrl['query'], $query);
            $str = explode('/', $arrayUrl['path']);
            if (empty($str[2]))
                return false;
            $item = $str[2];
            if (isset($query['GOODS_NO'])) {
                $item = $query['GOODS_NO'];
                $param = '?GOODS_NO=' . $item;
            }
            $regUrl = $hostUrl . ltrim($arrayUrl['path'], "/") . $param;
            $siteParams['site'] = $site[8];
            $siteParams['url'] = $regUrl;
            $siteParams['item_code'] = $item;
        } catch (\Exception $e) {
            \Common\Error::instance()
                    ->set_log($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage())
                    ->set_email($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage(), '[ERROR] API ')
                    ->logging();
            return false;
        }
        $siteParams['item'] = Model_Site_Dinos::getData($regUrl);
        return $siteParams;
    }

    private static function urlParseBuyma($url) {
        $hostUrl = \Config::get('s.urls.buyma');
        $site = \Config::get('s.site');
        // URL解体
        try {
            // 商品IDを解体
            $arrayUrl = parse_url($url);
            if (empty($arrayUrl['path']))
                return false;
            $str = explode('/', ltrim($arrayUrl['path'], '/'));

            if (count($str) < 3 || $str[0] != 'item')
                return false;
            $item = $str[1];

            $siteParams['site'] = $site[13];
            $siteParams['url'] = $url;
            $siteParams['item_code'] = $item;

            // 商品データ取得
            $siteParams['item'] = ModelSiteBuyma::getData($url);
            return $siteParams;
        } catch (\Exception $e) {
            \Common\Error::instance()
                    ->set_log($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage())
                    ->set_email($e->getFile() . ' ' . $e->getLine() . ' : ' . $e->getMessage(), '[ERROR] API ')
                    ->logging();

            return false;
        }
    }
}
