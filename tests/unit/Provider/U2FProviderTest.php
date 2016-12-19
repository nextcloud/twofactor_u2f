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
use OCP\IL10N;
use OCP\IUser;
use OCP\Template;
use PHPUnit_Framework_MockObject_MockObject;
use Test\TestCase;

class U2FProviderTest extends TestCase {

	/** @var IL10N|PHPUnit_Framework_MockObject_MockObject */
	private $l10n;

	/** @var U2FManager|PHPUnit_Framework_MockObject_MockObject */
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

		$this->manager->expects($this->once())
			->method('isEnabled')
			->willReturn(false);

		$this->assertFalse($this->provider->isTwoFactorAuthEnabledForUser($user));
	}

}
