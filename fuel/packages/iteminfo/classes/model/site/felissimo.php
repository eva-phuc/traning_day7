<?php

namespace Iteminfo;

/** 
 * Felissimo API
 */
class Model_Site_Felissimo extends \Model
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
        $arrayUrl = parse_url($url);
        $hostUrl = $arrayUrl['scheme'] . "://" . $arrayUrl['host'];
        $optionName = array('色', 'サイズ', 'タイプ');

        try {
            $htmlData = Model_Gethtml::getData($url);
            if ($htmlData === false) {
                return false;
            }

            preg_match_all('/<body(.*?)<\/body>/s', $htmlData, $matches);
            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($htmlData, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            $itemParams = array();
            // product name
            $concat = 'contains(concat(" ",normalize-space(@class)," ")';
            $itemName = '';
            if (isset($xpath->query('//div[' . $concat . ',"exp-dtl_")]/*/h2')->item(0)->nodeValue)) {
                $itemName = $xpath->query('//div[' . $concat . ',"exp-dtl_")]/*/h2')->item(0)->nodeValue;
            } elseif (isset($xpath->query('//div[' . $concat . ',"dtl-sel_")]/h2')->item(0)->nodeValue)) {
                $itemName = $xpath->query('//div[' . $concat . ',"dtl-sel_")]/h2')->item(0)->nodeValue;
            } elseif (isset($xpath->query('//h1[@class="nameItemSub"]')->item(0)->nodeValue)) {
                $itemName = $xpath->query('//h1[@class="nameItemSub"]')->item(0)->nodeValue;
            }
            $itemParams['item_name'] = $itemName;

            // product image
            $itemImg = '';
            $imgNode = '//div[@class="select-main-p"]/p[@id="large-wrap"]/img/@src';
            if (isset($xpath->query($imgNode)->item(0)->nodeValue)) {
                $itemImg = $xpath->query($imgNode)->item(0)->nodeValue;
            } elseif (isset($xpath->query('//div[@class="slide"]/img/@src')->item(0)->nodeValue)) {
                $itemImg = $xpath->query('//div[@class="slide"]/img/@src')->item(0)->nodeValue;
            }
            if (!empty($itemImg) && strpos($itemImg, $hostUrl) === false) {
                $itemImg = $hostUrl . $itemImg;
            }
            $itemParams['img_url'] = $itemImg;

            // product price
            $defaultPrice = $salePrice = $regularPrice = '';
            $expDtl = '//div[contains(@class,"exp-dtl_")]';
            $dtlSel = '//div[contains(@class,"dtl-sel_")]';
            $itmDtlTbl = '//table[@class="itmDtlTbl"]';

            if (isset($xpath->query($expDtl . '/*/*/span[@class="price02"]')->item(0)->nodeValue)) {
                $defaultPrice = $xpath->query($expDtl . '/*/*/span[@class="price02"]')->item(0)->nodeValue;
            } elseif (isset($xpath->query($expDtl . '/*/*/*/span[@class="price02"]')->item(0)->nodeValue)) {
                $defaultPrice = $xpath->query($expDtl . '/*/*/*/span[@class="price02"]')->item(0)->nodeValue;
            } elseif (isset($xpath->query($dtlSel . '/*/span[@class="price02"]')->item(0)->nodeValue)) {
                $defaultPrice = $xpath->query($dtlSel . '/*/span[@class="price02"]')->item(0)->nodeValue;
            } elseif (isset($xpath->query($itmDtlTbl . '/tr/td/p[@class="inPrice"]')->item(0)->nodeValue)) {
                $defaultPrice = $xpath->query($itmDtlTbl . '/tr/td/p[@class="inPrice"]')->item(0)->nodeValue;
            }
            if (isset($xpath->query($expDtl . '/*/*/span[@class="discount02"]')->item(0)->nodeValue)) {
                $salePrice = $xpath->query($expDtl . '/*/*/span[@class="discount02"]')->item(0)->nodeValue;
            } elseif (isset($xpath->query($expDtl . '/*/*/*/span[@class="discount02"]')->item(0)->nodeValue)) {
                $salePrice = $xpath->query($expDtl . '/*/*/*/span[@class="discount02"]')->item(0)->nodeValue;
            } elseif (isset($xpath->query($dtlSel . '/*/span[@class="discount02"]')->item(0)->nodeValue)) {
                $defaultPrice = $xpath->query($dtlSel . '/*/span[@class="discount02"]')->item(0)->nodeValue;
            }
            if (isset($xpath->query($expDtl . '/*/*/span[@class="regular02"]')->item(0)->nodeValue)) {
                $regularPrice = $xpath->query($expDtl . '/*/*/span[@class="regular02"]')->item(0)->nodeValue;
            } elseif (isset($xpath->query($expDtl . '/*/*/*/span[@class="regular02"]')->item(0)->nodeValue)) {
                $regularPrice = $xpath->query($expDtl . '/*/*/*/span[@class="regular02"]')->item(0)->nodeValue;
            } elseif (isset($xpath->query($dtlSel . '/*/span[@class="regular02"]')->item(0)->nodeValue)) {
                $defaultPrice = $xpath->query($dtlSel . '/*/span[@class="regular02"]')->item(0)->nodeValue;
            }
            $stock = 0;
            $shosurNode = $xpath->query('//select[@name="shosur"]/option');
            $quantityNode = $xpath->query('//select[@name="quantity"]/option');
            if (isset($shosurNode->item(0)->nodeValue) || isset($quantityNode->item(0)->nodeValue)) {
                $stock = 99;
            }
            $itemParams['sale'] = "no";
            $itemParams['stock'] = $stock;
            if (!empty($defaultPrice)) {
                preg_match_all('!\d+!', preg_replace('/[,]/', "", $defaultPrice), $arrayPrice);
                $itemParams['price'] = (int) $arrayPrice[0][2];
                $itemParams['default_price'] = (int) $arrayPrice[0][2];
            }
            if (!empty($regularPrice)) {
                preg_match_all('!\d+!', preg_replace('/[,]/', "", $regularPrice), $arrayPrice);
                $itemParams['default_price'] = (int) $arrayPrice[0][0];
            }
            if (!empty($salePrice)) {
                preg_match_all('!\d+!', preg_replace('/[,]/', "", $salePrice), $arrayPrice);
                $itemParams['sale'] = "yes";
                $itemParams['price'] = (int) $arrayPrice[0][0];
                $itemParams['sale_price'] = (int) $arrayPrice[0][0];
            }

            // product brand
            // product category
            $category = array();
            if (isset($xpath->query('//h1[not(contains(@id,"logo"))]/a')->item(0)->nodeValue)) {
                $category = $xpath->query('//h1/a');
            } elseif (isset($xpath->query('//p[@id="pan"]/a')->item(0)->nodeValue)) {
                $category = $xpath->query('//p[@id="pan"]/a');
            } elseif (isset($xpath->query('//ol[@id="topicPath"]/li[not(@id="topPage")]/a')->item(0)->nodeValue)) {
                $category = $xpath->query('//ol[@id="topicPath"]/li/a');
            }
            $itemParams['category'] = "";
            foreach ($category as $index => $val) {
                if ($index == 0) {
                    continue;
                }
                $itemParams['category'] .= $val->nodeValue . " > ";
                if (($index == 1 || $index == 2 ) && $val->getAttribute('href') !== $hostUrl) {
                    $itemParams['brand'] = $val->nodeValue;
                }
            }
            $itemParams['category'] = rtrim($itemParams['category'], " > ");

            // product size
            $cntSize = $cntColor = $cntType = 0;
            $size = $color = $type = array();
            $sizeNode = $xpath->query('//select[@name="size"][@class="size"]/option');
            $colorNode = $xpath->query('//select[@name="size"][@class="type"]/option');
            $typeNode = $xpath->query('//select[@name="classcategory_id1"]/option');
            $itemId = "";
            if (isset($xpath->query('//input[@name="goodscd1"]/@value')->item(0)->nodeValue)) {
                $itemId = $xpath->query('//input[@name="goodscd1"]/@value')->item(0)->nodeValue;
            } elseif (isset($xpath->query('//input[@name="product_id"]/@value')->item(0)->nodeValue)) {
                $itemId = $xpath->query('//input[@name="product_id"]/@value')->item(0)->nodeValue;
            }
            foreach ($colorNode as $node) {
                if ($node->getAttribute('value') != '@@') {
                    $color[++$cntColor] = $node->nodeValue;
                }
            }
            foreach ($sizeNode as $node) {
                if ($node->getAttribute('value') != '@@') {
                    $size[++$cntSize] = $node->nodeValue;
                }
            }
            foreach ($typeNode as $node) {
                if ($node->getAttribute('value') != null) {
                    $type[++$cntType] = $node->nodeValue;
                }
            }
            $cnt = 0;
            $options = array();
            if (!empty($color) && !empty($size)) {
                $itemParams['sub_options'][] = array('name' => $optionName[0], 'value' => $color);
                $itemParams['sub_options'][] = array('name' => $optionName[1], 'value' => $size);
                foreach ($color as $colorKey => $colorVal) {
                    foreach ($size as $sizeKey => $sizeVal) {
                        $options[$cnt]['stock'] = $stock;
                        $options[$cnt]['option_id'] = $itemId . $colorKey . $sizeKey;
                        $options[$cnt]['sub_option_id'] = $colorKey . "," . $sizeKey;
                        $options[$cnt]['option_value'][0]['name'] = $optionName[0];
                        $options[$cnt]['option_value'][0]['value'] = $colorVal;
                        $options[$cnt]['option_value'][1]['name'] = $optionName[1];
                        $options[$cnt]['option_value'][1]['value'] = $sizeVal;
                        $cnt++;
                    }
                }
            } elseif (!empty($color)) {
                $itemParams['sub_options'][] = array('name' => $optionName[0], 'value' => $color);
                foreach ($color as $colorKey => $colorVal) {
                    $options[$cnt]['stock'] = $stock;
                    $options[$cnt]['option_id'] = $itemId . $colorKey;
                    $options[$cnt]['sub_option_id'] = $colorKey;
                    $options[$cnt]['option_value'][0]['name'] = $optionName[0];
                    $options[$cnt]['option_value'][0]['value'] = $colorVal;
                    $cnt++;
                }
            } elseif (!empty($size)) {
                $itemParams['sub_options'][] = array('name' => $optionName[1], 'value' => $size);
                foreach ($size as $sizeKey => $sizeVal) {
                    $options[$cnt]['stock'] = $stock;
                    $options[$cnt]['option_id'] = $itemId . $sizeKey;
                    $options[$cnt]['sub_option_id'] = $sizeKey;
                    $options[$cnt]['option_value'][0]['name'] = $optionName[1];
                    $options[$cnt]['option_value'][0]['value'] = $sizeVal;
                    $cnt++;
                }
            }
            if (!empty($type)) {
                $itemParams['sub_options'][] = array('name' => $optionName[2], 'value' => $type);
                foreach ($type as $typeKey => $typeVal) {
                    $options[$cnt]['stock'] = $stock;
                    $options[$cnt]['option_id'] = $itemId . $typeKey;
                    $options[$cnt]['sub_option_id'] = $typeKey;
                    $options[$cnt]['option_value'][0]['name'] = $optionName[2];
                    $options[$cnt]['option_value'][0]['value'] = $typeVal;
                    $cnt++;
                }
            }
            
            if (!empty($options)) {
                $itemParams['options'] = $options;
            }
            
            if (empty($itemParams['item_name']) || empty($itemParams['price'])) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return $itemParams;
    }
}
