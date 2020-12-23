<?php

declare(strict_types = 1);

/**
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 * @copyright Copyright (c) 2018 Christoph Wurst <christoph@winzerhof-wurst.at>
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

namespace OCA\TwoFactorU2F\Activity;

use InvalidArgumentException;
use OCP\Activity\IEvent;
use OCP\Activity\IProvider;
use OCP\ILogger;
use OCP\IURLGenerator;
use OCP\L10N\IFactory as L10nFactory;

class Provider implements IProvider {

	/** @var L10nFactory */
	private $l10n;

	/** @var IURLGenerator */
	private $urlGenerator;

	/** @var ILogger */
	private $logger;

	/**
	 * @param L10nFactory $l10n
	 * @param IURLGenerator $urlGenerator
	 * @param ILogger $logger
	 */
	public function __construct(L10nFactory $l10n, IURLGenerator $urlGenerator, ILogger $logger) {
		$this->logger = $logger;
		$this->urlGenerator = $urlGenerator;
		$this->l10n = $l10n;
	}

	/**
	 * @param string $language
	 * @param IEvent $event
	 * @param IEvent $previousEvent
	 * @return IEvent
	 * @throws InvalidArgumentException
	 */
	public function parse($language, IEvent $event, IEvent $previousEvent = null) {
		if ($event->getApp() !== 'twofactor_u2f') {
			throw new InvalidArgumentException();
		}

		$l = $this->l10n->get('twofactor_u2f', $language);

		$event->setIcon($this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath('core', 'actions/password.svg')));
		switch ($event->getSubject()) {
			case 'u2f_device_added':
				$event->setSubject($l->t('You added an U2F hardware token'));
				break;
			case 'u2f_device_removed':
				$event->setSubject($l->t('You removed an U2F hardware token'));
				break;
			case 'u2f_disabled_by_admin':
				$event->setSubject($l->t('U2F disabled by an admin'));
				break;
		}
		return $event;
	}
}
