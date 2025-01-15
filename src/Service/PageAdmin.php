<?php
namespace App\Service;

use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;

class PageAdmin {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function getAll(): array {
        return $this->entityManager->getRepository(  Page::class)->getAll();
    }
    public function new(Page $page): void{
        $this->entityManager->persist($page);
        $this->entityManager->flush();
    }
    
    public function delete(Page $page): void{
        $this->entityManager->remove($page);
        $this->entityManager->flush();
    }
}