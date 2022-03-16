<?php

namespace App\Repository;

use App\Entity\Config;
use App\Entity\Log;
use App\Service\Log\SearchLogDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    use RepositoryHelperTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Log $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Log $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function persist(Log $log)
    {
        $em = $this->getEntityManager();
        $em->persist($log);
    }

    public function flush()
    {
        $em = $this->getEntityManager();
        $em->flush();
    }

    /**
     * @param SearchLogDto $searchLogDto
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCount(
        SearchLogDto $searchLogDto
    ): int
    {
        $qb = $this->createQueryBuilder('n')
            ->select('count(n.serviceName) as count');

        if ($searchLogDto->serviceNames) {
            $qb->andWhere(
                $qb->expr()->in('n.serviceName', ':servicesNames')
            )->setParameter('servicesNames', $searchLogDto->serviceNames);
        }
        if ($searchLogDto->statusCode) {
            $qb->andWhere('n.statusCode = :status')
                ->setParameter('status', $searchLogDto->statusCode);
        }
        if ($searchLogDto->startDate && $searchLogDto->endDate) {
            $qb->andWhere('n.logDate BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', $searchLogDto->startDate)
                ->setParameter('endDate', $searchLogDto->endDate);
        } else if ($searchLogDto->startDate) {
            $qb->andWhere('n.logDate = :logDate')
                ->setParameter('logDate', $searchLogDto->startDate);
        }
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result['count'];
    }
}
