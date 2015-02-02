<?php
$status_none   = '';
$status_enable = 'AVAILABLE';
$status_stop   = 'STOP';

return array(
    'status' => array(
        'enable' => $status_enable,
        'stop'   => $status_stop,
        'none'   => $status_none,
    ),
    'forms' => array(
        'service_label' => 'ALERMOプレミアムサービス',
        'service_fee'       => 324,
        'service_fee_extax' => 300,
        'payment_label'     => array(
                            'CARD'  => 'クレジットカード',
        ),
    ),
    'api' => array(
        'url' => array(
            'savecard'       => 'http://192.168.2.161/gmomp/save_card',
            'change_status'  => 'http://192.168.2.161/aucfan_service_manager/user_service_flag_change',
            'getcard'        => 'http://192.168.2.161/gmomp/search_card',
            ),
        'params' => array(
            'savecard'      => array('operation' => 'save_card'),
            'change_status' => array(
                            'password_check' => 'skip',
                            'payment'        => array('card' => 'CARD'),
                            'change_to'      => array('on' => $status_enable,'off' => $status_stop),
            ),
        ),
    ),
    'validate' => array(
        'subscribe' => array(
            'check_keys' => array('cardno','cardmonth','cardyear','security_code'),
            'required'   => array('cardno'=>1,'cardmonth'=>1,'cardyear'=>1,'security_code'=>1),
            'valid_string' => array('security_code' => 'numeric'),
            'length'     => array(
                'security_code' => array('min'=>2,'max'=>4)
            ),
            'special'    => array('cardno'=>'credit_card'),
        ),
    ),
    'error_messages' => array(
        'invalid_system'    => 'システムエラーが発生しました。',
        'invalid_save_card' => 'カード登録時にエラーが発生しました。カード情報を変えて再度登録をしてください。',
        'invalid_change_status' => 'カード決済時にエラーが発生しました。',
        'invalid_required' => ':labelは必須項目です。',
        'invalid_match_value' => '確認用の:labelが一致しません。',
        'invalid_min_length'  => ':labelは:param:1文字以上で入力してください。',
        'invalid_max_length'  => ':labelは:param:1文字以下で入力してください。',
        'invalid_strlen_between'  => ':labelは:param:1文字以上:param:2文字以下で入力してください。',
        'invalid_match_pattern'   => ':labelに使用できない文字が含まれています。',
        'invalid_valid_string'    => ':labelは数字で入力してください。',
        'invalid_credit_card'     => 'クレジットカード番号を正しく入力してください。',
        'invalid_card_date'       => '有効期限を正しく入力してください。',
    ),
    'key_label' => array(
        'account_type'  => 'システム',
        'session_type'  => 'システム',
        'user_remote_addr'  => 'システム',
        'cardno'          => 'カード番号',
        'cardyear'        => '有効期限（年）',
        'cardmonth'       => '有効期限（月）',
        'security_code'   => 'セキュリティコード',
    ),
);
