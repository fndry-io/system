<?php

namespace Foundry\System\Repositories;

use Doctrine\ORM\QueryBuilder;
use Foundry\Core\Repositories\EntityRepository;
use Foundry\System\Entities\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository extends EntityRepository {

	public function getAlias(): string {
		return 'u';
	}

	public function findByEmail(string $email, int $perPage = 20) : LengthAwarePaginator
	{
		return $this->filter(function(QueryBuilder $query) use ($email){
			$query->where('u.email LIKE :email')
			      ->setParameter('email', "$email");
		}, $perPage);
	}

	public function getLabelList($name, $limit = 20) {

		$qb = $this->query();
		$qb->select('u.id', 'u.username, u.display_name');
		$qb->where($qb->expr()->orX(
			$qb->expr()->like('u.username', ':name'),
			$qb->expr()->like('u.display_name', ':name'),
			$qb->expr()->like('u.email', ':name')
		));
		$qb->setParameter('name', "%$name%");
		$qb->setMaxResults($limit);

		return $qb->getQuery()->getArrayResult();
	}


}