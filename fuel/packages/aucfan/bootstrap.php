<?php


Autoloader::add_core_namespace('Aucfan');

Autoloader::add_classes(array(
	'Aucfan\\Auth'           => __DIR__.'/classes/auth.php',
	'Aucfan\\AuthException'  => __DIR__.'/classes/auth.php',
	'Aucfan\\Sendmail'       => __DIR__.'/classes/sendmail.php',
	'Aucfan\\Member'         => __DIR__.'/classes/member.php',
    'Aucfan\\RegisterValidate' => __DIR__.'/classes/member.php',
	'Aucfan\\Payment'        => __DIR__.'/classes/payment.php',
));


/* End of file bootstrap.php */
