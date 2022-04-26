<?php

namespace App\Repository;

use App\Entity\Rentabilite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<Rentabilite>
 *
 * @method Rentabilite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rentabilite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rentabilite[]    findAll()
 * @method Rentabilite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RentabiliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rentabilite::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Rentabilite $entity, bool $flush = true): void
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
    public function remove(Rentabilite $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Rentabilite[] Returns an array of Rentabilite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Rentabilite
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findOneBySomeDate(\DateTime $date): ?Rentabilite
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.date = :val')
            ->setParameter('val',date_format($date,'Y-m-d'))
            ->getQuery()
            ->getOneOrNullResult()
            ;

    }

}
