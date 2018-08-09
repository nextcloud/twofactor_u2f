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

namespace OCA\TwoFactorU2F\Listener;

use OCA\TwoFactorU2F\Event\StateChanged;
use OCA\TwoFactorU2F\Provider\U2FProvider;
use OCA\TwoFactorU2F\Service\U2FManager;
use OCP\Authentication\TwoFactorAuth\IRegistry;
use Symfony\Component\EventDispatcher\Event;

class StateChangeRegistryUpdater implements IListener {

	/** @var IRegistry */
	private $providerRegistry;

	/** @var U2FManager */
	private $manager;

	/** @var U2FProvider */
	private $provider;

	public function __construct(IRegistry $providerRegistry, U2FManager $manager, U2FProvider $provider) {
		$this->providerRegistry = $providerRegistry;
		$this->provider = $provider;
		$this->manager = $manager;
	}

	public function handle(Event $event) {
		if ($event instanceof StateChanged) {
			$devices = $this->manager->getDevices($event->getUser());
			if ($event->isEnabled() && count($devices) === 1) {
				// The first device was enabled -> enable provider for this user
				$this->providerRegistry->enableProviderFor($this->provider, $event->getUser());
			} else if (!$event->isEnabled() && empty($devices)) {
				// The last device was removed -> disable provider for this user
				$this->providerRegistry->disableProviderFor($this->provider, $event->getUser());
			}
		}
	}
}