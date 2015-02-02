<?php 

return array(
	'session_api_host' => '192.168.101.61',
	'session_api_path' => array(
				'pc' => 'aucfan_session_check_alermo',
				'ip' => 'aucfan_session_check',
				),
	'override_remote_addr_emulate' => null,
	 'systemid'       => array(
				'pc' => 'alermo',
				'ip' => 'aucdata',
				),

	//--- 課金サービスのキー ---//
	'services' => array(
			'premium'    => true,
			'aucdata'    => true,
			'seminer'    => true,
			'aucfan_pro' => true,
			'school'     => true,
			'pricefan'   => true,
			'aucuniv'    => true,
            'alermo'     => true,
			),
	//--- プレミアムオプションのキー ---//
	'premium_options' => array(
				'2ys'     => true,
				'csv'     => true,
				'pt_plus' => true,
				'mail_magazine' => true,
				'my_selling_storage'    => true,
				),
);

