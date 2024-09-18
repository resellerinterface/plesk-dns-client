<?php

class IndexController extends pm_Controller_Action {
	public function init(): void {
		parent::init();

		if(!pm_Session::getClient()->isAdmin()) {
			throw new pm_Exception('Permission denied');
		}

		$this->view->pageTitle = $this->lmsg('pageTitle');
	}

	public function indexAction(): void {
		$form = $this->getForm();
		if($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost();
			if($form->isValid($data)) {
				$this->processForm($data);
			}
		}

		$this->view->tabs = $this->getTabs();
		$this->view->form = $form;
	}

	private function getTabs(): array {
		$tabs = [];
		$tabs[] = [
			'title' => $this->lmsg('pageTitleIndex'),
			'action' => 'index',
		];
		return $tabs;
	}

	private function getForm(): pm_Form_Simple {
		$form = new pm_Form_Simple();

		$form->addElement("text", "apiResellerID", [
			'label' => $this->lmsg('formLabelApiResellerID'),
			'value' => pm_Settings::getDecrypted('apiResellerID'),
			'description' => $this->lmsg('formDescApiResellerID'),
			'required' => true,
			'validators' => [['notEmpty', true]],
		]);

		$form->addElement("text", "apiUsername", [
			'label' => $this->lmsg('formLabelApiUsername'),
			'value' => pm_Settings::getDecrypted('apiUsername'),
			'required' => true,
			'validators' => [['notEmpty', true]],
		]);

		$form->addElement("password", "apiPassword", [
			'label' => $this->lmsg('formLabelApiPassword'),
			'required' => true,
			'validators' => [['notEmpty', true]],
		]);

		$form->addElement("checkbox", "apiBackupZone", [
			'label' => $this->lmsg('formLabelApiBackupZone'),
			'value' => pm_Settings::getDecrypted('apiBackupZone'),
			'description' => $this->lmsg('formDescApiBackupZone'),
		]);

		$form->addElement("checkbox", "apiStatingMode", [
			'label' => $this->lmsg('formLabelApiStagingMode'),
			'value' => pm_Settings::getDecrypted('apiStatingMode'),
			'description' => $this->lmsg('formDescApiStagingMode'),
		]);

		$form->addControlButtons(array(
			'sendTitle' => $this->lmsg('formLabelApiSubmit'),
			'cancelLink' => pm_Context::getModulesListUrl(),
		));

		return $form;
	}

	private function processForm(array $data): void {
		try {
			$stagingMode = !empty($data['apiStatingMode']);
			$client = new Modules_ResellerinterfaceDns_Client($stagingMode ? Modules_ResellerinterfaceDns_Client::STAGING_URL : Modules_ResellerinterfaceDns_Client::LIVE_URL);

			$resellerID = preg_replace("/^(KD-|KD|K)\s*(\d+)$/i", "$2", $data['apiResellerID']);
			$response = $client->login($data['apiUsername'], $data['apiPassword'], $resellerID);
			if($response->isError()) {
				$this->_status->addError( $this->lmsg('apiResponse') . ": " . $response->getState() . " " . $response->getStateName() . ": " .$response->getStateParam());
				$this->_helper->json(['redirect' => pm_Context::getBaseUrl()]);
				return;
			}

			pm_Settings::clean();
			pm_Settings::setEncrypted('apiResellerID', $resellerID);
			pm_Settings::setEncrypted('apiUsername', $data['apiUsername']);
			pm_Settings::setEncrypted('apiPassword', $data['apiPassword']);
			pm_Settings::setEncrypted('apiBackupZone', !empty($data['apiBackupZone']));
			pm_Settings::setEncrypted('apiStatingMode', $stagingMode);

			$this->_status->addInfo($this->lmsg('apiResponse') . ": " . $response->getState() . " " . $response->getStateName());
			$this->_helper->json(['redirect' => pm_Context::getBaseUrl()]);
			return;
		} catch(Exception $e) {
			$this->_status->addError($e->getMessage());
			$this->_helper->json(['redirect' => pm_Context::getBaseUrl()]);
			return;
		}
	}
}
