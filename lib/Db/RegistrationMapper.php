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

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IUser;

class RegistrationMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'twofactor_u2f_registrations');
	}

	/**
	 * @param IUser $user
	 * @param int $id
	 */
	public function findRegistration(IUser $user, $id): Registration {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$qb->select('id', 'user_id', 'key_handle', 'public_key', 'certificate', 'counter', 'name')
			->from('twofactor_u2f_registrations')
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID())))
			->andWhere($qb->expr()->eq('id', $qb->createNamedParameter($id)));
		return $this->findEntity($qb);
	}

	/**
	 * @param IUser $user
	 * @return Registration[]
	 */
	public function findRegistrations(IUser $user): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$qb->select('id', 'user_id', 'key_handle', 'public_key', 'certificate', 'counter', 'name')
			->from('twofactor_u2f_registrations')
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID())));
		return $this->findEntities($qb);
	}
}
