<?php
pm_Loader::registerAutoload();
pm_Context::init("resellerinterface-dns");

try {
	$result = pm_ApiCli::call('server_dns', array('--disable-custom-backend'));
	pm_Settings::clean();
} catch (pm_Exception $e) {
	echo $e->getMessage() . "\n";
	exit(1);
}
exit(0);
