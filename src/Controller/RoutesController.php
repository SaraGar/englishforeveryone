<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class RoutesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $plans = $this->getDoctrine()->getManager()->getRepository('App:Plan')->findBy(['disabled' => 0]);
         
        return $this->render('home/index.html.twig', [
            'controller_name' => 'RoutesController',
            'plans' => $plans
        ]);
    }

     /**
     * @Route("/dash", name="dash")
     */
    public function dashboard()
    {   
        if($this->isGranted('ROLE_SUPER_ADMIN')){

            return $this->redirect('http://localhost:8000/teacher_admin_panel/');
        
        }else if ($this->isGranted('ROLE_ADMIN')){
        
            return $this->redirectToRoute('my_invoices');
        
        }else if ($this->isGranted('ROLE_TEACHER')){
        
            return $this->redirectToRoute('my_calendar');

        }else if($this->isGranted('ROLE_USER')){
        
            return $this->redirectToRoute('my_calendar');
        }            
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function loadProfile()
    {  
        if($this->getUser()){ 
            return $this->render('dashboard/profile.html.twig',  [
                'user' => $this->getUser(),
                'title' => 'Profile',
                'controller_name' => 'RoutesController',
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }


    /**
     * @Route("/pricing", name="pricing")
     */
    public function loadPricing()
    {  
        if($this->getUser()){ 
            $plans = $this->getDoctrine()->getManager()->getRepository('App:Plan')->findBy(['disabled' => 0]);
            return $this->render('dashboard/pricing.html.twig',  [
                'title' => 'Plans & Pricing',
                'plans' => $plans,
                'controller_name' => 'RoutesController',
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

     /**
     * @Route("/payment", name="payment")
     */
    public function billingAddressPage(Request $request)
    {  
        if($this->getUser()){ 
            $plan = '';
            $type = $request->get('t');
            if($type){
                $plan = $this->getDoctrine()->getManager()->getRepository('App:Plan')->findOneBy(['disabled' => 0, 'lessonsNumber' => $type]);
            }
            return $this->render('payment/billingAddress.html.twig',  [
                'title' => 'Payment',
                'plan' => $plan,
                'controller_name' => 'RoutesController',
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }


    /**
    * Page to give information about completed purchase with wire transfer payment
    * @Route("/checkout_transfer", name="checkout_transfer")
    */
    public function checkout_transfer(){
        if($this->getUser()){ 
            return $this->render('payment/success_transfer.html.twig',  [
                'title' => 'Payment',
                'controller_name' => 'RoutesController',
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

   /**
    * @Route("/my_invoices", name="my_invoices")
    */
    public function loadMyInvoices(){
        if($this->getUser()){ 
            return $this->render('dashboard/invoices.html.twig',  [
                'title' => 'Invoices',
                'controller_name' => 'RoutesController',
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
    * @Route("/my_calendar", name="my_calendar")
    */
    public function loadMyCalendar(){
        if($this->getUser()){ 
            $lessons = $this->getDoctrine()->getManager()->getRepository('App:Lesson')->findAll();
            $levels = $this->getDoctrine()->getManager()->getRepository('App:Lesson')->getLevelNames();
            return $this->render('dashboard/my_calendar.html.twig', [
                'title' => 'My calendar',
                'controller_name' => 'RoutesController',
                'levels' => $levels,
                'lessons' => $lessons,
                'paidLessonsLeft' => $this->getUser()->getPaidLessonsLeft()
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }
}
