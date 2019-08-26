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

namespace OCA\TwoFactorU2F\Provider;

use OCA\TwoFactorU2F\Service\U2FManager;
use OCA\TwoFactorU2F\Settings\Personal;
use OCP\AppFramework\IAppContainer;
use OCP\Authentication\TwoFactorAuth\IActivatableAtLogin;
use OCP\Authentication\TwoFactorAuth\IDeactivatableByAdmin;
use OCP\Authentication\TwoFactorAuth\ILoginSetupProvider;
use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;
use OCP\Authentication\TwoFactorAuth\IProvidesIcons;
use OCP\Authentication\TwoFactorAuth\IProvidesPersonalSettings;
use OCP\IL10N;
use OCP\IUser;
use OCP\Template;

class U2FProvider implements IActivatableAtLogin, IProvidesIcons, IProvidesPersonalSettings, IDeactivatableByAdmin {

	/** @var IL10N */
	private $l10n;

	/** @var U2FManager */
	private $manager;

	/** @var IAppContainer */
	private $container;

	public function __construct(IL10N $l10n,
								U2FManager $manager,
								IAppContainer $container) {
		$this->l10n = $l10n;
		$this->manager = $manager;
		$this->container = $container;
	}

	/**
	 * Get unique identifier of this 2FA provider
	 */
	public function getId(): string {
		return 'u2f';
	}

	/**
	 * Get the display name for selecting the 2FA provider
	 */
	public function getDisplayName(): string {
		return $this->l10n->t('U2F device');
	}

	/**
	 * Get the description for selecting the 2FA provider
	 */
	public function getDescription(): string {
		return $this->l10n->t('Authenticate with an U2F device');
	}

	/**
	 * Get the template for rending the 2FA provider view
	 */
	public function getTemplate(IUser $user): Template {
		$reqs = $this->manager->startAuthenticate($user);

		$tmpl = new Template('twofactor_u2f', 'challenge');
		$tmpl->assign('reqs', $reqs);
		return $tmpl;
	}

	/**
	 * Verify the given challenge
	 */
	public function verifyChallenge(IUser $user, string $challenge): bool {
		return $this->manager->finishAuthenticate($user, $challenge);
	}

	/**
	 * Decides whether 2FA is enabled for the given user
	 */
	public function isTwoFactorAuthEnabledForUser(IUser $user): bool {
		return count($this->manager->getDevices($user)) > 0;
	}

	public function getPersonalSettings(IUser $user): IPersonalProviderSettings {
		return new Personal($this->manager->getDevices($user));
	}

	public function getLightIcon(): String {
		return image_path('twofactor_u2f', 'app.svg');
	}

	public function getDarkIcon(): String {
		return image_path('twofactor_u2f', 'app-dark.svg');;
	}

	/**
	 * Disable this provider for the given user.
	 *
	 * @param IUser $user the user to deactivate this provider for
	 */
	public function disableFor(IUser $user) {
		$this->manager->removeAllDevices($user);
	}

	/**
	 * @param IUser $user
	 *
	 * @return ILoginSetupProvider
	 */
	public function getLoginSetup(IUser $user): ILoginSetupProvider {
		return $this->container->query(U2FLoginProvider::class);
	}

}
