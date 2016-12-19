<?php

/**
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 * @copyright Copyright (c) 2016 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * Two-factor U2F
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\TwoFactorU2F\Tests\Unit\Activity;

use OCA\TwoFactorU2F\Activity\Setting;
use OCP\IL10N;
use Test\TestCase;

class SettingTest extends TestCase {

	private $l10n;

	/** @var Setting */
	private $setting;

	protected function setUp() {
		parent::setUp();

		$this->l10n = $this->createMock(IL10N::class);

		$this->setting = new Setting($this->l10n);
	}

	public function testAll() {
		$this->assertEquals(false, $this->setting->canChangeMail());
		$this->assertEquals(false, $this->setting->canChangeStream());
		$this->assertEquals('twofactor_u2f', $this->setting->getIdentifier());
		$this->l10n->expects($this->once())
			->method('t')
			->with('U2F device')
			->willReturn('U2F Gerät');
		$this->assertEquals('U2F Gerät', $this->setting->getName());
		$this->assertEquals(30, $this->setting->getPriority());
		$this->assertEquals(true, $this->setting->isDefaultEnabledMail());
		$this->assertEquals(true, $this->setting->isDefaultEnabledStream());
	}

}
