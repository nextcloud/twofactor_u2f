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

namespace OCA\TwoFactorU2F\Tests\Unit\Controller;

use OCA\TwoFactorU2F\Controller\SettingsController;
use OCA\TwoFactorU2F\Service\U2FManager;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IUser;
use OCP\IUserSession;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SettingsControllerTest extends TestCase {

	/** @var IRequest|MockObject */
	private $request;

	/** @var U2FManager|MockObject */
	private $u2fManager;

	/** @var IUserSession|MockObject */
	private $userSession;

	/** @var SettingsController */
	private $controller;

	protected function setUp() {
		parent::setUp();

		$this->request = $this->createMock(IRequest::class);
		$this->u2fManager = $this->createMock(U2FManager::class);
		$this->userSession = $this->createMock(IUserSession::class);

		$this->controller = new SettingsController('twofactor_u2f', $this->request, $this->u2fManager, $this->userSession);
	}

	public function testState() {
		$user = $this->createMock(IUser::class);
		$devices = [
			[
				'id' => 1,
				'name' => null,
			],
			[
				'id' => 2,
				'name' => 'Yolokey',
			],
		];
		$this->userSession->expects($this->once())
			->method('getUser')
			->willReturn($user);
		$this->u2fManager->expects($this->once())
			->method('getDevices')
			->with($this->equalTo($user))
			->willReturn($devices);

		$expected = new JSONResponse([
			'devices' => $devices,
		]);
		$this->assertEquals($expected, $this->controller->state());
	}

	public function testStartRegister() {
		$user = $this->createMock(IUser::class);
		$this->userSession->expects($this->once())
			->method('getUser')
			->willReturn($user);

		$this->u2fManager->expects($this->once())
			->method('startRegistration')
			->with($this->equalTo($user))
			->willReturn([]);

		$this->assertEquals(new JSONResponse([]), $this->controller->startRegister());
	}

	public function testFinishRegister() {
		$user = $this->createMock(IUser::class);
		$this->userSession->expects($this->once())
			->method('getUser')
			->willReturn($user);
		$registrationData = 'regData';
		$data = 'some data';

		$this->u2fManager->expects($this->once())
			->method('finishRegistration')
			->with($this->equalTo($user), $this->equalTo($registrationData), $this->equalTo($data))
			->willReturn([]);

		$resp = $this->controller->finishRegister($registrationData, $data);

		$this->assertEquals(new JSONResponse([]), $resp);
	}

}
