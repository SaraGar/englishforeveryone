<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class RoutesController extends AbstractController
{
    /**
     * Home page
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
     * Function to check the user's role on login, and redirect 
     * @Route("/dash", name="dash")
     */
    public function dashboard()
    {   if($this->isGranted('ROLE_SUPER_ADMIN')){
            return $this->redirect('http://localhost:8000/teacher_admin_panel/');
        }else if ($this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('my_invoices');
        }else if ($this->isGranted('ROLE_TEACHER')){
            return $this->redirectToRoute('my_calendar');
        }
        else if($this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('my_calendar');
        }            
    }

    /**
     * Load my profile page
     * @Route("/profile", name="profile")
     */
    public function loadProfile()
    {  
        // Check user authentication
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
     * Load pricing page
     * @Route("/pricing", name="pricing")
     */
    public function loadPricing()
    {  
        // Check user authentication
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
     * Billing address page 
     * @Route("/payment", name="payment")
     */
    public function loadPayment(Request $request)
    {  
        // Check user authentication
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
    * Page to give information about successful purchase with wire transfer payment
    * @Route("/checkout_transfer", name="checkout_transfer")
    */
    public function checkout_transfer(){
        // Check user authentication
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
    * My invoices page
    * @Route("/my_invoices", name="my_invoices")
    */
    public function loadMyInvoices(){
        // Check user authentication
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
    * My reservations page
    * @Route("/my_calendar", name="my_calendar")
    */
    public function loadMyCalendar(){
        // Check user authentication
        if($this->getUser()){ 
            $lessons = $this->getDoctrine()->getManager()->getRepository('App:Lesson')->findAll();
            $levels = $this->getDoctrine()->getManager()->getRepository('App:Lesson')->getLevels();
            return $this->render('dashboard/my_calendar.html.twig', [
                'title' => 'My calendar',
                'controller_name' => 'RoutesController',
                'levels' => $levels,
                'lessons' => $lessons
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }
}
