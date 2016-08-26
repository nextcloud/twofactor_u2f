<?php

/**
 * Nextcloud - U2F 2FA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 * @copyright Christoph Wurst 2016
 */

namespace OCA\TwoFactor_U2F\Service;

require_once(__DIR__ . '/../../vendor/yubico/u2flib-server/src/u2flib_server/U2F.php');

use OC;
use OCP\ILogger;
use OCP\ISession;
use OCP\IUser;
use u2flib_server\U2F;

class U2FManager {

	/** @var ISession */
	private $session;

	/** @var ILogger */
	private $logger;

	public function __construct(ISession $session, ILogger $logger) {
		$this->session = $session;
		$this->logger = $logger;
	}

	private function getU2f() {
		return new U2F(OC::$server->getURLGenerator()->getAbsoluteURL('/'));
	}

	public function isEnabled(IUser $user) {
		// TODO: save in DB
		return file_exists('/tmp/yubi');
	}

	private function getRegs() {
		if (!file_exists('/tmp/yubi')) {
			return [];
		}
		return [json_decode(file_get_contents('/tmp/yubi'))];
	}

	private function setReg($data) {
		file_put_contents('/tmp/yubi', json_encode($data));
	}

	public function startRegistration(IUser $user = null) {
		$u2f = $this->getU2f();
		$data = $u2f->getRegisterData($this->getRegs());
		list($req, $sigs) = $data;

		$this->logger->debug(json_encode($req));
		$this->logger->debug(json_encode($sigs));

		$this->session->set('twofactor_u2f_regReq', json_encode($req));

		return [
		    'req' => $req,
		    'sigs' => $sigs,
		    'username' => 'user', // TODO
		];
	}

	public function finishRegistration($registrationData, $clientData) {
		$this->logger->debug($registrationData);
		$this->logger->debug($clientData);

		$u2f = $this->getU2f();
		$regReq = json_decode($this->session->get('twofactor_u2f_regReq'));
		$regResp = [
		    'registrationData' => $registrationData,
		    'clientData' => $clientData,
		];
		$reg = $u2f->doRegister($regReq, (object) $regResp);

		$this->setReg($reg);

		$this->logger->debug(json_encode($reg));
	}

	public function startAuthenticate() {
		$u2f = $this->getU2f();
		$u2f->getAuthenticateData($registrations);
	}

	public function finishAuthenticate() {
		
	}

}
