<?php

namespace Iteminfo;

/**
 * Stylife API
 */
class Model_Site_Stylife extends \Model
{

    /**
     * Pasre item data from product url
     * 
     * @param string $url
     * @return mixed
     */
    public static function getData($url)
    {
        //fetch and check html data
        $htmlData = \Model_Gethtml::getData($url);
        if ($htmlData === false) {
            return false;
        }

        $htmlData = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $htmlData);

        try {
            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($htmlData, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            //declare item information parameter
            $itemParams = array();

            //parse breadcrumb to get product categories and name
            $itemParams['category'] = "";
            $categoryQuery = $xpath->query('//div[@id="breadcrumbsField"]/h1/ul/li');
            foreach ($categoryQuery as $key => $node) {
                if ($key > 0 && $key < $categoryQuery->length - 1) {
                    $itemParams['category'] .= $key == 1 ? trim($node->nodeValue) : " > " . trim($node->nodeValue);
                }
            }
            
            $itemParams['brand'] = trim($xpath->query('//p[@class="itempage_brandname"]/a')->item(0)->nodeValue);

            //get product name
            $itemParams['item_name'] = $xpath->query('//p[@class="itempage_itemname"]')->item(0)->nodeValue;

            //get product default image
            $strQuery = "//ul[@class='tlb_clearfix heightLineParent']/li[position()=1]/a/img/@src";
            $itemParams['img_url'] = preg_replace('/&[a-z]=(.*)/i', '', $xpath->query($strQuery)->item(0)->value);

            //get product price
            $itemParams['sale'] = "no";
            $itemParams['price'] = $itemParams['default_price'] = 0;
            if ($xpath->query('//div[@id="detailPrice"]/div[@class="price"]')->length > 0) {
                $strQuery = '//div[@id="detailPrice"]/div/div/div/p/span[@class="proper-price"]';

                if ($xpath->query('//div[@class="price"]/div/div/p/span[@class="proper-price"]')->length > 0) {
                    $strQuery = '//div[@class="price"]/div/div/p/span[@class="proper-price"]';
                }

                if (isset($xpath->query($strQuery)->item(0)->nodeValue)) {
                    $defaultPrice = $xpath->query($strQuery)->item(0)->nodeValue;
                    $itemParams['default_price'] = (int) preg_replace('/[^+\d]/', '', $defaultPrice);
                }

                $salePrice = $xpath->query('//div[@id="detailPrice"]/div/div/div/span[@class="discount-price"]');

                if ($xpath->query('//div[@class="price"]//div/div/span[@class="discount-price"]')->length > 0) {
                    $salePrice = $xpath->query('//div[@class="price"]//div/div/span[@class="discount-price"]');
                }
                
                if (isset($salePrice->item(0)->nodeValue)) {
                    $itemParams['sale'] = 'yes';                    
                    $price = (int) preg_replace('/[^+\d]/', '', $salePrice->item(0)->nodeValue);
                    $itemParams['price'] = $itemParams['sale_price'] = $price;
                }                
            }

            //sale off per each product size
            $opstionsPrice = array();
            $strNode = '//div[@id="detailCartField"]/table[@id="detailListTbl"]/tbody';
            $strQuery = $strNode . '/tr[@class="tlb_price"]';
            $len = $xpath->query($strQuery)->length;
            for ($i = 1; $i <= $len; $i++) {
                $strQuery = "$strNode/tr[@class='tlb_price'][$i]/td/p[@class='price']/span[@class='proper-price']";
                $defaultPrice = $xpath->query($strQuery);
                $strQuery = "$strNode/tr[@class='tlb_price'][$i]/td/p[@class='price']/span[@class='discount-price']";
                $salePrice = $xpath->query($strQuery);

                if (isset($defaultPrice->item(0)->nodeValue)) {
                    $price = (int) preg_replace('/[^+\d]/', '', $defaultPrice->item(0)->nodeValue);
                    $opstionsPrice[$i]['price'] = $price;
                    if ($price > $itemParams['default_price']) {
                        $itemParams['default_price'] = $price;
                    }
                }

                if (isset($salePrice->item(0)->nodeValue)) {
                    $itemParams['sale'] = 'yes';
                    $opstionsPrice[$i]['sale'] = 'yes';
                    preg_match_all('!\d+!', preg_replace('/[,]/', "", $salePrice->item(0)->nodeValue), $arrayPrice);
                    $opstionsPrice[$i]['sale_price'] = (int) $arrayPrice[0][1];
                    if ((int) $arrayPrice[0][1] > $itemParams['sale_price']) {
                        $itemParams['price'] = $itemParams['sale_price'] = (int) $arrayPrice[0][1];
                    }
                }
            }

            if ($itemParams['sale'] == 'no') {
                $strQuery = '//div[@id="detailPrice"]/span[@class="price"]';
                $price = (int) preg_replace('/[^+\d]/', '', $xpath->query($strQuery)->item(0)->nodeValue);
                $itemParams['price'] = $itemParams['default_price'] = $price;
            }
            $itemParams['stock'] = 0;

            //get image, color and size of product
            $subOptions = array();
            $size = array();
            $color = array();
            $options = array();
            $optionIndex = 0;

            //parse color value
            for ($j = 1; $j <= $xpath->query("//ul[@class='tlb_clearfix heightLineParent']/li")->length; $j++) {
                $colorItem = $xpath->query("//ul[@class='tlb_clearfix heightLineParent']/li[$j]/a/img/@label");

                if (ord($colorItem->item(0)->value) != 194) {
                    $strQuery = "//ul[@class='tlb_clearfix heightLineParent']/li[" . $j . "]/a/img/@src";
                    $optionImg = preg_replace('/&[a-z]=(.*)/i', '', $xpath->query($strQuery)->item(0)->value);

                    if (!array_search($colorItem->item(0)->value, $color)) {
                        $color[$j] = $colorItem->item(0)->value;

                        //parse size value
                        $strQuery = $strNode . '/tr[not(@class="tlb_price")]';
                        $len = $xpath->query($strQuery)->length;
                        for ($i = 1; $i <= $len; $i++) {
                            $strQuery = "$strNode/tr[not(@class='tlb_price')][$i]/td[@class='color']";
                            $sizeItem = $xpath->query($strQuery)->item(0)->nodeValue;

                            if (strpos($sizeItem, chr(194)) !== false) {
                                $sizeItem = explode(chr(194), $sizeItem);
                            }

                            if (is_string($sizeItem) && strpos($sizeItem, '/') !== false) {
                                $sizeItem = explode('/', $sizeItem);
                            }

                            if ($sizeItem[0] == "") {
                                $strQuery = "//div[@id='detailSize']/table/tr[2]/td[1]";
                                $sizeItem[0] = $xpath->query($strQuery)->item(0)->nodeValue;
                            }

                            if (strpos($sizeItem[1], $colorItem->item(0)->value) !== false) {
                                if (!array_search($sizeItem[0], $size)) {
                                    $size[$i] = $sizeItem[0];
                                }

                                //set item opstion
                                $strQuery = "$strNode/tr[not(@class='tlb_price')][$i]/td[@class='stock']/span/@class";
                                $stock = $xpath->query($strQuery);
                                $options[$optionIndex]['stock'] = 0;
                                if ($stock->length > 0 && $stock->item(0)->value != "out_of_stock") {
                                    $options[$optionIndex]['stock'] = 99;
                                    $itemParams['stock'] = 99;
                                }

                                if (isset($opstionsPrice[$i])) {
                                    $options[$optionIndex]['price'] = $opstionsPrice[$i]['price'];
                                    $options[$optionIndex]['sale_price'] = $opstionsPrice[$i]['sale_price'];
                                }
                                $options[$optionIndex]['option_id'] = preg_replace('/[^+\d]/', '', $url) . $j . $i;
                                $options[$optionIndex]['option_img'] = $optionImg;
                                $options[$optionIndex]['sub_option_id'] = $j . ',' . array_search($sizeItem[0], $size);
                                $options[$optionIndex]['option_value'] = array(
                                    0 => array('name' => '色', 'value' => $colorItem->item(0)->value),
                                    1 => array('name' => 'サイズ', 'value' => $sizeItem[0])
                                );
                                $optionIndex++;
                            }
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
