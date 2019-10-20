<?php

namespace App\Repository;

use App\Entity\InvoiceLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method InvoiceLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceLine[]    findAll()
 * @method InvoiceLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceLine::class);
    }

   
}
