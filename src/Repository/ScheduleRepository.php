<?php

namespace App\Repository;

use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Schedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Schedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Schedule[]    findAll()
 * @method Schedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    public function getAllFutureAvailableLessons(){
    
        $sql = "SELECT s.id, l.level, s.teacher_name as teacher, s.date as start, DATE_ADD(s.date, INTERVAL +50 MINUTE) as end 
                FROM schedule s 
                JOIN lesson l ON l.id = s.lesson_id
                WHERE (SELECT COUNT(r.id) from reservation r where r.schedule_id = s.id ) < l.attendants
                AND s.date > CURDATE() ";

        $queryRecords = $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll(\PDO::FETCH_ASSOC);
        return $queryRecords;    
    }

   
}
