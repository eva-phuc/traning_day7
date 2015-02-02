<?php

return array (
    'status'                   => array(
        'ok'                   => 'success',
        'ng'                   => 'error',
        'no_login'             => 'login_error',
    ),
    'alert'                    => array(
        'on'                   => '通知あり',
        'off'                  => '通知なし',
    ),
    'cache_time'               => 60*10,
    
    'default_search'           => array(
        'sort'                 => 'new',
        'par_page'             => 10,
    ),
    'topfeed_sort'              => array(
        'new'                  => '新着',
        'popular'              => '人気',
        'sale'                 => 'セール',
        'ten_thousand'         => '1万円以下の商品',
    ),
    'myfeed_sort'              => array(
        'new'                  => '新着',
        'discount'             => 'ディスカウント中',
    ),
    'query_list'               => array(
        'member'               => '会員登録について',
        'login'                => 'ログインについて',
        'function'             => '機能要望',
        'alliance'             => '提携先の要望',
        'trouble'              => '不具合の報告',
        'leaving'              => '休会、退会したい',
        'other'                => 'その他',
    ),
    'os_type'              => array(
        'ios'                  => 'ios',
        'android'              => 'android',
    ),
);
