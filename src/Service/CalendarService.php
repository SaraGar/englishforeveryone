<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\Reservation;

class CalendarService
{

    private $em;
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }


    public function bookLesson($date, $scheduled_lesson_id){

        try {
            $scheduledLesson = $this->em->getRepository('App:schedule')->findOneById($scheduled_lesson_id);

            //Make the reservation
            $reservation = new Reservation();
            $reservation->setUser($this->security->getUser());
            $reservation->setDate(new \DateTime());
            $reservation->setSchedule($scheduledLesson);
            $this->em->persist($reservation);

            //Discount one lesson to the user's paidLessonsLeft
            $this->security->getUser()->setPaidLessonsLeft($this->security->getUser()->getPaidLessonsLeft()-1);
            $this->em->persist($this->security->getUser());

            $this->em->flush();   
        
            return ["code" => 200, "message" => 'Successfully saved'];

        } catch (\Throwable $th) {
            return ["code" => 500, "message" => 'An error occurred'];
        }
        
    }


}