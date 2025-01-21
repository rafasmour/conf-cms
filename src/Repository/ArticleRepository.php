<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

   public function findOneBySlug(string $slug): ?Article
   {    
        return $this->createQueryBuilder('Article')
           ->andWhere('Article.slug = :slug')
           ->setParameter('slug', $slug)
           ->getQuery()
           ->getOneOrNullResult()
       ;
       
   }
}
