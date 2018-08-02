<?php
/**
 * Created by PhpStorm.
 * User: christoph
 * Date: 31.07.18
 * Time: 08:11
 */

namespace OCA\TwoFactorU2F\Tests\Unit\Listener;

use OCA\TwoFactorU2F\Event\StateChanged;
use OCA\TwoFactorU2F\Listener\StateChangeRegistryUpdater;
use OCA\TwoFactorU2F\Provider\U2FProvider;
use OCA\TwoFactorU2F\Service\U2FManager;
use OCP\Authentication\TwoFactorAuth\IRegistry;
use OCP\IUser;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\EventDispatcher\Event;

class StateChangeRegistryUpdaterTest extends TestCase {

	/** @var IRegistry|PHPUnit_Framework_MockObject_MockObject */
	private $providerRegistry;

	/** @var U2FManager|PHPUnit_Framework_MockObject_MockObjec */
	private $manager;

	/** @var U2FProvider|PHPUnit_Framework_MockObject_MockObjec */
	private $provider;

	/** @var StateChangeRegistryUpdater */
	private $listener;

	protected function setUp() {
		parent::setUp();

		$this->providerRegistry = $this->createMock(IRegistry::class);
		$this->manager = $this->createMock(U2FManager::class);
		$this->provider = $this->createMock(U2FProvider::class);

		$this->listener = new StateChangeRegistryUpdater($this->providerRegistry, $this->manager, $this->provider);
	}

	public function testHandleGenericEvent() {
		$event = $this->createMock(Event::class);
		$this->providerRegistry->expects($this->never())
			->method('enableProviderFor');
		$this->providerRegistry->expects($this->never())
			->method('disableProviderFor');

		$this->listener->handle($event);
	}

	public function testHandleEnableFirstDevice() {
		$user = $this->createMock(IUser::class);
		$event = new StateChanged($user, true);
		$this->manager->expects($this->once())
			->method('getDevices')
			->willReturn([
				[
					'id' => 1,
					'name' => 'utf1',
				],
			]);
		$this->providerRegistry->expects($this->once())
			->method('enableProviderFor')
			->with(
				$this->provider,
				$user
			);

		$this->listener->handle($event);
	}

	public function testHandleEnableSecondDevice() {
		$user = $this->createMock(IUser::class);
		$event = new StateChanged($user, true);
		$this->manager->expects($this->once())
			->method('getDevices')
			->willReturn([
				[
					'id' => 1,
					'name' => 'utf1',
				],
				[
					'id' => 2,
					'name' => 'utf2',
				],
			]);
		$this->providerRegistry->expects($this->never())
			->method('enableProviderFor');

		$this->listener->handle($event);
	}

	public function testHandleDisableLastDevice() {
		$user = $this->createMock(IUser::class);
		$event = new StateChanged($user, false);
		$this->manager->expects($this->once())
			->method('getDevices')
			->willReturn([]);
		$this->providerRegistry->expects($this->once())
			->method('disableProviderFor')
			->with(
				$this->provider,
				$user
			);

		$this->listener->handle($event);
	}

	public function testHandleDisableWithRemainingDevices() {
		$user = $this->createMock(IUser::class);
		$event = new StateChanged($user, false);
		$this->manager->expects($this->once())
			->method('getDevices')
			->willReturn([
				[
					'id' => 2,
					'name' => 'utf2',
				],
			]);
		$this->providerRegistry->expects($this->never())
			->method('disableProviderFor')
			->with(
				$this->provider,
				$user
			);

		$this->listener->handle($event);
	}
}
