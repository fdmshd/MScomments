<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\NonUniqueResultException;
/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findComments($page=1,$limit=10,$object_id,$object_name,$order){
        $query = $this->createQueryBuilder('c');
        $query->where('c.object_id=:object_id')
            ->setParameter('object_id',$object_id);
        $query->andWhere('c.object_name= :object_name')
            ->setParameter('object_name',$object_name);
        $query->orderBy('c.date',$order);
        $query->setMaxResults($limit);
        $query->setFirstResult(($limit*$page)-$limit);

        return new Paginator($query);
    }
    /**
     * @throws NonUniqueResultException
     */
    public function countComments(){
        $query = $this->createQueryBuilder('p')->select('count (p.id)');
        return $query->getQuery()->getOneOrNullResult();
    }
    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
