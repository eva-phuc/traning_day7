<?php

namespace Iteminfo;

/**
 * Buyma API
 */
class ModelSiteBuyma extends \Model
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
		$getHtml = new Model_Gethtml();
        $htmlData = $getHtml->getData($url);
        //$htmlData = \Model_Gethtml::getData($url);
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
			if(isset($xpath->query('//dl[@id="s_cate"]/dd')->item(0)->nodeValue)){
				$itemParams['category'] = trim($xpath->query('//dl[@id="s_cate"]/dd')->item(0)->nodeValue);	
			}
            if(isset($xpath->query('//dl[@id="s_brand"]/dd/a')->item(0)->nodeValue)){
				$itemParams['brand'] = trim($xpath->query('//dl[@id="s_brand"]/dd/a')->item(0)->nodeValue);	
			}elseif(isset($xpath->query('//dl[@id="s_brand"]/dd')->item(0)->nodeValue)){
				$itemParams['brand'] = trim($xpath->query('//dl[@id="s_brand"]/dd')->item(0)->nodeValue);	
			}

            //get product name
            $itemParams['item_name'] = $xpath->query('//h1[@id="item_h1"]')->item(0)->nodeValue;
           
            
            //get product default image
            preg_match('#\((.*?)\)#', $xpath->query('//div[@id="item_mainimg_box"]/p/@style')->item(0)->value, $results);
			$itemParams['img_url'] = $results[1];

            //get product price
            $itemParams['sale'] = "no";
            $itemParams['price'] = $itemParams['default_price'] = 0;
			$itemParams['price'] = (int) preg_replace('/[^+\d]/', '', $xpath->query('//span[@class="price_txt"]')->item(0)->nodeValue);
			
			if (isset($xpath->query('//span[@class="percent_refer"]')->item(0)->nodeValue)) {
				$itemParams['sale'] = 'yes';                    
				$itemParams['default_price'] = (int) preg_replace('/[^+\d]/', '', $xpath->query('//span[@class="percent_refer"]')->item(0)->nodeValue);
				$itemParams['sale_price'] = $itemParams['price'];
			}
			$itemParams['quantity'] = 0;
			$itemParams['quantity'] = (int) preg_replace('/[^+\d]/', '', $xpath->query('//dl[@id="s_quantity"]')->item(0)->nodeValue);
            return $itemParams;
        } catch (\Exception $e) {
            return false;
        }
    }
}
