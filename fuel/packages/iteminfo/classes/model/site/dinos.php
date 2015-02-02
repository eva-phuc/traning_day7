<?php

namespace Iteminfo;

/**
 * Dinos API
 */
class Model_Site_Dinos extends \Model
{

    /**
     * Product data analysis
     * 
     * @access public
     * @param type $url
     * @return mixed
     */
    public static function getData($url)
    {
        \Config::load('sites', 's');
        $hostUrl = \Config::get('s.urls.dinos');
        $tax = \Config::get('s.tax');
        $optionName = array('色', 'サイズ');

        try {
            $htmlData = Model_Gethtml::getData($url);
            if ($htmlData === false) {
                return false;
            }

            $htmlData = mb_convert_encoding($htmlData, 'utf-8', 'shift_jis');
            preg_match_all('/<div id=\"ht5_contents\">(.*?)<div id=\"ht5_footer\">/s', $htmlData, $matches);
            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($matches[1][0], 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            $itemParams = array();
            // Product name
            if (isset($xpath->query('//h1[@itemprop="name"]')->item(0)->nodeValue)) {
                $itemParams['item_name'] = $xpath->query('//h1[@itemprop="name"]')->item(0)->nodeValue;
            }

            // product image
            if (isset($xpath->query('//div[@id="dpvThumb"]/ul/li/@data-dpv-main-url')->item(0)->nodeValue)) {
                $imgNode = $xpath->query('//div[@id="dpvThumb"]/ul/li/@data-dpv-main-url');
                $itemParams['img_url'] = "http:" . $imgNode->item(0)->nodeValue;
            }

            // product price
            $itemParams['sale'] = "no";
            $prices = $xpath->query('//p[@class="priceA"]/span[@class="pLarge"]')->item(0)->nodeValue;
            preg_match_all('!\d+!', preg_replace('/[,]/', "", $prices), $arrayPrice);
            $price = isset($arrayPrice[0][1]) ? (int) $arrayPrice[0][1] : (int) $arrayPrice[0][0];
            $itemParams['default_price'] = $price;
            $itemParams['price'] = $price;
            if (isset($xpath->query('//p[@class="priceB"]')->item(0)->nodeValue)) {
                $salePrices = $xpath->query('//p[@class="priceB"]')->item(0)->nodeValue;
                preg_match_all('!\d+!', preg_replace('/[,]/', "", $salePrices), $arrayPrice);
                $itemParams['default_price'] = floor((int) $arrayPrice[0][1] * $tax);
                $itemParams['sale_price'] = $itemParams['price'];
                $itemParams['sale'] = "yes";
            }

            // product category
            $itemParams['category'] = "";
            $category = $xpath->query('//div[@id="mod_breadcrumb"]/div[@class="list"]/nav/ol/li/a');
            foreach ($category as $index => $val) {
                if ($index > 0) {
                    $itemParams['category'] .= $val->nodeValue . " > ";
                }
            }
            $itemParams['category'] = rtrim($itemParams['category'], " > ");

            // product color, size and stock
            $itemParams['stock'] = 0;
            $isMulti = false; //check multi product
            $itemList = array();
            $priceList = array();
            $size = array();
            $color = array();
            $cntColor = 0;
            $cntSize = 0;
            $cnt = 0;
            $priceNode = $xpath->query('//table[@class="mod_defTable itemD_table"][position()=1]/tbody/tr');
            foreach ($priceNode as $key => $val) {
                $item = '//table[@class="mod_defTable itemD_table"][1]/tbody/tr[' . ($key + 1) . ']/td/span';
                $itemLine = $xpath->query($item);
                if ($priceNode->length > 1) {
                    $isMulti = true;
                    $size[$key + 1] = $itemLine->item(1)->nodeValue;
                    preg_match_all('!\d+!', preg_replace('/[,]/', "", $itemLine->item(2)->nodeValue), $arrayPrice);
                    $priceList[] = $arrayPrice[0][0];
                }
                $itemId = $itemLine->item(0)->nodeValue;
                $itemId = str_replace("-", "", substr(strstr($itemId, "-"), 1));
                $itemList[] = $itemId;
            }
            $options = array();
            foreach ($itemList as $key => $val) {
                $stockUrl = "defaultMall/sitemap/CSfStockList.jsp?CATNO=900&MOSHBG=" . $val . "&DATEFLG=1&BTNFLG=1";
                $htmlStockData = \Model_Gethtml::getData($hostUrl . $stockUrl);
                if ($htmlStockData !== false) {
                    $htmlStockData = mb_convert_encoding($htmlStockData, 'utf-8', 'shift_jis');
                    $domStock = new \DOMDocument();
                    @$domStock->loadHTML(mb_convert_encoding($htmlStockData, 'HTML-ENTITIES', 'UTF-8'));
                    $xpathStock = new \DOMXPath($domStock);
                    $length = $xpathStock->query('//table[@class="borderTbl mb20"]/tbody/tr')->length;
                    for ($i = 0; $i < $length; $i++) {
                        $stock = 0;
                        $node = $xpathStock->query('//table[@class="borderTbl mb20"]/tbody/tr[' . ($i + 1) . ' ]/td');
                        $tmpColor = trim($node->item(0)->nodeValue);
                        $tmpSize = trim($node->item(1)->nodeValue);
                        if (!in_array($tmpColor, $color) && !empty($tmpColor)) {
                            $color[++$cntColor] = $tmpColor;
                        }
                        if (!in_array($tmpSize, $size) && !empty($tmpSize)) {
                            $size[++$cntSize] = $tmpSize;
                        }
                        if ($isMulti) {
                            $tmpSize = $size[$key + 1];
                        }
                        $stockNode = '//table[@class="borderTbl mb20"]/tbody/tr[' . ($i + 1) . ' ]/td/p';
                        if (isset($xpathStock->query($stockNode)->item(0)->nodeValue)) {
                            $stock = 99;
                        }
                        if ($stock > 0) {
                            $itemParams['stock'] = 99;
                        }
                        if (empty($tmpColor) && empty($tmpSize)) {
                            continue;
                        }
                        $options[$cnt]['stock'] = $stock;
                        $subColor = $subSize = "";
                        $subId = array();
                        $optionValue = array();
                        if (!empty($color)) {
                            $optionValue[] = array('name' => $optionName[0], 'value' => $tmpColor);
                            $tmpKey = array_search($tmpColor, $color);
                            $subColor = "10" . $tmpKey;
                            $subId[] = $tmpKey;
                        }
                        if (!empty($size)) {
                            $optionValue[] = array('name' => $optionName[1], 'value' => $tmpSize);
                            $subSize = "10" . array_search($tmpSize, $size);
                            $subId[] = array_search($tmpSize, $size);
                        }
                        if ($isMulti) {
                            $options[$cnt]['price'] = $priceList[$key];
                        }
                        $options[$cnt]['option_id'] = $val . $subColor . $subSize;
                        if (!empty($subId)) {
                            $options[$cnt]['sub_option_id'] = implode(",", $subId);
                        }
                        if (!empty($optionValue)) {
                            $options[$cnt]['option_value'] = $optionValue;
                        }
                        $cnt++;
                    }
                }
            }
            if (!empty($color)) {
                $itemParams['sub_options'][] = array('name' => $optionName[0], 'value' => $color);
            }
            if (!empty($size)) {
                $itemParams['sub_options'][] = array('name' => $optionName[1], 'value' => $size);
            }
            if (!empty($options)) {
                $itemParams['options'] = $options;
            }

            if (empty($itemParams['item_name']) || empty($itemParams['price']) || !isset($itemParams['stock'])) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return $itemParams;
    }
}
