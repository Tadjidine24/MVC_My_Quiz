<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    // /**
    //  * @return Quiz[] Returns an array of Quiz objects
    //  */
    
    // public function findByExampleField($value)
    // {
    //     return $this->createQueryBuilder('q')
    //         ->andWhere('q.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->orderBy('q.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    // public function find($id, $lockMode = null, $lockVersion = null){
    //     return $this->_em->find($this->_entityName, $id, $lockMode, $lockVersion);
    // }

    // public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    // {
    //     $persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);
    //     return $persister->loadAll($criteria, $orderBy, $limit, $offset);
    // }

    // public function findAll()
    // {
    //     return $this->findBy([]);
    // }

    // public function findOneBy(array $criteria, array $orderBy = null)
    // {
    //     $persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);
    //     return $persister->load($criteria, null, null, [], null, 1, $orderBy);
    // }
    

    
    // public function findOneBySomeField($value): ?Quiz
    // {
    //     return $this->createQueryBuilder('q')
    //         ->andWhere('q.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }

    public function findByQuestion(){
        $qb = $this->createQueryBuilder('question');
        $qb->select('question, categorie.name')
            ->innerJoin('App\Repository\CategorieRepository','ON' ,'question.id_categorie = categorie.id')
            ->groupBy('categorie.id');
    
        return $qb->getQuery()->getResult();
    }
}
