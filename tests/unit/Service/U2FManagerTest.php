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

namespace OCA\TwoFactorU2F\Tests\Unit\Service;

use OCA\TwoFactorU2F\Db\Registration;
use OCA\TwoFactorU2F\Db\RegistrationMapper;
use OCA\TwoFactorU2F\Service\U2FManager;
use OCP\Activity\IEvent;
use OCP\Activity\IManager;
use OCP\ILogger;
use OCP\IRequest;
use OCP\ISession;
use OCP\IUser;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use u2flib_server\U2F;

class U2FManagerTest extends PHPUnit_Framework_TestCase {

	/** @var RegistrationMapper|PHPUnit_Framework_MockObject_MockObject */
	private $mapper;

	/** @var ISession|PHPUnit_Framework_MockObject_MockObject */
	private $session;

	/** @var ILogger|PHPUnit_Framework_MockObject_MockObject */
	private $logger;

	/** @var IRequest|PHPUnit_Framework_MockObject_MockObject */
	private $request;

	/** @var IManager|PHPUnit_Framework_MockObject_MockObject */
	private $activityManager;

	/** @var U2FManager */
	private $manager;

	protected function setUp() {
		parent::setUp();

		$this->mapper = $this->createMock(RegistrationMapper::class);
		$this->session = $this->createMock(ISession::class);
		$this->logger = $this->createMock(ILogger::class);
		$this->request = $this->createMock(IRequest::class);
		$this->activityManager = $this->createMock(IManager::class);

		$this->manager = new U2FManager($this->mapper, $this->session, $this->logger, $this->request, $this->activityManager);
	}

	/**
	 * @return U2F
	 */
	private function getU2f() {
		$this->request->expects($this->once())
			->method('getServerProtocol')
			->willReturn('https');
		$this->request->expects($this->once())
			->method('getServerHost')
			->willReturn('cloud.example.com');
		return new U2F('https://cloud.example.com');
	}

	/**
	 * @param IUser $user
	 * @param int $nr
	 */
	private function mockRegistrations(IUser $user, $nr) {
		$regs = [];
		for ($i = 0; $i < $nr; $i++) {
			$reg = new Registration();
			array_push($regs, $reg);
		}
		$this->mapper->expects($this->once())
			->method('findRegistrations')
			->with($this->equalTo($user))
			->willReturn($regs);
	}

	public function testGetDevices() {
		$user = $this->createMock(IUser::class);
		$this->mockRegistrations($user, 2);

		$this->assertCount(2, $this->manager->getDevices($user));
	}

	public function testGetNoDevices() {
		$user = $this->createMock(IUser::class);
		$this->mockRegistrations($user, 0);

		$this->assertEmpty($this->manager->getDevices($user));
	}

	public function testDisableU2F() {
		$user = $this->createMock(IUser::class);
		$event = $this->createMock(IEvent::class);
		$reg = $this->createMock(Registration::class);

		$this->mapper->expects($this->once())
			->method('findRegistration')
			->with($user, 13)
			->willReturn($reg);
		$this->mapper->expects($this->once())
			->method('delete')
			->with($reg);
		$this->activityManager->expects($this->once())
			->method('generateEvent')
			->willReturn($event);
		$event->expects($this->once())
			->method('setApp')
			->with($this->equalTo('twofactor_u2f'))
			->willReturnSelf();
		$event->expects($this->once())
			->method('setType')
			->with($this->equalTo('security'))
			->willReturnSelf();
		$user->expects($this->any())
			->method('getUID')
			->willReturn('ursula');
		$event->expects($this->once())
			->method('setAuthor')
			->with($this->equalTo('ursula'))
			->willReturnSelf();
		$event->expects($this->once())
			->method('setAffectedUser')
			->with($this->equalTo('ursula'))
			->willReturnSelf();
		$event->expects($this->once())
			->method('setSubject')
			->with($this->equalTo('u2f_device_removed'))
			->willReturnSelf();
		$this->activityManager->expects($this->once())
			->method('publish')
			->with($this->equalTo($event));

		$this->manager->removeDevice($user, 13);
	}

	public function testStartRegistrationFirstDevice() {
		$user = $this->createMock(IUser::class);
		$this->mockRegistrations($user, 0);

		$this->session->expects($this->once())
			->method('set');

		$this->manager->startRegistration($user);
	}

	public function testFinishRegistration() {
		// TODO: get a grasp of how the u2f lib works and feed it with
		// realistic data or mock it.
	}

	public function testStartAuthenticate() {
		// TODO: get a grasp of how the u2f lib works and feed it with
		// realistic data or mock it.
	}

	public function testFinishAuthenticate() {
		// TODO: get a grasp of how the u2f lib works and feed it with
		// realistic data or mock it.
	}

}
