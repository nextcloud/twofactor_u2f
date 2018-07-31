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
use OCP\Authentication\TwoFactorAuth\IRegistry;
use OCP\IUser;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\EventDispatcher\Event;

class StateChangeRegistryUpdaterTest extends TestCase {

	/** @var IRegistry|PHPUnit_Framework_MockObject_MockObject */
	private $providerRegistry;

	/** @var U2FProvider|PHPUnit_Framework_MockObject_MockObjec */
	private $provider;

	/** @var StateChangeRegistryUpdater */
	private $listener;

	protected function setUp() {
		parent::setUp();

		$this->providerRegistry = $this->createMock(IRegistry::class);
		$this->provider = $this->createMock(U2FProvider::class);

		$this->listener = new StateChangeRegistryUpdater($this->providerRegistry, $this->provider);
	}

	public function testHandleGenericEvent() {
		$event = $this->createMock(Event::class);
		$this->providerRegistry->expects($this->never())
			->method('enableProviderFor');
		$this->providerRegistry->expects($this->never())
			->method('disableProviderFor');

		$this->listener->handle($event);
	}

	public function testHandleEnableEvent() {
		$user = $this->createMock(IUser::class);
		$event = new StateChanged($user, true);
		$this->providerRegistry->expects($this->once())
			->method('enableProviderFor')
			->with(
				$this->provider,
				$user
			);

		$this->listener->handle($event);
	}

	public function testHandleDisableEvent() {
		$user = $this->createMock(IUser::class);
		$event = new StateChanged($user, false);
		$this->providerRegistry->expects($this->once())
			->method('disableProviderFor')
			->with(
				$this->provider,
				$user
			);

		$this->listener->handle($event);
	}
}
