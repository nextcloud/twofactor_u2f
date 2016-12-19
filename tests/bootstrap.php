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

require_once __DIR__.'/../../../lib/base.php';
require_once __DIR__.'/../vendor/autoload.php';

OC::$loader->addValidRoot(OC::$SERVERROOT . '/tests');
OC_App::loadApp('twofactor_u2f');
