<?php

namespace Iteminfo;

/**
 * Magaseek API
 */
class Model_Site_Magaseek extends \Model
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
        $hostUrl = \Config::get('s.urls.magaseek');
        //fetch and check html data
        $htmlData = \Model_Gethtml::getData($url);
        if ($htmlData === false) {
            return false;
        }

        try {
            //spit html item data
            preg_match_all('/<div id=\"container\">(.*?)<div id=\"pagetop\">/s', $htmlData, $matches);

            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($matches[1][0], 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            //declare item information parameter
            $itemParams = array();

            //parse breadcrumb to get product categories and name
            $itemParams['category'] = "";
            foreach ($xpath->query('//div[@id="pankuzu"]/ul/li[not(@class)]') as $key => $node) {
                if ($key > 0) {
                    $itemParams['category'] .= $key == 1 ? trim($node->nodeValue) : " > " . trim($node->nodeValue);
                }
            }

            $itemParams['brand'] = trim($xpath->query('//p[@id="brand_name"]')->item(0)->nodeValue);

            //get product name
            $itemParams['item_name'] = $xpath->query('//h1[@class="item"]')->item(0)->nodeValue;

            //get product default image
            $itemParams['img_url'] = $xpath->query("//ul[@id='colorlist']/li[position()=1]/a/@href")->item(0)->value;

            //get product price
            $itemParams['sale'] = "no";

            $defaultPrice = $xpath->query('//p[@class="normal-price"]');
            if (isset($defaultPrice->item(0)->nodeValue)) {
                $itemParams['default_price'] = (int) preg_replace('/[^+\d]/', '', $defaultPrice->item(0)->nodeValue);
            }

            $salePrice = $xpath->query('//p[@class="special-price"]');
            if (isset($salePrice->item(0)->nodeValue)) {
                $itemParams['sale'] = 'yes';
                $price = (int) preg_replace('/[^+\d]/', '', $salePrice->item(0)->nodeValue);
                $itemParams['price'] = $itemParams['sale_price'] = $price;
            }

            if ($itemParams['sale'] == 'no') {
                $price = $xpath->query('//p[@class="price"]')->item(0)->nodeValue;
                $price = (int) preg_replace('/[^+\d]/', '', $price);
                $itemParams['price'] = $itemParams['default_price'] = $price;
            }
            $itemParams['stock'] = 0;

            //get image, color and size of product
            $subOptions = array();
            $size = array();
            $color = array();
            $options = array();
            $optionIndex = 0;

            //check in/out stock
            $stockURL = $hostUrl . ltrim($xpath->query('//ul[@class="stock-button"]/li/a/@href')->item(0)->value, '/');
            $htmlStockData = \Model_Gethtml::getData($stockURL);

            if ($htmlStockData !== false) {
                //spit html item data
                preg_match_all('/<div class=\"tablebox\">(.*?)<\/div>/s', $htmlStockData, $matches);
                $domStock = new \DOMDocument();
                @$domStock->loadHTML(mb_convert_encoding($matches[1][0], 'HTML-ENTITIES', 'UTF-8'));
                $xpathStock = new \DOMXPath($domStock);

                $totalColor = $xpath->query("//ul[@id='colorlist']/li")->length;
                $totalSize = $xpath->query("//ul[@id='sizelist']/li")->length;
                foreach ($xpathStock->query('//table/tr') as $stockKey => $stockNode) {
                    //get all size of product item
                    if ($stockKey == 0) {
                        $key = 1;
                        for ($i = 0; $i < ceil($totalSize / 4); $i++) {
                            $strQuery = '//table/tr[' . ($i * ($totalColor + 1) + 1) . ']/th';
                            foreach ($xpathStock->query($strQuery) as $sizeKey => $sizeNode) {
                                if ($sizeKey > 1 && $sizeNode->nodeValue != "") {
                                    $size[$key++] = $sizeNode->nodeValue;
                                }
                            }
                        }
                    }

                    //parse color and set item options
                    if ($stockKey > 0 && $stockKey <= $totalColor) {
                        $colorItem = $xpathStock->query('//table/tr[' . ($stockKey + 1) . ']/td[1]/img');
                        $color[$stockKey] = $colorItem->item(0)->getAttribute('alt');

                        //set item options
                        for ($i = 1; $i <= count($size); $i++) {
                            $stockNode = (1 == ceil($i / 4) ? $stockKey + 1 : $stockKey + ceil($i / 4) + $totalColor);
                            $strQuery = '//table/tr[' . $stockNode . ']/td[' . (($i - 1) % 4 + 3) . ']/p';
                            $stockStatus = $xpathStock->query($strQuery);
                            $stockLength = $stockStatus->length;
                            if ($stockLength > 0) {
                                $stockValue = $stockStatus->item(0)->getAttribute('class');
                            }
                            $options[$optionIndex]['stock'] = 99;

                            if ($stockLength == 0) {
                                $options[$optionIndex]['stock'] = 0;
                            }

                            if ($stockLength > 0 && $stockValue == 'stock') {
                                $options[$optionIndex]['stock'] = 0;
                                if ($stockStatus->item(0)->nodeValue != '在庫なし') {
                                    $options[$optionIndex]['stock'] = 99;
                                    $itemParams['stock'] = 99;
                                }
                            }

                            if ($stockLength > 0 && $stockValue == 'stock-notice') {
                                $value = (int) preg_replace('/[^+\d]/', '', $stockStatus->item(0)->nodeValue);
                                $options[$optionIndex]['stock'] = $value;
                                $itemParams['stock'] = 99;
                            }

                            $id = preg_replace('/[^+\d]/', '', basename($colorItem->item(0)->getAttribute('src')));
                            $options[$optionIndex]['option_id'] = $id . $stockKey . $i;
                            $options[$optionIndex]['option_img'] = $colorItem->item(0)->getAttribute('src');
                            $options[$optionIndex]['sub_option_id'] = $stockKey . ',' . $i;
                            $options[$optionIndex]['option_value'] = array(
                                0 => array('name' => '色', 'value' => $colorItem->item(0)->getAttribute('alt')),
                                1 => array('name' => 'サイズ', 'value' => $size[$i])
                            );
                            $optionIndex++;
                        }
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
