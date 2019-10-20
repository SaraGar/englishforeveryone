<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\PaymentService;

class PaymentController extends Controller
{
    //--------------------------------------------------------------------
    //----------------------------------------------------------- Checkout 
    //--------------------------------------------------------------------

    /**
     * Function to check if a discount code is valid
     * @Route("/check_promo_code", name="check_promo_code")
     */
    public function checkPromoCode(Request $request){

        if($this->getUser()){
            $em = $this->getDoctrine()->getManager();
            $codeText = $request->request->get('code'); 
            $total = $request->request->get('total'); 

            //Promotion code validation
            $code = $em->getRepository('App:PromoCode')->getCodeFromTextIfValid($codeText);

            if($code){
                // Get the new total and return success response
                $discount = $total* ( $code->getPercentDiscount()/100);
                $newTotal = $total - $discount;
                return new JsonResponse(["code" => 200, "message" => 'Valid code', "data" => ['newTotal' => $newTotal, 'code' => $code->getCode(), 'discount' => $discount]]);
            }else{
                //Return error response
                return new JsonResponse(["code" => 500, "message" => 'Invalid code']);
            }

        }else{
            return $this->redirectToRoute('app_login');
        }

    }

     /**
     * Function to complete a purchase
     * @Route("/checkout", name="checkout")
     */
    public function checkout(Request $request, PaymentService $paymentService){

        if($this->getUser()){
            parse_str($request->request->get('form'), $formData); 
            
            //If the user ask to save the billing information
            if(isset($formData['save-info']) && $formData['save-info'] == 'on'){
                $this->saveBillingAddress($formData);
            }

            //Proceed depending on the payment method (by the moment, only transfer is available)
            if($formData['paymentMethod']){
                switch ($formData['paymentMethod']){
                    case 'transfer':

                        $result = $paymentService->generatePlanInvoiceAndLines($formData);                                              
                        return new JsonResponse($result);

                    default:
                        return new JsonResponse(["code" => 500, "message" => 'Invalid payment method']);
                }
            }
            return new JsonResponse(["code" => 500, "message" => 'Missing data']);

        }else{
            return $this->redirectToRoute('app_login');
        }
    }


    //--------------------------------------------------------------------
    //-------------------------------------------------------- My Invoices 
    //--------------------------------------------------------------------

    /**
     * Function to get the user's invoices list, to show the datatable
     * @Route("/get_my_invoices", name="get_my_invoices")
     */
    public function getMyInvoices(Request $request){
        $em = $this->getDoctrine()->getManager();
        $params = $request->request->all();
        parse_str($request->request->get('form_filters'), $filters); 
        $invoices = $em->getRepository('App:Invoice')->getMyInvoices($params, $filters, $this->getUser());        
        return new JsonResponse($invoices);
    }

    /**
     * Function to download an invoice
     * @Route("/invoice_to_pdf", name="invoice_to_pdf")
     */
    public function invoiceToPDF(Request $request){
        $em = $this->getDoctrine()->getManager();

        $invoice = $em->getRepository('App:Invoice')->findOneById($request->get('invoiceId'));
        if($invoice){
            $html = $this->renderView('payment/invoice_pdf.html.twig', ['invoice' => $invoice]);
            $filename = sprintf('invoice-%s.pdf', date('Y-m-d-hh-ss'));
            return new Response(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                200,
                [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                ]
            );
        }else{
            return new JsonResponse(["code" => 500, "message" => 'Error processing invoice']);
        }
       
    }
}
