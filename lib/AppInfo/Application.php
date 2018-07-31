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

namespace OCA\TwoFactorU2F\AppInfo;

use OCA\TwoFactorU2F\Event\StateChanged;
use OCA\TwoFactorU2F\Listener\IListener;
use OCA\TwoFactorU2F\Listener\StateChangeActivity;
use OCP\AppFramework\App;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Application extends App {

	public function __construct(array $urlParams = []) {
		parent::__construct('twofactor_u2f', $urlParams);

		$container = $this->getContainer();
		/** @var EventDispatcherInterface $eventDispatcher */
		$eventDispatcher = $container->getServer()->getEventDispatcher();
		$eventDispatcher->addListener(StateChanged::class, function (StateChanged $event) use ($container) {
			/** @var IListener[] $listeners */
			$listeners = [
				$container->query(StateChangeActivity::class),
			];

			foreach ($listeners as $listener) {
				$listener->handle($event);
			}
		});
	}

}
