<?php
pm_Loader::registerAutoload();
pm_Context::init("resellerinterface-dns");

try {
	if(substr(PHP_OS, 0, 3) === "WIN") {
		$cmd = '"' . PRODUCT_ROOT . '\bin\extension.exe"';
	} else {
		$cmd = '"' . PRODUCT_ROOT . '/bin/extension"';
	}

	$script = $cmd . ' --exec ' . pm_Context::getModuleId() . ' dns-update.php';
	$result = pm_ApiCli::call('server_dns', array('--enable-custom-backend', $script));
} catch (pm_Exception $e) {
	echo $e->getMessage() . "\n";
	exit(1);
}
exit(0);
