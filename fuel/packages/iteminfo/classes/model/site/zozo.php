<?php
namespace Iteminfo;

/**
 * ZOZOTOWN用 商品データ解析
 * 
 * @author higuchi
 */
class Model_Site_Zozo extends \Model {

    /**
     * 商品データ取得・解析
     * 
     * @access public
     * @param string $url
     * @return mixed
     * @author higuchi
     */
	public static function getData($url) {
        
        // オプション名称
        $optionName[0] = '色';
        $optionName[1] = 'サイズ';
        
        $errorFlag = 0;
        
        // 商品データ取得・整形
        try{
        
            // URLからHTMLソース取得
            $htmlData = Model_Gethtml::getData($url);
            if($htmlData === false) return false;
            $htmlData = mb_convert_encoding($htmlData, 'UTF-8', 'SJIS');
            
            // ---- HTMLパース ----------------------------------------
            $itemParams = array();
            // メイン画像
            $str =explode('<div id="photoMain"><img src="', $htmlData);
            if (isset($str[1])) {
                $str = explode('"', $str[1]);
                if (preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $str[0])) {
                    $itemParams['img_url'] = $str[0];
                }
            }
            
            // 商品名・価格・セール情報・ブランド
            $str = explode('<div id="item-intro">', $htmlData);
            if (isset($str[1])) {
                $str = explode('<div id="privilege">', $str[1]);
                $tmpHtml = $str[0];
                
                 $str =explode('<h1>', $tmpHtml);
                if (isset($str[1])) {
                    $str = explode('</h1>', $str[1]);
                    $itemParams['item_name'] = $str[0];
                }
                $str =explode('<p class="price">&yen;', $tmpHtml); // セール外のパターン
                if (isset($str[1])) {
                    $str = explode('<span', $str[1]);
                    $number = str_replace(',', '', $str[0]);
                    if (preg_match("/^[0-9]+$/", $number)) {
                        $itemParams['price'] = $number;
                        $itemParams['default_price'] = $number;
                        $itemParams['sale'] = 'no';
                    }
                } else {
                    $str =explode('<p class="price priceDown"><span>&yen;', $tmpHtml); // セール中のパターン
                    if (isset($str[1])) {
                        $str = explode('&nbsp;→', $str[1]);
                        $number = str_replace(',', '', $str[0]);
                        if (preg_match("/^[0-9]+$/", $number)) {
                            $itemParams['default_price'] = $number;
                        }
                    }
                    $str =explode('</span>&yen;', $tmpHtml);
                    if (isset($str[1])) {
                        $str = explode('<span', $str[1]);
                        $number = str_replace(',', '', $str[0]);
                        if (preg_match("/^[0-9]+$/", $number)) {
                            $itemParams['price'] = $number;
                            $itemParams['sale_price'] = $number;
                            $itemParams['sale'] = 'yes';
                        }
                    }
                }
                $str =explode('ブランド：<a href="', $tmpHtml);
                if (isset($str[1])) {
                    $str =explode('">', $str[1]);
                    if (isset($str[1])) {
                       $str = explode('</a>', $str[1]);
                       $itemParams['brand'] = $str[0];
                    }
                }
            }
            
            
            // 在庫の有無
            if (preg_match("/noStockBox/", $htmlData)) {
                $itemParams['stock'] = 0;
            } else {
                $itemParams['stock'] = 99;
            }
            
            
            // カラーサイズ情報
            $str = explode('<div class="blockMain">', $htmlData);
            if (isset($str[1])) {
                $str = explode('</div><!--/.blockMain-->', $str[1]);
                $tmpHtml = $str[0];
                
                // カラーごとに分割
                $color_name = '';
                $color_img = '';
                $tmpColor = explode('<dl class="clearfix">', $tmpHtml);
                if (isset($tmpColor[1])) {
                    array_shift($tmpColor); // ひとつめのみ空なので削除
                    
                    // オプションのカラーとサイズの初期設定
                    $cnt = 0;
                    $cntColor = 0;
                    $itemParams['sub_options'][0]['name'] = $optionName[0];
                    $itemParams['sub_options'][1]['name'] = $optionName[1];
                    
                    foreach ($tmpColor as $colorVal) {
                        $str = explode('<span class="txt">', $colorVal);
                        if (isset($str[1])) {
                            if (preg_match("/<\/span><\/dt>/",  $str[1])) {
                                $str = explode('</span></dt>', $str[1]);
                                $cntColor++;
                                // カラー名
                                $colorName = $str[0];
                                $itemParams['sub_options'][0]['value'][$cntColor] = $colorName;
                            }
                        }
                        $str = explode('<span class="img"><img src="', $colorVal);
                        if (isset($str[1])) {
                            $str = explode('"', $str[1]);
                            if (preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $str[0])) {
                                $optionImg = $str[0]; // オプション画像
                            }
                        }
                    
                        // サイズ毎に分割
                        $cntSize = 0;
                        $tmpSize = explode('</div></li>', $colorVal);
                        foreach ($tmpSize as $sizeVal) {
                            // サイズ名・在庫
                            $tmpStr = explode('&nbsp;/&nbsp;</span><span>', $sizeVal);
                            if (! isset($tmpStr[1])) {
                                $tmpStr = explode('&nbsp;/&nbsp;', $sizeVal);
                            }
                            if (isset($tmpStr[1])) {
                                // 在庫パース
                                $str = explode('</span></p></div>', $tmpStr[1]);
                                if ($str[0] == '在庫なし') {
                                    $itemParams['options'][$cnt]['stock'] = 0;
                                } else {
                                    $itemParams['options'][$cnt]['stock'] = 99;
                                    // 在庫が少ない場合在庫数を取得できる場合がある
                                    $stockStr = explode('残り', $str[0]);
                                    if (isset($stockStr[1])) {
                                        $stockStr = explode('点', $stockStr[1]);
                                        if (preg_match("/^[0-9]+$/", $stockStr[0])) {
                                            $itemParams['options'][$cnt]['stock']= $stockStr[0];
                                        }
                                    }
                                }
                                // サイズパース
                                $str = explode('<p><span>', $tmpStr[0]);
                                if (isset($str[1])) {
                                    $str = explode('&nbsp;/', $str[1]);
                                    $cntSize++;
                                    // サイズ名
                                    $sizeName = $str[0];
                                    $itemParams['sub_options'][1]['value'][$cntSize] = $sizeName;
                                } else {
                                    $errorFlag = 1;
                                }
                            }
                            // サイズ毎の商品詳細ID
                            $str = explode('<li class="clearfix" id="cart_did_', $sizeVal);
                            if (isset($str[1])) {
                                $str = explode('">', $str[1]);
                                if (preg_match("/^[0-9]+$/", $str[0])) {
                                    $itemParams['options'][$cnt]['option_id'] = $str[0]; // オプションID
                                    $itemParams['options'][$cnt]['option_img'] = $optionImg; // オプション画像
                                    // オプション選択識別用ID
                                    $tempKey = array();
                                    $tempKey[] = $cntColor;
                                    $tempKey[] = $cntSize;
                                    $itemParams['options'][$cnt]['sub_option_id'] = implode(",", $tempKey);
                                    $itemParams['options'][$cnt]['option_value'][0]['name'] = $optionName[0];
                                    $itemParams['options'][$cnt]['option_value'][0]['value'] = $colorName;
                                    $itemParams['options'][$cnt]['option_value'][1]['name'] = $optionName[1];
                                    $itemParams['options'][$cnt]['option_value'][1]['value'] = $sizeName; 
                                } else {
                                    $errorFlag = 1;
                                }
                            }
                            $cnt++;
                        }
                    }
                }
            }
            
            // カテゴリ
            $str = explode('<dt>カテゴリ<span>:</span>', $htmlData);
            if (isset($str[1])) {
                $str = explode('</ul>', $str[1]);
                $categoryHtml = explode('<li>', $str[0]);
                if (isset($categoryHtml[1])) {
                    $str = explode('<span>></span></li>', $categoryHtml[1]);
                    $itemParams['category'] = $str[0];
                }
                if (isset($categoryHtml[2])) {
                    $str = explode('</li>', $categoryHtml[2]);
                    $itemParams['category'] .= ' > '.$str[0];
                }
            }

            // 商品名、価格取れなれな場合や、途中にエラーがあった場合
            if(empty($itemParams['item_name']) || empty($itemParams['price']) || $errorFlag == 1) {
                return false;
            }
            
        } catch (\Exception $e) {
            return false;
        }
        
        return $itemParams;
        
	}


}