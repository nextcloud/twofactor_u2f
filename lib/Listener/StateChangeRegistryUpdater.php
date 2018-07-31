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
use OCP\Authentication\TwoFactorAuth\IRegistry;
use Symfony\Component\EventDispatcher\Event;

class StateChangeRegistryUpdater implements IListener {

	/** @var IRegistry */
	private $providerRegistry;

	/** @var U2FProvider */
	private $provider;

	public function __construct(IRegistry $providerRegistry, U2FProvider $provider) {
		$this->providerRegistry = $providerRegistry;
		$this->provider = $provider;
	}

	public function handle(Event $event) {
		if ($event instanceof StateChanged) {
			if ($event->isEnabled()) {
				$this->providerRegistry->enableProviderFor($this->provider, $event->getUser());
			} else {
				$this->providerRegistry->disableProviderFor($this->provider, $event->getUser());
			}
		}
	}
}