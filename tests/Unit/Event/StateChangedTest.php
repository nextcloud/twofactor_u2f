<?php

declare(strict_types=1);

/**
 * Nextcloud - U2F 2FA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 * @copyright Christoph Wurst 2018
 */

namespace OCA\TwoFactorU2F\Tests\Unit\Event;

use OCA\TwoFactorU2F\Event\StateChanged;
use OCP\IUser;
use PHPUnit\Framework\TestCase;

class StateChangedTest extends TestCase {

	public function testEnabledState() {
		$user = $this->createMock(IUser::class);

		$event = new StateChanged($user, true);

		$this->assertTrue($event->isEnabled());
		$this->assertSame($user, $event->getUser());
	}

	public function testDisabledState() {
		$user = $this->createMock(IUser::class);

		$event = new StateChanged($user, false);

		$this->assertFalse($event->isEnabled());
		$this->assertSame($user, $event->getUser());
	}

}
