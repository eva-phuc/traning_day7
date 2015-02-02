<?php

namespace Iteminfo;

/**
 * Fashionwalker API
 */
class Model_Site_Fashionwalker extends \Model
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
        $optionName = array('色', 'サイズ');

        try {
            $htmlData = Model_Gethtml::getData($url);
            if ($htmlData === false) {
                return false;
            }

            $htmlData = mb_convert_encoding($htmlData, 'utf-8', 'shift_jis');
            preg_match_all('/<div id="main_menu">(.*?)<div id=\"recommender_pc311\">/s', $htmlData, $matches);
            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($matches[1][0], 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            $itemParams = array();
            // Product name
            $itemParams['item_name'] = $xpath->query('//h2[@id="txt_productname"]')->item(0)->nodeValue;

            // product image
            $itemParams['img_url'] = $xpath->query('//div[@class="slides_container"]/img/@src')->item(0)->nodeValue;

            // product price
            $itemParams['sale'] = "no";
            $prices = $xpath->query('//div[@id="txt_price"]')->item(0)->nodeValue;
            preg_match_all('!\d+!', preg_replace('/[,]/', "", $prices), $arrayPrice);
            $itemParams['default_price'] = (int) $arrayPrice[0][0];
            $itemParams['price'] = (int) $arrayPrice[0][0];
            if (isset($arrayPrice[0][2])) {
                $itemParams['sale'] = "yes";
                $itemParams['price'] = (int) $arrayPrice[0][2];
                $itemParams['sale_price'] = (int) $arrayPrice[0][2];
            }

            // product brand
            $brand = $xpath->query('//div[@id="brandlogo"]/a[@id="brandlogo_url"]/@href')->item(0)->nodeValue;
            $brand = explode("brand/", $brand);
            if (isset($brand[1])) {
                $itemParams['brand'] = trim($brand[1], '/');
            }

            // product category
            $itemParams['category'] = "";
            $category = $xpath->query('//div[@class="fw_shopnavi"]/a');
            foreach ($category as $index => $val) {
                if ($index > 0) {
                    $itemParams['category'] .= $val->nodeValue . " > ";
                }
            }
            $itemParams['category'] = rtrim($itemParams['category'], " > ");

            // product color, size and stock
            $itemParams['stock'] = 0;
            $color = array();
            $size = array();
            $cntColor = 0;
            $cntSize = 0;
            $cnt = 0;
            $tmpSize = 1;
            $itemParams['sub_options'][0] = array('name' => $optionName[0], 'value' => '');
            $itemParams['sub_options'][1] = array('name' => $optionName[1], 'value' => '');
            $itemId = $xpath->query('//form[@name="PCCartForm"]/input[@name="PC"]/@value')->item(0)->value;
            foreach ($xpath->query('//dt[@class="cart_color"]/ul/li[@class="color"]') as $node) {
                $data = explode(" ", $node->nodeValue);
                if (strpos($node->nodeValue, chr(194) . chr(160)) !== false) { // The extended ASCII codes
                    $data = explode(chr(194) . chr(160), $node->nodeValue);
                }
                $stock = 99;
                if (isset($data[0]) && !in_array($data[0], $color)) {
                    $color[++$cntColor] = $data[0];
                    $tmpSize = 1;
                }
                if (isset($data[1]) && !in_array($data[1], $size)) {
                    $size[++$cntSize] = $data[1];
                }
                if (isset($data[2])) {
                    if (strpos($data[2], 'SOLDOUT') !== false) {
                        $stock = 0;
                    }
                    preg_match('!\d+!', $data[2], $arrayStock);
                    if (!empty($arrayStock[0])) {
                        $stock = $arrayStock[0];
                    }
                }
                if ($stock > 0) {
                    $itemParams['stock'] = 99;
                }

                $itemParams['options'][$cnt]['stock'] = $stock;
                $itemParams['options'][$cnt]['option_id'] = $itemId . "10$cntColor" . "10$tmpSize";
                $optionImgNode = $xpath->query('//dt[@class="cart_color"]/ul/li[@class="photo"]/img/@src');
                $itemParams['options'][$cnt]['option_img'] = $optionImgNode->item($cnt)->nodeValue;
                $itemParams['options'][$cnt]['sub_option_id'] = $cntColor . "," . $tmpSize;
                $itemParams['options'][$cnt]['option_value'][0]['name'] = $optionName[0];
                $itemParams['options'][$cnt]['option_value'][0]['value'] = $data[0];
                $itemParams['options'][$cnt]['option_value'][1]['name'] = $optionName[1];
                $itemParams['options'][$cnt]['option_value'][1]['value'] = $data[1];
                $tmpSize++;
                $cnt++;
            }
            $itemParams['sub_options'][0]['value'] = $color;
            $itemParams['sub_options'][1]['value'] = $size;

            if (empty($itemParams['item_name']) || empty($itemParams['price'])) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return $itemParams;
    }
}
