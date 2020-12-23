<?php

declare(strict_types = 1);

/**
 * Nextcloud - U2F 2FA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 * @copyright Christoph Wurst 2018
 */

namespace OCA\TwoFactorU2F\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getKeyHandle()
 * @method void setKeyHandle(string $keyHandle)
 * @method string getPublicKey()
 * @method void setPublicKey(string $publicKey)
 * @method string getCertificate()
 * @method void setCertificate(string $Certificate)
 * @method int getCounter()
 * @method void setCounter(int $counter)
 * @method string getName()
 * @method void setName(string $name)
 */
class Registration extends Entity implements JsonSerializable {
	protected $userId;
	protected $keyHandle;
	protected $publicKey;
	protected $certificate;
	protected $counter;
	protected $name;

	public function jsonSerialize(): array {
		return [
			'id' => $this->getId(),
			'userId' => $this->getUserId(),
			'keyHandle' => $this->getKeyHandle(),
			'publicKey' => $this->getPublicKey(),
			'certificate' => $this->getCertificate(),
			'counter' => $this->getCounter(),
			'name' => $this->getName(),
		];
	}
}
