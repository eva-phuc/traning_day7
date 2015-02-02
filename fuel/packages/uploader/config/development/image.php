<?php 

return array(
	'upload_max_units'  => 3,
	'upload_max_size'   => 3145728,  // 3M
	'upload_allowed_extensions' => array(
			'jpg'  => 'image',
			'jpeg' => 'image',
			'gif'  => 'image',
	),
	'upload_temporary_path' => '/tmp',
	'upload_path_base'      => DOCROOT.'/upfiles',

);

