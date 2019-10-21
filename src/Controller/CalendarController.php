<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Reservation;
use App\Entity\Schedule;
use App\Service\CalendarService;

class CalendarController extends Controller
{
     /**
     * @Route("/get_my_reservations", name="get_my_reservations")
     */
    public function getMyReservationsWithCalendarFormat(Request $request){

        if($this->getUser()){
            $em = $this->getDoctrine()->getManager();

            $reservations = $em->getRepository('App:Reservation')->findByUser($this->getUser());
            
            //Format the data as the calendar needs
            $responseReservation = [];
            foreach($reservations as $reservation){
                $dateEnd = clone($reservation->getSchedule()->getDate());
                $responseReservation[] = [
                    'id' => $reservation->getId(),
                    'title' => $reservation->getSChedule()->getLesson()->getDescription(),
                    'start' => $reservation->getSchedule()->getDate()->format('Y-m-d H:i:s'),
                    'end' => $dateEnd->modify('+50 minutes')->format('Y-m-d H:i:s')
                ];
            }
            return new JsonResponse($responseReservation);
            
        }else{
            return $this->redirectToRoute('app_login');
        }

    }

     /**
     * @Route("/cancel_reservation", name="cancel_reservation")
     */
    public function cancelReservation(Request $request){

        if($this->getUser()){
            $em = $this->getDoctrine()->getManager();
            $idReservation = $request->request->get('id_reservation');

            $reservation = $em->getRepository('App:Reservation')->findOneBy(['user' => $this->getUser(), 'id' => $idReservation]);
            if($reservation){
                //Check if it can be cancelled (it can't be a past date or fall in the next 24h)
                $now = new \DateTime();
                $diff = $now->diff($reservation->getSchedule()->getDate())->format('%h');

                if($reservation->getSchedule()->getDate() < $now || ($reservation->getSchedule()->getDate() < $now && $diff <= 24 )){
                    return new JsonResponse(["code" => 403, "message" => 'You can not cancel this lesson anymore!']);
                }

                $em->remove($reservation);
                $this->getUser()->setPaidLessonsLeft($this->getUser()->getPaidLessonsLeft()+1);
                $em->flush();
                return new JsonResponse(["code" => 200, "message" => 'Successfully canceled']);
            }else{
                return new JsonResponse(["code" => 404, "message" => 'Reservation not found']);
            }
            
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/get_scheduled_lesssons", name="get_scheduled_lesssons")
     */
    public function getAvailableLessonsByDateAndLevel(Request $request){

        if($this->getUser()){
            $em = $this->getDoctrine()->getManager();
            $level = $request->request->get('level');
            $date = $request->request->get('date');
            try {
               $scheduledLessons = $em->getRepository('App:Lesson')->getAvailableLessonsByDateAndLevel($level, $date, $this->getUser()->getId());
                if($scheduledLessons){               
                    return new JsonResponse(["code" => 200, "message" => 'Successfully obtained', "data" => ["scheduledLessons" => $scheduledLessons]]);
                }else{
                    return new JsonResponse(["code" => 404, "message" => 'No lessons found']);
                }
            } catch (\Throwable $th) {
                return new JsonResponse(["code" => 500, "message" => 'An error occurred']);
            }
            
        }else{
            return $this->redirectToRoute('app_login');
        }

    }

   /**
     * @Route("/get_all_future_lessons", name="get_all_future_lessons")
     */
    public function getAllFutureAvailableLessonsWithCalendarFormat(){
        
        if($this->getUser()){
            $em = $this->getDoctrine()->getManager();
            try {

                $schedules = $em->getRepository('App:Schedule')->getAllFutureAvailableLessons($this->getUser()->getUsername());
                //Format the data as the calendar needs
                $responseSchedule = [];
                foreach($schedules as $schedule){
                    $responseSchedule[] = [
                        'id' => $schedule['id'],
                        'title' => $schedule['level'].' - '.$schedule['teacher'],
                        'start' => $schedule['start'],
                        'end' => $schedule['end']
                    ];
                }
                return new JsonResponse($responseSchedule);
            } catch (\Throwable $th) {
              return new JsonResponse(["code" => 500, "message" => 'An error occurred']);
            }
            
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

     /**
     * @Route("/book_lessson", name="book_lessson")
     */
    public function bookLessonAction(Request $request, CalendarService $calendarService){

        if($this->getUser()){
            $em = $this->getDoctrine()->getManager();
            $date = $request->request->get('date');
            $scheduled_lesson_id = $request->request->get('lesson_id');
          
            $book_response = $calendarService->bookLesson($date, $scheduled_lesson_id);
            if($book_response && $book_response['code'] == 200){               
                return new JsonResponse(["code" => 200, "message" => 'Successfully saved']);
            }else{
                return new JsonResponse(["code" => 500, "message" => 'An error occurred']);
            }
            
        }else{
            return $this->redirectToRoute('app_login');
        }

    }

    
    /**
     * @Route("/check_lessons_left", name="check_lessons_left")
     */
    public function checkUserHasLessonsLeft(Request $request){

        if($this->getUser()){
            $em = $this->getDoctrine()->getManager();
            try {
               if(!empty($this->getUser()->getPaidLessonsLeft())){               
                    return new JsonResponse(["code" => 200, "message" => 'Successfully checked']);
                }else{
                    return new JsonResponse(["code" => 418, "message" => 'Successfully checked']);
                }
            } catch (\Throwable $th) {
                return new JsonResponse(["code" => 200, "message" => 'An error occurred']);
            }
            
        }else{
            return $this->redirectToRoute('app_login');
        }

    }

    /**
     * @Route("/teacher_get_lessons", name="teacher_get_lessons")
     */
    public function getTeacherLessonsWithCalendarFormat(Request $request){

        if($this->getUser()){
            $em = $this->getDoctrine()->getManager();

            $schedules = $em->getRepository('App:Schedule')->findByTeacherName($this->getUser()->getUsername());
            //Format data as the calendar needs
            $responseSchedule = [];
            foreach($schedules as $schedule){
                $dateEnd = clone($schedule->getDate());
                $responseSchedule[] = [
                    'id' => $schedule->getId(),
                    'title' => $schedule->getLesson()->getDescription(),
                    'start' => $schedule->getDate()->format('Y-m-d H:i:s'),
                    'end' => $dateEnd->modify('+50 minutes')->format('Y-m-d H:i:s')
                ];
            }
            return new JsonResponse($responseSchedule);
            
        }else{
            return $this->redirectToRoute('app_login');
        }

    }

    /**
     * Function to get the list of students that have made a reservation for a lesson
     * @Route("/get_schedule_students", name="get_schedule_students")
     */
    public function getStudentsOfScheduledLesson(Request $request){

        if($this->getUser()){
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('schedule_id');

            $schedule = $em->getRepository('App:Schedule')->findOneById($id);
            if($schedule){
                $students = [];
                foreach($schedule->getReservations() as $reservation){
                    array_push($students, $reservation->getUser()->getUsername());
                }
                if(count($students) == 0){
                    array_push($students, 'Nobody has made a reservation yet');
                }
                return new JsonResponse(["code" => 200, "message" => 'Lesson found', "data" => ['students' => $students]]);
            }else{
                return new JsonResponse(["code" => 500, "message" => 'Lesson not found']);
            }
          
            return new JsonResponse($responseSchedule);
            
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/save_scheduled_lesson", name="save_scheduled_lesson")
     */
    public function saveScheduledLesson(Request $request){

        if($this->getUser()){
            $em = $this->getDoctrine()->getManager();
            $params = $request->request->all();

            if(empty($params['date']) || empty($params['time']) || $params['time'] == "" || empty($params['level']) ){
                return $this->redirectToRoute('my_calendar', ["error"=> true]);
            }

            try {
                $schedule = new Schedule;
                $lesson = $em->getRepository('App:Lesson')->findOneById($params['level']);
                $schedule->setLesson($lesson);
                $lesson->addSchedule($schedule);
                $datetime = $params['date'].' '.$params['time'];
                $schedule->setDate(new \DateTime($datetime));
                $schedule->setTeacherName($this->getUser()->getUsername());

                $em->persist($schedule);
                $em->persist($lesson);

                $em->flush();
                
                return $this->redirectToRoute('my_calendar');
            } catch (\Throwable $th) {
                return $this->redirectToRoute('my_calendar', ["error"=> true]);
            }
            
        }else{
            return $this->redirectToRoute('app_login');
        }
    }
}