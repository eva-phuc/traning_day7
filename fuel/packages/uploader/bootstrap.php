<?php


Autoloader::add_core_namespace('Uploader');

Autoloader::add_classes(array(
	'Uploader\\Image'           => __DIR__.'/classes/image.php',
	'Uploader\\ImageException'  => __DIR__.'/classes/image.php',
));


/* End of file bootstrap.php */
