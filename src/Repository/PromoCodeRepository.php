<?php

namespace App\Repository;

use App\Entity\PromoCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PromoCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromoCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromoCode[]    findAll()
 * @method PromoCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoCode::class);
    }

    /**
    * Function to check if a promo code is valid 
    */
    public function getCodeFromTextIfValid($code)
    {   
        $codeArray = $this->createQueryBuilder('c')
        ->andWhere('c.code = :code')
        ->andWhere('c.endDate >= :endDate')
        ->andWhere('c.maxTimesUsed > c.timesUsed OR c.maxTimesUsed IS NULL')
        ->setParameter('code', $code)
        ->setParameter('endDate', new \DateTime())
        ->getQuery()
        ->getResult();

        if(count($codeArray) > 0 ){
           $codeArray = $codeArray[0];
        }
        return $codeArray;
    } 
}
