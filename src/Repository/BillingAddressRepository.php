<?php

namespace App\Repository;

use App\Entity\BillingAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BillingAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method BillingAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method BillingAddress[]    findAll()
 * @method BillingAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BillingAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BillingAddress::class);
    }

}
