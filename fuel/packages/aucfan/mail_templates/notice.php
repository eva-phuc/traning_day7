<?php

$break_count = 20; // 商品表示数


// 重複アイテム削除
$tmp = array();
$itemResult = array();
foreach( $items as $key => $value ){
    if(! in_array($value['item_id'], $tmp)) {
        $tmp[] = $value['item_id'];
        $itemResult[] = $value;
    }
}
// 表示整形
$i = 0;
$item_text = '';
foreach ($itemResult as $value) {
    // 割引率計算
    $priceDown = $value['bookmark_price'] - $value['price'];
    $tmpPrice = $priceDown / $value['bookmark_price'];
    $downRate = round($tmpPrice * 100);
    
    // 表示
    $item_text .= "======================================================================\n";
    $item_text .= "■" . $value['item_name'] . "\n\n";
    $item_text .= $downRate."%OFF\n\n";
    $item_text .= '￥' . number_format($value['bookmark_price']) . ' > ￥'.number_format($value['price'])."\n\n";
    $item_text .= $value['name']."\n\n";
    $item_text .= $baseUrl.'?item='.$value['item_id']."\n";
    $item_text .= "======================================================================\n\n";
    $i++;
    
    if ($i >= $break_count) {
        break;
    }
}

echo '
クリップしたアイテムの価格が下がりました、ショップで売り切れる前にcheck!

'.$item_text.'

ALERMO━━━━━━━━━━━━━━━━━━━━━━━━━━
おしゃれに賢く、オトクな買い物ならALERMO
http://www.alermo.me/about.html
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━


※この通知メールの配信停止をご希望の場合※
以下のURLをクリックして頂き、記載の方法に従って、アプリ上で配信停止の設定を行ってください。
http://www.alermo.me/suspention

※尚、本メールと行き違いで配信停止、退会手続きをされた場合は、何卒ご容赦ください。
※この通知メールに対してのお問い合わせは、alermo_support@aucfan.comまでお願い致します。

株式会社オークファン　http://aucfan.co.jp/
Copyright (C) aucfan Co.,Ltd. All rights reserved.';