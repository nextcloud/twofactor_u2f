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

namespace OCA\TwoFactorU2F\Test\Unit\Controller;

use OCA\TwoFactorU2F\Controller\SettingsController;
use OCA\TwoFactorU2F\Service\U2FManager;
use OCP\IRequest;
use OCP\IUser;
use OCP\IUserSession;
use PHPUnit_Framework_MockObject_MockObject;
use Test\TestCase;

class SettingsControllerTest extends TestCase {

	/** @var IRequest|PHPUnit_Framework_MockObject_MockObject */
	private $request;

	/** @var U2FManager|PHPUnit_Framework_MockObject_MockObject */
	private $u2fManager;

	/** @var IUserSession|PHPUnit_Framework_MockObject_MockObject */
	private $userSession;

	/** @var SettingsController */
	private $controller;

	protected function setUp() {
		parent::setUp();

		$this->request = $this->createMock(IRequest::class);
		$this->u2fManager = $this->getMockBuilder(U2FManager::class)
			->disableOriginalConstructor()
			->getMock();
		$this->userSession = $this->createMock(IUserSession::class);

		$this->controller = new SettingsController('twofactor_u2f', $this->request, $this->u2fManager, $this->userSession);
	}

	public function testState() {
		$user = $this->createMock(IUser::class);
		$this->userSession->expects($this->once())
			->method('getUser')
			->will($this->returnValue($user));
		$this->u2fManager->expects($this->once())
			->method('isEnabled')
			->with($this->equalTo($user))
			->will($this->returnValue(true));

		$expected = [
		    'enabled' => true,
		];
		$this->assertSame($expected, $this->controller->state());
	}

	public function testDisable() {
		$user = $this->createMock(IUser::class);
		$this->userSession->expects($this->once())
			->method('getUser')
			->will($this->returnValue($user));
		$this->u2fManager->expects($this->once())
			->method('disableU2F')
			->with($this->equalTo($user));

		$this->controller->disable();
	}

	public function testStartRegister() {
		$user = $this->createMock(IUser::class);
		$this->userSession->expects($this->once())
			->method('getUser')
			->will($this->returnValue($user));

		$this->u2fManager->expects($this->once())
			->method('startRegistration')
			->with($this->equalTo($user))
			->will($this->returnValue([]));

		$this->assertEquals([], $this->controller->startRegister());
	}

	public function testFinishRegister() {
		$user = $this->createMock(IUser::class);
		$this->userSession->expects($this->once())
			->method('getUser')
			->will($this->returnValue($user));
		$registrationData = 'regData';
		$data = 'some data';

		$this->u2fManager->expects($this->once())
			->method('finishRegistration')
			->with($this->equalTo($user), $this->equalTo($registrationData), $this->equalTo($data));

		$this->controller->finishRegister($registrationData, $data);
	}

}
