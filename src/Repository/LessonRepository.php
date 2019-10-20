<?php

namespace App\Repository;

use App\Entity\Lesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Lesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lesson[]    findAll()
 * @method Lesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    /**
     * Function to get level names
     */
    public function getLevels(){
        $sql = "SELECT DISTINCT(l.level)
                FROM lesson l
                ORDER BY l.level ";
        $queryRecords = $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll(\PDO::FETCH_ASSOC);
        return $queryRecords;
    }

    /**
     * Function to get available lessons for a certain day and level
     */
    public function getAvailableLessonsByLevel($level, $date, $user){
        $sql = "SELECT s.id, l.description, l.level, s.teacher_name, CAST(s.date AS time) as time
                FROM schedule s 
                JOIN lesson l ON l.id = s.lesson_id
                LEFT JOIN reservation r0 ON s.id = r0.schedule_id AND r0.user_id = '".$user."'
                WHERE date_format(s.date,'%Y-%m-%d') = date_format('".$date."','%Y-%m-%d')
                AND l.level = '".$level."'
                AND r0.id IS NULL
                AND (SELECT COUNT(r.id) from reservation r where r.schedule_id = s.id ) < l.attendants ";

        $queryRecords = $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll(\PDO::FETCH_ASSOC);
        return $queryRecords;
    }


}
