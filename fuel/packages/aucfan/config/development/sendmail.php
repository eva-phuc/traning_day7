<?php

return array(
        'url' => array(
                'sendmail'           => 'http://mailapi.aucfan.com/aucfan_mail_send_api_staging.cgi',
        ),
        'defaults' => array(
                'template' => array(
                        'from'=>'ALERMO','from_addr'=>'','mail_option'=>'','operation'=>'send_template_mail',
                ),
                'inner_template' => array(
                        'from'        => 'ALERMO',
			'from_addr'   => 'aucuniv@aucfan.com',
			'to'          => 'tanaka@aucfan.com',
			'operation'   => 'send',
			'subject'     => '【ALERMO】退会アンケート',
			'body'        => 'エラー',
			'mail_option' => '',
                ),
            'notice' => array(
                'from'        => 'ALERMO',
                'from_addr'   => 'alermo_support@aucfan.com',
                'operation'   => 'send',
                'subject'     => '[stg]【ALERMO】クリップ中のアイテムが値下がりしました!',
                'body'        => 'エラー',
                'mail_option' => '',
            ),
            'inquiry' => array(
                'from'        => 'ALERMO',
                'from_addr'   => 'alermo_support@aucfan.com',
                'to'          => 't_higuchi@aucfan.com',
                'operation'   => 'send',
                'subject'     => '【ALERMO】',
                'body'        => 'エラー',
                'mail_option' => '',
            ),
        ),
	'validation' => array(
		'template' => array(
			'check_keys' => array('to','template'),
			'required'   => array('to'=>1,'template'=>1),
			'numeric'    => array('to'=>1),
		),
		'inner_template' => array(
			'check_keys' => array('from_addr','to','subject','operation','tmpl_key'),
			'required'   => array('from_addr'=>1,'to'=>1,'subject'=>1,'operation'=>1,'tmpl_key'=>1),
			'mailaddr'   => array('from_addr'=>1,'to'=>1),
		),
	),
	'error_messages' => array(
		'invalid_required'     => ':labelは必須項目です。',
		'invalid_mailaddr'     => 'メールアドレスの形式が間違っています。',
		'invalid_valid_string' => ':labelは:param:1のみ有効です。',
		'invalid_system'       => 'メール送信時エラー',
	),
	'templates' => array(
		'regist' => 'alermo_pre',
	),
	'template_files' => array(
		'enquete' => 'enquete.php',
		'notice'  => 'notice.php',
		'inquiry'  => 'inquiry.php',
	),
);
