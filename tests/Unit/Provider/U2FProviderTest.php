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

namespace OCA\TwoFactorU2F\Tests\Unit\Provider;

use OCA\TwoFactorU2F\Provider\U2FProvider;
use OCA\TwoFactorU2F\Service\U2FManager;
use OCA\TwoFactorU2F\Settings\Personal;
use OCP\IL10N;
use OCP\IUser;
use OCP\Template;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class U2FProviderTest extends TestCase {

	/** @var IL10N|MockObject */
	private $l10n;

	/** @var U2FManager|MockObject */
	private $manager;

	/** @var U2FProvider */
	private $provider;

	protected function setUp() {
		parent::setUp();

		$this->l10n = $this->createMock(IL10N::class);
		$this->manager = $this->createMock(U2FManager::class);

		$this->provider = new U2FProvider($this->l10n, $this->manager);
	}

	public function testGetId() {
		$this->assertSame('u2f', $this->provider->getId());
	}

	public function testGetDisplayName() {
		$this->assertSame('U2F device', $this->provider->getDisplayName());
	}

	public function testGetDescription() {
		$this->l10n->expects($this->once())
			->method('t')
			->with('Authenticate with an U2F device')
			->willReturn('translated');

		$this->assertSame('translated', $this->provider->getDescription());
	}

	public function testGetTemplate() {
		$user = $this->createMock(IUser::class);
		$this->manager->expects($this->once())
			->method('startAuthenticate')
			->willReturn([]);

		$tmpl = new Template('twofactor_u2f', 'challenge');
		$tmpl->assign('reqs', []);

		$actual = $this->provider->getTemplate($user);
		$this->assertEquals($tmpl, $actual);
		$actual->fetchPage();
	}

	public function testVerifyChallenge() {
		$user = $this->createMock(IUser::class);
		$val = '123';

		$this->manager->expects($this->once())
			->method('finishAuthenticate')
			->willReturn(false);

		$this->assertFalse($this->provider->verifyChallenge($user, $val));
	}

	public function testIsTwoFactorAuthEnabledForUser() {
		$user = $this->createMock(IUser::class);
		$devices = [
			'dev1',
		];

		$this->manager->expects($this->once())
			->method('getDevices')
			->willReturn($devices);

		$this->assertTrue($this->provider->isTwoFactorAuthEnabledForUser($user));
	}

	public function testIsTwoFactorAuthDisabledForUser() {
		$user = $this->createMock(IUser::class);
		$devices = [];

		$this->manager->expects($this->once())
			->method('getDevices')
			->willReturn($devices);

		$this->assertFalse($this->provider->isTwoFactorAuthEnabledForUser($user));
	}

	public function testGetGetLightIcon() {
		$expected = image_path('twofactor_u2f', 'app-dark.svg');

		$icon = $this->provider->getDarkIcon();

		$this->assertEquals($expected, $icon);
	}

	public function testGetDarkIcon() {
		$expected = image_path('twofactor_u2f', 'app-dark.svg');

		$icon = $this->provider->getDarkIcon();

		$this->assertEquals($expected, $icon);
	}

	public function testGetPersonalSettings() {
		$expected = new Personal([]);
		$user = $this->createMock(IUser::class);

		$settings = $this->provider->getPersonalSettings($user);

		$this->assertEquals($expected, $settings);
	}

}
