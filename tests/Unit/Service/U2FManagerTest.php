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
use OCA\TwoFactorU2F\Event\StateChanged;
use OCA\TwoFactorU2F\Service\U2FManager;
use OCP\Activity\IEvent;
use OCP\ILogger;
use OCP\IRequest;
use OCP\ISession;
use OCP\IUser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class U2FManagerTest extends TestCase {

	/** @var RegistrationMapper|MockObject */
	private $mapper;

	/** @var ISession|MockObject */
	private $session;

	/** @var ILogger|MockObject */
	private $logger;

	/** @var IRequest|MockObject */
	private $request;

	/** @var U2FManager */
	private $manager;

	/** @var EventDispatcherInterface */
	private $eventDispatcher;

	protected function setUp() {
		parent::setUp();

		$this->mapper = $this->createMock(RegistrationMapper::class);
		$this->session = $this->createMock(ISession::class);
		$this->logger = $this->createMock(ILogger::class);
		$this->request = $this->createMock(IRequest::class);
		$this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

		$this->manager = new U2FManager($this->mapper, $this->session, $this->logger, $this->request, $this->eventDispatcher);
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
		$reg = $this->createMock(Registration::class);
		$this->mapper->expects($this->once())
			->method('findRegistration')
			->with($user, 13)
			->willReturn($reg);
		$this->mapper->expects($this->once())
			->method('delete')
			->with($reg);
		$this->eventDispatcher->expects($this->once())
			->method('dispatch')
			->with(
				$this->equalTo(StateChanged::class),
				$this->equalTo(new StateChanged($user, false))
			);

		$this->manager->removeDevice($user, 13);
	}

	public function testStartRegistrationFirstDevice() {
		$user = $this->createMock(IUser::class);
		$this->mockRegistrations($user, 0);

		$this->session->expects($this->once())
			->method('set');

		$this->manager->startRegistration($user);
	}

}
