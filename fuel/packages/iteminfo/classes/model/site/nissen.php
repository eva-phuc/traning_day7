<?php

namespace Iteminfo;

/**
 * Nissen API
 */
class Model_Site_Nissen extends \Model
{

    /**
     * Parse item data from product url
     * 
     * @param string $url
     * @return mixed
     */
    public static function getData($url)
    {
        \Config::load('sites', 's');
        $hostUrl = \Config::get('s.urls.nissen');
        $tax = \Config::get('s.tax');

        //parse url to set stock URL
        $arrayUrl = parse_url($url);
        $str = explode('/', ltrim($arrayUrl['path'], '/'));
        $item = substr(strrchr(strstr($str[3], '.', true), "_"), 1);
        $stockURL = $hostUrl . "GetItemStockInfo.servlet?method_flg=1&book_no=" . $str[2] . "&sho_no=" . $item;

        //fetch and check html data
        $htmlData = \Model_Gethtml::getData($url);
        if ($htmlData === false) {
            return false;
        }
        $htmlData = mb_convert_encoding($htmlData, 'utf-8', 'shift_jis');

        try {
            //spit html item data
            preg_match_all('/<div id=\"wrapper\">(.*?)<div id=\"nlq_ft\">/s', $htmlData, $matches);

            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($matches[1][0], 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            //declare item information parameter
            $itemParams = array();

            //parse breadcrumb to get product categories and name
            $itemParams['category'] = "";
            foreach ($xpath->query('//div[@id="topicPath"]/span[@typeof="v:Breadcrumb"]') as $key => $node) {
                if ($key > 0) {
                    $itemParams['category'] .= $key == 1 ? trim($node->nodeValue) : " > " . trim($node->nodeValue);
                }

                //get brand
                if ($key == 1) {
                    $itemParams['brand'] = trim($node->nodeValue);
                }
            }

            //get product name
            $itemParams['item_name'] = $xpath->query('//h1/div[@id="item_name"]')->item(0)->nodeValue;

            //get product image picture
            $pic = $xpath->query("//ul[@id='Photothumb']/li[position()=1]/a/img/@target");
            $itemParams['img_url'] = $hostUrl . ltrim($pic->item(0)->value, '/');

            //get product price
            $prices = $xpath->query("//p[@class='taxin nocolor fz108']")->item(0)->nodeValue;
            preg_match_all('!\d+!', preg_replace('/[,]/', "", $prices), $arrayPrice);
            $plen = count($arrayPrice[0]);
            $itemParams['sale'] = "no";
            $itemParams['price'] = $itemParams['default_price'] = (int) $arrayPrice[0][$plen == 2 ? 1 : 0];

            //check product sale off
            if (isset($xpath->query('//table[@class="off"]')->item(0)->nodeValue)) {
                $itemParams['sale'] = "yes";

                //check product default price
                $defaultPrice = $xpath->query("//p[@class='former']/b");
                if (isset($defaultPrice->item(0)->nodeValue)) {
                    $defaultPrice = $defaultPrice->item(0)->nodeValue;
                    preg_match_all('!\d+!', preg_replace('/[,]/', "", $defaultPrice), $arrayDefaultPrice);
                    $itemParams['default_price'] = (int) ($arrayDefaultPrice[0][0] * $tax);
                }
                $itemParams['sale_price'] = $itemParams['price'];
            }
            $itemParams['stock'] = 0;

            //get color and size of product
            $subOptions = array();
            $size = array();
            $color = array();
            $colorKey = $sizeKey = 1;
            $options = array();
            $optionIndex = 0;

            //fetch and check stock html data
            $stockHtmlData = \Model_Gethtml::getData($stockURL);
            if ($stockHtmlData !== false) {
                $stockHtmlData = mb_convert_encoding($stockHtmlData, 'utf-8', 'shift_jis');

                $stockDom = new \DOMDocument();
                @$stockDom->loadHTML(mb_convert_encoding($stockHtmlData, 'HTML-ENTITIES', 'UTF-8'));
                $stockXpath = new \DOMXPath($stockDom);

                $stock = $stockXpath->query('//table[position()=4]/tr[position() mod 2 = 0]');
                if ($stock->length > 0) {
                    for ($i = 1; $i <= $stock->length; $i++) {
                        $strQuery = '//table[position()=4]/tr[position() mod 2 = 0][position()=' . $i . ']/td[1]';
                        $colorName = $stockXpath->query($strQuery)->item(0)->nodeValue;

                        $strQuery = '//table[position()=4]/tr[position() mod 2 = 0][position()=' . $i . ']/td[2]';
                        $sizeName = $stockXpath->query($strQuery)->item(0)->nodeValue;

                        $strQuery = '//table[position()=4]/tr[position() mod 2 = 0][position()=' . $i . ']/td[3]';
                        $price = (int) preg_replace('/[^+\d]/', '', $stockXpath->query($strQuery)->item(0)->nodeValue);

                        if (!array_search($colorName, $color)) {
                            $color[$colorKey++] = $colorName;
                            $sKey = 1;
                            $strQuery = "//dd/ul/li[" . ($colorKey - 1) . "]/a/div[@colorname]/img[1]/@target";
                            $optionImg = $hostUrl . ltrim($xpath->query($strQuery)->item(0)->value, '/');
                        }

                        if (!array_search($sizeName, $size)) {
                            $size[$sizeKey++] = $sizeName;
                        }

                        //set item opstion
                        $options[$optionIndex]['stock'] = 0;
                        $tdOrder = $itemParams['sale'] == "yes" ? 7 : 5;
                        $strQuery = '//table[4]/tr[position() mod 2 = 0][' . $i . ']/td[' . $tdOrder . ']/a';
                        if ($stockXpath->query($strQuery)->length == 2) {
                            $options[$optionIndex]['stock'] = 99;
                            $itemParams['stock'] = 99;
                        }
                        $options[$optionIndex]['option_id'] = $item . ($colorKey - 1) . (++$sKey - 1);
                        $options[$optionIndex]['option_img'] = $optionImg;
                        $options[$optionIndex]['sub_option_id'] = ($colorKey - 1) . ',' . ($sKey - 1);
                        $options[$optionIndex]['price'] = $price;
                        if ($itemParams['sale'] == "yes") {
                            $strQuery = '//table[4]/tr[position() mod 2 = 0][' . $i . ']/td[5]';
                            $stockSale = $stockXpath->query($strQuery)->item(0)->nodeValue;
                            $options[$optionIndex]['sale_price'] = (int) preg_replace('/[^+\d]/', '', $stockSale);
                        }
                        $options[$optionIndex]['option_value'] = array(
                            0 => array('name' => '色', 'value' => $colorName),
                            1 => array('name' => 'サイズ', 'value' => $sizeName)
                        );
                        $optionIndex++;
                    }
                }
            }
            if (!empty($color)) {
                $subOptions[0] = array('name' => '色', 'value' => $color);
            }

            if (!empty($size)) {
                $subOptions[1] = array('name' => 'サイズ', 'value' => $size);
            }

            if (!empty($subOptions)) {
                $itemParams['sub_options'] = $subOptions;
            }

            if (!empty($options)) {
                $itemParams['options'] = $options;
            }

            return $itemParams;
        } catch (\Exception $e) {
            return false;
        }
    }
}
