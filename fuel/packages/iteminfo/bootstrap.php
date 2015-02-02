<?php


Autoloader::add_core_namespace('Iteminfo');

Autoloader::add_classes(array(
	'Iteminfo\\Siteparse'           		=> __DIR__.'/classes/siteparse.php',
	'Iteminfo\\Affili'              		=> __DIR__.'/classes/affili.php',
	'Iteminfo\\Model_Gethtml'       		=> __DIR__.'/classes/model/getthtml.php',
	'Iteminfo\\Model_Getapi'        		=> __DIR__.'/classes/model/getapi.php',
	'Iteminfo\\Model_Item'          		=> __DIR__.'/classes/model/item.php',
	'Iteminfo\\Model_Site_Yshop'    		=> __DIR__.'/classes/model/site/yshop.php',
	'Iteminfo\\Model_Site_Zozo'     		=> __DIR__.'/classes/model/site/zozo.php',
	'Iteminfo\\Model_Site_Felissimo'    	=> __DIR__.'/classes/model/site/felissimo.php',
	'Iteminfo\\Model_Site_Locondo'    		=> __DIR__.'/classes/model/site/locondo.php',
	'Iteminfo\\Model_Site_Fashionwalker'	=> __DIR__.'/classes/model/site/fashionwalker.php',
	'Iteminfo\\Model_Site_Dinos'    		=> __DIR__.'/classes/model/site/dinos.php',
	'Iteminfo\\Model_Site_Bellemaison'    	=> __DIR__.'/classes/model/site/bellemaison.php',
	'Iteminfo\\Model_Site_Nissen'    		=> __DIR__.'/classes/model/site/nissen.php',
	'Iteminfo\\Model_Site_Magaseek'   		=> __DIR__.'/classes/model/site/magaseek.php',
	'Iteminfo\\Model_Site_Stylife'  		=> __DIR__.'/classes/model/site/stylife.php',
	'Iteminfo\\ModelSiteBuyma'  		=> __DIR__.'/classes/model/site/buyma.php',
));


/* End of file bootstrap.php */
