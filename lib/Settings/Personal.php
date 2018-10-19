<?php

declare(strict_types=1);

/**
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
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

namespace OCA\TwoFactorU2F\Settings;

use function json_encode;
use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;
use OCP\Template;

class Personal implements IPersonalProviderSettings {

	/** @var array */
	private $devices;

	public function __construct(array $devices) {
		$this->devices = $devices;
	}

	public function getBody(): Template {
		$template = new Template('twofactor_u2f', 'personal');
		$template->assign('state', json_encode($this->devices));
		return $template;
	}
}
