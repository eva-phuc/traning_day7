<?php

namespace Iteminfo;

/**
 * Bellemaison API
 */
class Model_Site_Bellemaison extends \Model
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
        $tax = \Config::get('s.tax');

        //fetch and check html data
        $htmlData = \Model_Gethtml::getData($url);

        if ($htmlData === false) {
            return false;
        }
        $htmlData = mb_convert_encoding($htmlData, 'utf-8', 'shift_jis');
        $htmlData = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $htmlData);
        try {
            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($htmlData, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            //declare item information parameter
            $itemParams = array();

            $itemParams['category'] = "";
            $catQuery = $xpath->query('//p[@class="topicPath _dummy_kw_crawl_"]/span[@class="category"]/a');
            foreach ($catQuery as $key => $node) {
                $itemParams['category'] .= $key == 0 ? trim($node->nodeValue) : " > " . trim($node->nodeValue);

                //get brand
                if ($key == 0) {
                    $itemParams['brand'] = trim($node->nodeValue);
                }
            }

            //get product name
            $queryName = $xpath->query("//h1[@class='fn']");

            if ($queryName->length === 0) {
                return false;
            }
            $itemParams['item_name'] = $queryName->item(0)->nodeValue;

            //get product image picture
            $pic = $xpath->query("//li[@id='div0']/a/img/@src");
            $itemParams['img_url'] = str_replace(array("pic_s", "ps"), array("pic_b", "pb"), $pic->item(0)->value);

            //get product price
            $prices = $xpath->query("//div[@class='money']")->item(0)->nodeValue;
            preg_match_all('!\d+!', preg_replace('/[,]/', "", $prices), $arrayPrice);
            $plen = count($arrayPrice[0]);

            $itemParams['sale'] = "no";
            $price = (int) $arrayPrice[0][$plen == 4 ? 3 : ($plen == 2 ? 1 : 0)];
            $itemParams['price'] = $itemParams['default_price'] = $price;

            //check product sale off
            $sale = $xpath->query("//p[@id='sale']");
            if (isset($sale->item(0)->nodeValue)) {
                $itemParams['sale'] = "yes";

                //check product default price
                $strQuery = "//div[@class='border-tdot fc-gray03 mt10 pt10']/div[@class='text12 mt5']";
                $defaultPrice = $xpath->query($strQuery);
                if (isset($defaultPrice->item(0)->nodeValue)) {
                    $defaultPrice = preg_replace('/[,]/', "", $defaultPrice->item(0)->nodeValue);
                    preg_match_all('!\d+!', $defaultPrice, $arrPrice);
                    $itemParams['default_price'] = (int) ($arrPrice[0][count($arrPrice[0]) - 1] * $tax);
                }
                $itemParams['sale_price'] = (int) $arrayPrice[0][$plen == 4 ? 3 : ($plen == 2 ? 1 : 0)];
            }
            $itemParams['stock'] = 99;

            //get color and size of product
            $subOptions = array();
            $size = array();
            $color = array();
            $optionId = $xpath->query('//div[@class="items"]/@id')->item(0)->value;

            //parse color value
            $colorIndex = 1;
            foreach ($xpath->query("//select[@name='IRO_SKBT_SEQ']/option") as $colorKey => $colorNode) {
                if ($colorNode->getAttribute('value') > 0 && $colorNode->nodeValue != "") {
                    $color[$colorIndex++] = $colorNode->nodeValue;
                }
            }
            if (!empty($color)) {
                $subOptions[0] = array('name' => '色', 'value' => $color);
            }

            //parse size value
            $sizeIndex = 1;
            foreach ($xpath->query("//select[@name='SIZE1_SKBT_SEQ']/option") as $sizeKey => $sizeNode) {
                if ($sizeNode->getAttribute('value') > 0 && $sizeNode->nodeValue != "") {
                    $size[$sizeIndex++] = $sizeNode->nodeValue;
                }
            }
            if (!empty($size)) {
                $subOptions[1] = array('name' => 'サイズ', 'value' => $size);
            }

            if (!empty($subOptions)) {
                $itemParams['sub_options'] = $subOptions;
            }
            
            $options = array();
            $optionIndex = 0;
            foreach ($color as $ckey => $cval) {
                foreach ($size as $skey => $sval) {
                    $options[$optionIndex]['stock'] = 99;
                    $options[$optionIndex]['option_id'] = $optionId . $ckey . $skey;
                    $options[$optionIndex]['option_img'] = $itemParams['img_url'];
                    $options[$optionIndex]['sub_option_id'] = $ckey . ',' . $skey;
                    $options[$optionIndex]['option_value'] = array(
                        0 => array('name' => '色', 'value' => $cval),
                        1 => array('name' => 'サイズ', 'value' => $sval)
                    );
                    $optionIndex++;
                }
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
