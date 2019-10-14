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

use OCA\TwoFactorU2F\Provider\U2FLoginProvider;
use OCA\TwoFactorU2F\Provider\U2FProvider;
use OCA\TwoFactorU2F\Service\U2FManager;
use OCA\TwoFactorU2F\Settings\Personal;
use OCP\AppFramework\IAppContainer;
use OCP\IInitialStateService;
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

	/** @var IAppContainer|MockObject */
	private $container;

	/** @var IInitialStateService|MockObject */
	private $initialState;

	/** @var U2FProvider */
	private $provider;

	protected function setUp() {
		parent::setUp();

		$this->l10n = $this->createMock(IL10N::class);
		$this->manager = $this->createMock(U2FManager::class);
		$this->container = $this->createMock(IAppContainer::class);
		$this->initialState = $this->createMock(IInitialStateService::class);

		$this->provider = new U2FProvider(
			$this->l10n,
			$this->manager,
			$this->container,
			$this->initialState
		);
	}

	public function testGetId() {
		$this->assertSame('u2f', $this->provider->getId());
	}

	public function testGetDisplayName() {
		$this->l10n->expects($this->once())
			->method('t')
			->with('U2F device')
			->willReturn('translated');

		$displayName = $this->provider->getDisplayName();

		$this->assertSame('translated', $displayName);
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
		$expected = new Personal();
		$this->initialState->expects($this->once())
			->method('provideInitialState')
			->with(
				'twofactor_u2f',
				'devices',
				['my', 'devices']
			);

		$user = $this->createMock(IUser::class);
		$this->manager->method('getDevices')
			->willReturn(['my', 'devices']);

		$settings = $this->provider->getPersonalSettings($user);

		$this->assertEquals($expected, $settings);
	}

	public function testDisable() {
		$user = $this->createMock(IUser::class);
		$this->manager->expects($this->once())
			->method('removeAllDevices')
			->with($user);

		$this->provider->disableFor($user);
	}

	public function testGet() {
		$user = $this->createMock(IUser::class);
		$loginProvider = $this->createMock(U2FLoginProvider::class);
		$this->container->expects($this->once())
			->method('query')
			->with(U2FLoginProvider::class)
			->willReturn($loginProvider);

		$result = $this->provider->getLoginSetup($user);

		$this->assertSame($loginProvider, $result);
	}

}
