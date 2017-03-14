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

namespace OCA\TwoFactorU2F\Controller;

require_once(__DIR__ . '/../../vendor/yubico/u2flib-server/src/u2flib_server/U2F.php');

use OCA\TwoFactorU2F\Service\U2FManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IUserSession;

class SettingsController extends Controller {

	/** @var U2FManager */
	private $manager;

	/** @var IUserSession */
	private $userSession;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param U2FManager $manager
	 * @param IUserSession $userSession
	 */
	public function __construct($appName, IRequest $request, U2FManager $manager, IUserSession $userSession) {
		parent::__construct($appName, $request);
		$this->manager = $manager;
		$this->userSession = $userSession;
	}

	/**
	 * @NoAdminRequired
	 * @return JSONResponse
	 */
	public function state() {
		return [
			'devices' => $this->manager->getDevices($this->userSession->getUser())
		];
	}

	/**
	 * @NoAdminRequired
	 * @PasswordConfirmationRequired
	 * @UseSession
	 * @return JSONResponse
	 */
	public function startRegister() {
		return $this->manager->startRegistration($this->userSession->getUser());
	}

	/**
	 * @NoAdminRequired
	 * @PasswordConfirmationRequired
	 *
	 * @param string $registrationData
	 * @param string $clientData
	 * @param string|null $name device name, given by user
	 * @return JSONResponse
	 */
	public function finishRegister($registrationData, $clientData, $name = null) {
		return $this->manager->finishRegistration($this->userSession->getUser(), $registrationData, $clientData, $name);
	}

	/**
	 * @NoAdminRequired
	 * @PasswordConfirmationRequired
	 *
	 * @param int $id
	 * @return JSONResponse
	 */
	public function remove($id) {
		return $this->manager->removeDevice($this->userSession->getUser(), $id);
	}

}
