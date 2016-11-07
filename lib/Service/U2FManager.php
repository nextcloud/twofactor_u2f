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

namespace OCA\TwoFactorU2F\Service;

require_once(__DIR__ . '/../../vendor/yubico/u2flib-server/src/u2flib_server/U2F.php');

use InvalidArgumentException;
use OC;
use OCA\TwoFactorU2F\Db\Registration;
use OCA\TwoFactorU2F\Db\RegistrationMapper;
use OCP\ILogger;
use OCP\IRequest;
use OCP\ISession;
use OCP\IURLGenerator;
use OCP\IUser;
use u2flib_server\Error;
use u2flib_server\U2F;

class U2FManager {

	/** @var RegistrationMapper */
	private $mapper;

	/** @var ISession */
	private $session;

	/** @var ILogger */
	private $logger;

	/** @var IRequest */
	private $request;

	public function __construct(RegistrationMapper $mapper, ISession $session, ILogger $logger, IRequest $request) {
		$this->mapper = $mapper;
		$this->session = $session;
		$this->logger = $logger;
		$this->request = $request;
	}

	private function getU2f() {
		$url = $this->request->getServerProtocol() . '://' . $this->request->getServerHost();
		return new U2F($url);
	}

	private function getRegistrations(IUser $user) {
		$registrations = $this->mapper->findRegistrations($user);
		$registrationObjects = array_map(function (Registration $registration) {
			return (object) $registration->jsonSerialize();
		}, $registrations);
		return $registrationObjects;
	}

	public function isEnabled(IUser $user) {
		$registrations = $this->mapper->findRegistrations($user);
		return count($registrations) > 0;
	}

	public function disableU2F(IUser $user) {
		// TODO: use single query instead
		foreach ($this->mapper->findRegistrations($user) as $registration) {
			$this->mapper->delete($registration);
		}
	}

	public function startRegistration(IUser $user) {
		$u2f = $this->getU2f();
		$data = $u2f->getRegisterData($this->getRegistrations($user));
		list($req, $sigs) = $data;

		$this->logger->debug(json_encode($req));
		$this->logger->debug(json_encode($sigs));

		$this->session->set('twofactor_u2f_regReq', json_encode($req));

		return [
			'req' => $req,
			'sigs' => $sigs,
		];
	}

	public function finishRegistration(IUser $user, $registrationData, $clientData) {
		$this->logger->debug($registrationData);
		$this->logger->debug($clientData);

		$u2f = $this->getU2f();
		$regReq = json_decode($this->session->get('twofactor_u2f_regReq'));
		$regResp = [
			'registrationData' => $registrationData,
			'clientData' => $clientData,
		];
		$reg = $u2f->doRegister($regReq, (object) $regResp);

		$registration = new Registration();
		$registration->setUserId($user->getUID());
		$registration->setKeyHandle($reg->keyHandle);
		$registration->setPublicKey($reg->publicKey);
		$registration->setCertificate($reg->certificate);
		$registration->setCounter($reg->counter);
		$this->mapper->insert($registration);

		$this->logger->debug(json_encode($reg));
	}

	public function startAuthenticate(IUser $user) {
		$u2f = $this->getU2f();
		$reqs = $u2f->getAuthenticateData($this->getRegistrations($user));
		$this->session->set('twofactor_u2f_authReq', json_encode($reqs));
		return $reqs;
	}

	public function finishAuthenticate(IUser $user, $challenge) {
		$u2f = $this->getU2f();

		$registrations = $this->getRegistrations($user);
		$authReq = json_decode($this->session->get('twofactor_u2f_authReq'));
		try {
			$reg = $u2f->doAuthenticate($authReq, $registrations, json_decode($challenge));
		} catch (InvalidArgumentException $ex) {
			$this->logger->warning('U2F auth failed: ' . $ex->getMessage());
			return false;
		} catch (Error $ex) {
			$this->logger->warning('U2F auth failed: ' . $ex->getMessage());
			return false;
		}

		$registration = $this->mapper->findRegistration($user, $reg->id);
		$registration->setCounter($reg->counter);
		$this->mapper->update($registration);
		return true;
	}

}
