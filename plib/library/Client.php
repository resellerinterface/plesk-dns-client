<?php
require_once __DIR__ . '/../vendor/autoload.php';

use ResellerInterface\Api\Client;
use ResellerInterface\Api\Response\Response;
use ResellerInterface\Api\Response\ResponseDownload;

class Modules_ResellerinterfaceDns_Client extends Client {

	public const STAGING_URL = "https://core-staging.resellerinterface.de/";
	public const LIVE_URL = "https://core.resellerinterface.de/";

	/**
	 * @param string $baseUrl
	 * @param string $version
	 * @param array  $options
	 *
	 * @throws pm_Exception
	 */
	public function __construct(string $baseUrl = self::LIVE_URL, string $version = "stable", array $options = [] ) {
		parent::__construct($baseUrl, $version, $options);
		$extension = pm_Extension::getById("resellerinterface-dns");
		$this->setUserAgent($this->getUserAgent() . " plesk-dns-client/" . $extension->getVersion() . " plesk/" . pm_ProductInfo::getVersion());
	}

	/**
	 * @param array  $loginParams
	 * @param string $action
	 * @param array  $params
	 * @param string $responseType
	 *
	 * @return Response|ResponseDownload|bool|string
	 * @throws \ResellerInterface\Api\Exception\InvalidRequestException
	 * @throws \ResellerInterface\Api\Exception\InvalidResponseException
	 */
	public function sessionRequest(array $loginParams, string $action, array $params = [], string $responseType = self::RESPONSE_RESPONSE): Response|ResponseDownload|bool|string {
		$this->session($loginParams);
		$response = $this->request($action,$params, $responseType);

		// not logged in
		if($response->getState() === 2100) {
			pm_Settings::clean('client');
			$this->session($loginParams);
			return $this->request($action,$params, $responseType);
		}
		return $response;
	}

	/**
	 * @param array $loginParams
	 *
	 * @return void
	 * @throws \ResellerInterface\Api\Exception\InvalidRequestException
	 * @throws \ResellerInterface\Api\Exception\InvalidResponseException
	 */
	private function session(array $loginParams): void {
		$session = pm_Settings::getDecrypted('clientSession');
		if(!$session) {
			$response = $this->login($loginParams['username'] ?? "", $loginParams['password'] ?? "", $loginParams['resellerID'] ?? null);
			if($response->isError()) {
				throw new Exception("login failed");
			}
			pm_Settings::setEncrypted('clientSession', $this->getSession());
		} else {
			$this->setSession($session);
		}
	}
}
