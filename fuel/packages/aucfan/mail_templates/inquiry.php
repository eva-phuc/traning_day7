<?php

$list = \Config::get('cn.query_list');
$body = "【お問合せ: ".$inputData['os']." 】\n\n";
$body .= "【お問合せメールアドレス】 \n".$inputData['mail_addr']."\n\n";
$body .= "【お問合せ種別】 \n".$list[$inputData['query_param']]."\n\n";
$body .= "【お問合せ内容】 \n".$inputData['body']."\n\n";

echo $body;