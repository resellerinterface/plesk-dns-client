<?php

try {
	pm_Loader::registerAutoload();
	pm_Context::init("resellerinterface-dns");

	// extension disabled
	$extension = pm_Extension::getById("resellerinterface-dns");
	if(!$extension->isActive()) {
		exit(0);
	}

	$datas = json_decode(file_get_contents('php://stdin'), true, 512, JSON_THROW_ON_ERROR);

	$loginParams = [
		'resellerID' => pm_Settings::getDecrypted('apiResellerID'),
		'username' => pm_Settings::getDecrypted('apiUsername'),
		'password' => pm_Settings::getDecrypted('apiPassword'),
	];
	$apiBackupZone = pm_Settings::getDecrypted('apiBackupZone');
	$apiStatingMode = pm_Settings::getDecrypted('apiStatingMode');

	$client = new Modules_ResellerinterfaceDns_Client($apiStatingMode ? Modules_ResellerinterfaceDns_Client::STAGING_URL : Modules_ResellerinterfaceDns_Client::LIVE_URL);

	foreach($datas as $data) {
		// create a zone
		if($data['command'] === "create") {
			$data['zone']['name'] = preg_replace( "/\.$/", "", $data['zone']['name']);
			$response = $client->sessionRequest($loginParams, "dns/createZoneDefault", [
				'domain' => $data['zone']['name'],
			]);
			if($response->isError() && $response->getState() !== 2001) { // 2001 => ALREADY_EXISTS
				throw new Exception($data['zone']['name'] . " dns/createZoneDefault " . pm_Locale::lmsg('apiResponse') . ": " . $response->getState() . " " . $response->getStateName() . ": " .$response->getStateParam());
			}
		}

		// update a zone, set records
		if($data['command'] === "update") {
			$data['zone']['name'] = preg_replace( "/\.$/", "", $data['zone']['name']); // remove trailing dot
			$response = $client->sessionRequest($loginParams, "dns/updateZone", [
				'domain' => $data['zone']['name'],
				'refresh' => $data['zone']['soa']['refresh'],
				'retry' => $data['zone']['soa']['retry'],
				'expire' => $data['zone']['soa']['expire'],
				'minimum' => $data['zone']['soa']['minimum'],
				'ttl' => $data['zone']['soa']['ttl'],
			]);
			if($response->isError()) {
				throw new Exception($data['zone']['name'] . " dns/updateZone " . pm_Locale::lmsg('apiResponse') . ": " . $response->getState() . " " . $response->getStateName() . ": " .$response->getStateParam());
			}

			$records = [];
			foreach($data['zone']['rr'] as $rr) {
				$rr['host'] = preg_replace( "/\.$/", "", $rr['host']); // remove trailing dot

				// skip NS records for the zone
				if($rr['type'] === "NS" && $rr['host'] === $data['zone']['name']) {
					continue;
				}
				// skip not supported records
				if(in_array($rr['type'], ['DS', 'PTR'])) {
					continue;
				}

				// merge SRV/TLSA priority and content
				if($rr['type'] === "SRV" || $rr['type'] === "TLSA") {
					$rr['value'] = $rr['opt'] . " " . $rr['value'];
					$rr['opt'] = null;
				}
				// merge CAA priority and content
				if($rr['type'] === "CAA") {
					$rr['value'] = $rr['opt'] . " \"" . $rr['value'] . "\"";
					$rr['opt'] = null;
				}

				if(in_array($rr['type'], ['CNAME', 'MX', 'SRV', 'NS'])){
					$rr['value'] = preg_replace( "/\.$/", "", $rr['value']); // remove trailing dot
				}

				$records[] = [
					'name' => $rr['host'],
					'type' => $rr['type'],
					'ttl' => $rr['ttl'],
					'priority' => $rr['opt'],
					'content' => $rr['value'],
				];
			}
			$response = $client->sessionRequest($loginParams, "dns/setRecords", [
				'domain' => $data['zone']['name'],
				'backupZone' => $apiBackupZone,
				'clearZone' => true,
				'records' => $records,
			]);
			if($response->isError()) {
				throw new Exception($data['zone']['name'] . " dns/updateZone " . pm_Locale::lmsg('apiResponse') . ": " . $response->getState() . " " . $response->getStateName() . ": " .$response->getStateParam());
			}
		}

		// delete a zone
		if($data['command'] === "delete") {
			$data['zone']['name'] = preg_replace( "/\.$/", "", $data['zone']['name']);
			$response = $client->sessionRequest($loginParams, "dns/deleteZone", [
				'domain' => $data['zone']['name'],
			]);
			if($response->isError() && $response->getState() !== 2002) { // 2002 => NOT_EXISTS
				throw new Exception($data['zone']['name'] . " dns/deleteZone " . pm_Locale::lmsg('apiResponse') . ": " . $response->getState() . " " . $response->getStateName() . ": " .$response->getStateParam());
			}
		}

		// create ptr records
		if($data['command'] === "createPTRs") {
			throw new Exception("createPTRs not supported");
		}

		// delete ptr records
		if($data['command'] === "deletePTRs") {
			throw new Exception("deletePTRs not supported");
		}
	}
} catch(Exception $e) {
	echo $e->getMessage() . "\n";
	exit(1);
}
exit(0);
