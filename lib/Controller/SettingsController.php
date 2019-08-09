<?php

declare(strict_types = 1);

/**
 * Nextcloud - U2F 2FA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 * @copyright Christoph Wurst 2018
 */

namespace OCA\TwoFactorU2F\Controller;

require_once(__DIR__ . '/../../vendor/yubico/u2flib-server/src/u2flib_server/U2F.php');

use OCA\TwoFactorU2F\Service\U2FManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\Authentication\TwoFactorAuth\ALoginSetupController;
use OCP\IRequest;
use OCP\IUserSession;

class SettingsController extends ALoginSetupController {

	/** @var U2FManager */
	private $manager;

	/** @var IUserSession */
	private $userSession;

	public function __construct(string $appName, IRequest $request, U2FManager $manager, IUserSession $userSession) {
		parent::__construct($appName, $request);
		$this->manager = $manager;
		$this->userSession = $userSession;
	}

	/**
	 * @NoAdminRequired
	 */
	public function state(): JSONResponse {
		return new JSONResponse([
			'devices' => $this->manager->getDevices($this->userSession->getUser())
		]);
	}

	/**
	 * @NoAdminRequired
	 * @PasswordConfirmationRequired
	 * @UseSession
	 */
	public function startRegister(): JSONResponse {
		return new JSONResponse($this->manager->startRegistration($this->userSession->getUser()));
	}

	/**
	 * @NoAdminRequired
	 * @PasswordConfirmationRequired
	 *
	 * @param string $registrationData
	 * @param string $clientData
	 * @param string|null $name device name, given by user
	 */
	public function finishRegister(string $registrationData, string $clientData, string $name = null): JSONResponse {
		return new JSONResponse($this->manager->finishRegistration($this->userSession->getUser(), $registrationData, $clientData, $name));
	}

	/**
	 * @NoAdminRequired
	 * @PasswordConfirmationRequired
	 *
	 * @param int $id
	 */
	public function remove(int $id): JSONResponse {
		return new JSONResponse($this->manager->removeDevice($this->userSession->getUser(), $id));
	}

}
