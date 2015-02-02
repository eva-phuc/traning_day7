<?php


Autoloader::add_core_namespace('Batch');

Autoloader::add_classes(array(
	'Batch\\Notice'              => __DIR__.'/classes/notice.php',
	'Batch\\Batchlog'           => __DIR__.'/classes/batchlog.php',
	'Batch\\Model_Items'         => __DIR__.'/classes/model/items.php',
	'Batch\\Model_Noticeuser'    => __DIR__.'/classes/model/noticeuser.php',
	'Batch\\Model_Noticemail'    => __DIR__.'/classes/model/noticemail.php',
	'Batch\\Model_Ios_Push'      => __DIR__.'/classes/model/ios/push.php',
	'Batch\\Model_Android_Push'  => __DIR__.'/classes/model/android/push.php',
));


/* End of file bootstrap.php */
