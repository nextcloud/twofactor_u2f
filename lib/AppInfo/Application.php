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

use OCA\TwoFactorU2F\Event\DisabledByAdmin;
use OCA\TwoFactorU2F\Event\StateChanged;
use OCA\TwoFactorU2F\Listener\IListener;
use OCA\TwoFactorU2F\Listener\StateChangeActivity;
use OCA\TwoFactorU2F\Listener\StateChangeRegistryUpdater;
use OCP\AppFramework\App;
use OCP\EventDispatcher\IEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Application extends App {

	const APP_ID = 'twofactor_u2f';

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);

		$container = $this->getContainer();

		/** @var IEventDispatcher $eventDispatcher */
		$eventDispatcher = $container->query(IEventDispatcher::class);
		$eventDispatcher->addServiceListener(StateChanged::class, StateChangeActivity::class);
		$eventDispatcher->addServiceListener(StateChanged::class, StateChangeRegistryUpdater::class);
		$eventDispatcher->addServiceListener(DisabledByAdmin::class, StateChangeActivity::class);
	}

}
