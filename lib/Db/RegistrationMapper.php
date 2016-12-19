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

namespace OCA\TwoFactorU2F\Db;

use OCP\AppFramework\Db\Mapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IUser;

class RegistrationMapper extends Mapper {

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'twofactor_u2f_registrations');
	}

	/**
	 * @param IUser $user
	 * @param int $id
	 * @return Registration
	 */
	public function findRegistration(IUser $user, $id) {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$qb->select('id', 'user_id', 'key_handle', 'public_key', 'certificate', 'counter')
			->from('twofactor_u2f_registrations')
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID())))
			->andWhere($qb->expr()->eq('id', $qb->createNamedParameter($id)));
		$result = $qb->execute();

		$row = $result->fetch();
		$result->closeCursor();

		return Registration::fromRow($row);
	}

	/**
	 * @param IUser $user
	 * @return Registration[]
	 */
	public function findRegistrations(IUser $user) {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$qb->select('id', 'user_id', 'key_handle', 'public_key', 'certificate', 'counter')
			->from('twofactor_u2f_registrations')
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID())));
		$result = $qb->execute();

		$rawRegistrations = $result->fetchAll();
		$result->closeCursor();

		$registrations = array_map(function ($row) {
			return Registration::fromRow($row);
		}, $rawRegistrations);

		return $registrations;
	}

}
