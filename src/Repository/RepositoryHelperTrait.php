<?php

namespace App\Repository;

trait RepositoryHelperTrait
{
    public function deleteAll()
    {
        return $this->createQueryBuilder('c')
            ->delete()
            ->getQuery()
            ->execute();
    }

}