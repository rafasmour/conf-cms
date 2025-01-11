<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

   public function findOneBySlug(string $slug): ?Page
   {
        return $this->createQueryBuilder('page')
           ->andWhere('page.slug = :slug')
           ->setParameter('slug', $slug)
           ->getQuery()
           ->getOneOrNullResult()
       ;
       
   }
}
