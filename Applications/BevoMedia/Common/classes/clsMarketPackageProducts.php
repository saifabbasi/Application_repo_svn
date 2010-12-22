<?php

class MarketPackageProducts extends DBObject {

	function __construct() {
		parent::__construct('bevomedia_market_package_products', 'id', array('packageId', 'productId', 'qty'));
	}
	
	function GetListByPackageid($intInPackageid) {
		$strSQL = 'SELECT bevomedia_market_package_products.id, productId, qty, bevomedia_market_products.name AS ProductName, market_products.price AS ProductPrice FROM
					bevomedia_market_package_products LEFT JOIN bevomedia_market_products ON bevomedia_market_package_products.productDd = bevomedia_market_products.id
					WHERE (packageId = ' . $intInPackageid . ')';
		$this->Select($strSQL);
	}
	
	
}

?>
