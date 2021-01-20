<?php

namespace OCA\TwoFactorU2F\Tests\Unit\Listener;

use OCA\TwoFactorU2F\Event\DisabledByAdmin;
use OCA\TwoFactorU2F\Event\StateChanged;
use OCA\TwoFactorU2F\Listener\StateChangeActivity;
use OCP\Activity\IEvent;
use OCP\Activity\IManager;
use OCP\EventDispatcher\Event;
use OCP\IUser;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class StateChangeActivityTest extends TestCase {

	/** @var IManager|PHPUnit_Framework_MockObject_MockObject */
	private $activityManager;

	/** @var StateChangeActivity */
	private $listener;

	protected function setUp(): void {
		parent::setUp();

		$this->activityManager = $this->createMock(IManager::class);

		$this->listener = new StateChangeActivity($this->activityManager);
	}

	public function testHandleGenericEvent() {
		$event = new Event();
		$this->activityManager->expects($this->never())
			->method('publish');

		$this->listener->handle($event);
	}

	public function testHandleEnableEvent() {
		$user = $this->createMock(IUser::class);
		$event = new StateChanged($user, true);
		$activityEvent = $this->createMock(IEvent::class);
		$this->activityManager->expects($this->once())
			->method('generateEvent')
			->willReturn($activityEvent);
		$activityEvent->expects($this->once())
			->method('setApp')
			->with($this->equalTo('twofactor_u2f'))
			->willReturnSelf();
		$activityEvent->expects($this->once())
			->method('setType')
			->with($this->equalTo('security'))
			->willReturnSelf();
		$user->expects($this->any())
			->method('getUID')
			->willReturn('ursula');
		$activityEvent->expects($this->once())
			->method('setAuthor')
			->with($this->equalTo('ursula'))
			->willReturnSelf();
		$activityEvent->expects($this->once())
			->method('setAffectedUser')
			->with($this->equalTo('ursula'))
			->willReturnSelf();
		$activityEvent->expects($this->once())
			->method('setSubject')
			->with($this->equalTo('u2f_device_added'))
			->willReturnSelf();
		$this->activityManager->expects($this->once())
			->method('publish')
			->with($this->equalTo($activityEvent));

		$this->listener->handle($event);
	}

	public function testHandleDisableEvent() {
		$user = $this->createMock(IUser::class);
		$event = new StateChanged($user, false);
		$activityEvent = $this->createMock(IEvent::class);
		$this->activityManager->expects($this->once())
			->method('generateEvent')
			->willReturn($activityEvent);
		$activityEvent->expects($this->once())
			->method('setApp')
			->with($this->equalTo('twofactor_u2f'))
			->willReturnSelf();
		$activityEvent->expects($this->once())
			->method('setType')
			->with($this->equalTo('security'))
			->willReturnSelf();
		$user->expects($this->any())
			->method('getUID')
			->willReturn('ursula');
		$activityEvent->expects($this->once())
			->method('setAuthor')
			->with($this->equalTo('ursula'))
			->willReturnSelf();
		$activityEvent->expects($this->once())
			->method('setAffectedUser')
			->with($this->equalTo('ursula'))
			->willReturnSelf();
		$activityEvent->expects($this->once())
			->method('setSubject')
			->with($this->equalTo('u2f_device_removed'))
			->willReturnSelf();
		$this->activityManager->expects($this->once())
			->method('publish')
			->with($this->equalTo($activityEvent));

		$this->listener->handle($event);
	}

	public function testHandleDisabledByAdminEvent() {
		$uid = 'user234';
		$user = $this->createMock(IUser::class);
		$user->method('getUID')->willReturn($uid);
		$event = new DisabledByAdmin($user);
		$activityEvent = $this->createMock(IEvent::class);
		$this->activityManager->expects($this->once())
			->method('generateEvent')
			->willReturn($activityEvent);
		$activityEvent->expects($this->once())
			->method('setApp')
			->with('twofactor_u2f')
			->willReturnSelf();
		$activityEvent->expects($this->once())
			->method('setType')
			->with('security')
			->willReturnSelf();
		$activityEvent->expects($this->once())
			->method('setAuthor')
			->with($uid)
			->willReturnSelf();
		$activityEvent->expects($this->once())
			->method('setAffectedUser')
			->with($uid)
			->willReturnSelf();
		$activityEvent->expects($this->once())
			->method('setSubject')
			->with($this->equalTo('u2f_disabled_by_admin'))
			->willReturnSelf();
		$this->activityManager->expects($this->once())
			->method('publish')
			->with($activityEvent);

		$this->listener->handle($event);
	}
}
