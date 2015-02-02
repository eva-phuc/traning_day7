<?php

namespace Iteminfo;

/**
 * Locondo API
 */
class Model_Site_Locondo extends \Model
{

    /**
     * Product data analysis
     *
     * @access public
     * @param string $url
     * @return mixed
     */
    public static function getData($url)
    {
        \Config::load('sites', 's');
        $tax = \Config::get('s.tax');

        try {
            $optionName = array('色', 'サイズ');

            $htmlData = Model_Gethtml::getData($url);
            if ($htmlData === false) {
                return false;
            }

            $dom = new \DOMDocument();
            @$dom->loadHTML($htmlData);
            $xpath = new \DOMXPath($dom);

            $itemParams = array();
            $itemParams['img_url'] = $xpath->query('//p[@class="product-image"]/a/img/@src')->item(0)->nodeValue;

            // product name
            $itemParams['item_name'] = $xpath->query('//div[@id="product_detail"]/h2')->item(0)->nodeValue;

            // product price
            $regularPrice = $xpath->query('//p[@class="regular_price"]/span[@class="taxin"]');
            $oldPrice = $xpath->query('//p[@class="old_price"]/span[@class="price"]');
            $specialPrice = $xpath->query('//p[@class="special_price"]/span[@class="taxin"]');
            if (isset($regularPrice->item(0)->nodeValue)) {
                preg_match_all('!\d+!', preg_replace('/[,]/', "", $regularPrice->item(0)->nodeValue), $arrayPrice);
                $itemParams['price'] = (int) $arrayPrice[0][0];
                $itemParams['default_price'] = (int) $arrayPrice[0][0];
                $itemParams['sale'] = 'no';
            }
            if (isset($oldPrice->item(0)->nodeValue)) {
                preg_match_all('!\d+!', preg_replace('/[,]/', "", $oldPrice->item(0)->nodeValue), $arrayPrice);
                $itemParams['default_price'] = (int) $arrayPrice[0][0] * $tax; // include tax
            }
            if (isset($specialPrice->item(0)->nodeValue)) {
                preg_match_all('!\d+!', preg_replace('/[,]/', "", $specialPrice->item(0)->nodeValue), $arrayPrice);
                $itemParams['price'] = (int) $arrayPrice[0][0];
                $itemParams['sale_price'] = (int) $arrayPrice[0][0];
                $itemParams['sale'] = 'yes';
            }

            // product brand
            $brandNode = $xpath->query('//dl[@class="related_brand"]/dd/a');
            $itemParams['brand'] = substr(strrchr($brandNode->item(0)->getAttribute('href'), '/'), 1);

            // product stock
            $itemParams['stock'] = 0;

            // product color and size
            $itemParams['sub_options'][0]['name'] = $optionName[0];
            $itemParams['sub_options'][1]['name'] = $optionName[1];
            $str = explode("（", $itemParams['item_name']);
            if (isset($str[count($str) - 1])) {
                $str = explode("）", $str[count($str) - 1]);
                $colorName = $str[0];
                $itemParams['sub_options'][0]['value'][1] = $colorName;
            }
            $nodeSize = $xpath->query('//div[@class="shopping_cantrol"]/table/tr/td[@class="size"]');
            $itemId = $xpath->query('//input[@id="commodityCode"]/@value')->item(0)->nodeValue;
            foreach ($nodeSize as $index => $node) {
                $nodeStock = "table/tr[".($index+2)."]/td/span[@class='outofstock']";
                $stock = 0;
                if (!isset($xpath->query("//div[@class='shopping_cantrol']/$nodeStock")->item(0)->nodeValue)) {
                    $stock = 99;
                    $itemParams['stock'] = 99;
                }
                $itemParams['options'][$index]['stock'] = $stock;
                $sizeName = $node->nodeValue;
                $itemParams['sub_options'][1]['value'][$index + 1] = $sizeName;
                $itemParams['options'][$index]['option_id'] = $itemId . $index;
                $itemParams['options'][$index]['option_img'] = $itemParams['img_url'];
                $itemParams['options'][$index]['sub_option_id'] = "1," . ($index + 1);
                $itemParams['options'][$index]['option_value'][0]['name'] = $optionName[0];
                $itemParams['options'][$index]['option_value'][0]['value'] = $colorName;
                $itemParams['options'][$index]['option_value'][1]['name'] = $optionName[1];
                $itemParams['options'][$index]['option_value'][1]['value'] = $sizeName;
            }

            $node = $xpath->query('//div[@class="breadcrumbs clearfix"]/ul/li/a');
            $category = '';
            foreach ($node as $v) {
                if (preg_match('/category/', $v->getAttribute('href'))) {
                    $category .= $v->nodeValue . " > ";
                }
            }
            $itemParams['category'] = rtrim($category, " > ");
        } catch (\Exception $e) {
            return false;
        }

        return $itemParams;
    }
}
