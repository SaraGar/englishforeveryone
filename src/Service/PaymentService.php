<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\BillingAddress;
use App\Entity\Invoice;
use App\Entity\InvoiceLine;

class PaymentService
{

    private $em;
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * Aux function to generate a plan's invoice and its lines
     */
    public function generatePlanInvoiceAndLines($formData){

        //Get the plan 
        $plan = $this->em->getRepository('App:Plan')->findOneBy(['disabled' => 0, 'lessonsNumber' =>  $formData['type']]);
        if($plan){
            //TODO the payment should be checked manually in the admin panel and then, the lessons should be added, not here.
            $this->security->getUser()->setPaidLessonsLeft($this->security->getUser()->getPaidLessonsLeft() + $plan->getLessonsNumber());

            //Create a new invoice, with temporary total, to be able to link it to he lines 
            $response = $this->generatePlanInvoice($formData, $plan->getPriceNet());
            if($response['code'] != 200){
                return ["code" => 500, "message" => 'There has been an error'];
            }else{
                $invoice = $response['data']['invoice'];
            }

            //Create an invoice line with the plan amount
            $lineData = [
                'concept' => $plan->getLessonsNumber().' lessons plan', 
                'totalNet' => $plan->getPriceNet(), 
                'totalVAT' => 0, 
                'total' => $plan->getPriceTotal(), 
                'invoice' => $invoice 
            ];
            $response = $this->generatePlanInvoiceLine($lineData);
            if($response['code'] != 200){
                return ["code" => 500, "message" => 'There has been an error'];
            }

            //Get the discount -if any- and create discount line
            if(isset($formData['appliedCode'])){
                $code = $this->em->getRepository('App:PromoCode')->getCodeFromTextIfValid($formData['appliedCode']);
                $lineData = [
                    'concept' => $code->getPercentDiscount().'% discount code',
                    'totalNet' => $plan->getPriceNet() * ( $code->getPercentDiscount()/100) * -1,
                    'totalVAT' => 0, 
                    'total' =>  $plan->getPriceNet() * ( $code->getPercentDiscount()/100) * -1, 
                    'invoice' => $invoice];
                $response = $this->generatePlanInvoiceLine($lineData);
                if($response['code'] != 200){
                    return ["code" => 500, "message" => 'There has been an error'];
                }
            }        
            
            //Fix invoice amounts
           return $this->recalculateInvoiceByLines($invoice);     
           
        }else{
            return ["code" => 500, "message" => 'Missing data'];
        }
    }

    /**
     * Aux function to save the billing address for future use
     */
    public function saveBillingAddress($formData){

        try {

            $billingAddress = new BillingAddress();
            $billingAddress->setName($formData['firstname'] ? $formData['firstname'] : "");
            $billingAddress->setLastname($formData['lastname'] ? $formData['lastname'] : "");
            $billingAddress->setAddress($formData['address'] ? $formData['address'] : "");
            $billingAddress->setCity($formData['city'] ? $formData['city'] : "");
            $billingAddress->setZipCode($formData['zip'] ? $formData['zip'] : "");
            $billingAddress->setCountry($formData['country'] ? $formData['country'] : "");
            $billingAddress->setUser($this->security->getUser());
            
            $this->em->persist($billingAddress);
            $this->em->flush();

            return ["code" => 200, "message" => 'Billing address saved'];
    
        } catch (\Throwable $th) {
            return ["code" => 500, "message" => 'There has ben an error processing the billing address'];
        }       
    }


    /**
     * Aux function to generate a plan's invoice
     */
    public function generatePlanInvoice($formData, $temporaryTotal=0){

        $invoice = new Invoice();
        try {
            $invoice->setName($formData['firstname'] ? $formData['firstname'] : "");
            $invoice->setLastname($formData['lastname'] ? $formData['lastname'] : "");
            $invoice->setAddress($formData['address'] ? $formData['address'] : "");
            $invoice->setCity($formData['city'] ? $formData['city'] : "");
            $invoice->setCountry($formData['country'] ? $formData['country'] : "");
            $invoice->setZipCode($formData['zip'] ? $formData['zip'] : "");
            $invoice->setCreatedDate(new \DateTime());
            $invoice->setDueDate(new \DateTime('now +5 day'));
            $invoice->setTotalNet($temporaryTotal);
            $invoice->setTotalVat(0); //VAT is not applied in academic english lessons 
            $invoice->setTotal($temporaryTotal);
            $invoice->setUser($this->security->getUser());
            $this->security->getUser()->addInvoice($invoice);

            $this->em->persist($invoice);
            $this->em->persist($this->security->getUser());
            $this->em->flush();

            return ["code" => 200, "message" => 'Invoice saved', "data" => ['invoice' => $invoice]];
        } catch (\Throwable $th) {
           return ["code" => 500, "message" => 'Error processing invoice'];
        }
       
    }

    /**
     * Aux function to generate the lines of a plan's invoice
     */
    public function generatePlanInvoiceLine($lineInfo){

        try {
            $line = new InvoiceLine();
            $line->setConcept($lineInfo['concept']);
            $line->setTotalNet($lineInfo['totalNet']);
            $line->setTotalVAT($lineInfo['totalVAT']);
            $line->setTotal($lineInfo['total']);
            $line->setInvoice($lineInfo['invoice']);
            $lineInfo['invoice']->addInvoiceLine($line);
            $this->em->persist($line);
            $this->em->persist($lineInfo['invoice']);
            $this->em->flush();

            return ["code" => 200, "message" => 'Invoice line saved', 'data' => ['line' => $line]];

        } catch (\Throwable $th) {
              $this->em->remove($lineInfo['invoice']); $this->em->flush();             
           return ["code" => 500, "message" => 'Error processing invoice'];
        }
     
    }

    /**
     * Aux function to fix total of the invoice, by using totals of the lines
     */
    public function recalculateInvoiceByLines($invoice){

        try {
            $total = $totalNet = $totalVAT = 0;

            //Get the real totals throught the lines
            foreach($invoice->getInvoiceLines() as $line){
                $total += $line->getTotal();
                $totalNet += $line->getTotalNet();
                $totalVAT += $line->getTotalVAT();
            }

            //Set the real totals
            $invoice->setTotal($total);
            $invoice->setTotalVAT($totalVAT);
            $invoice->setTotalNet($totalNet);
            $this->em->persist($invoice);
            $this->em->flush();

            return ["code" => 200, "message" => 'Invoice saved', "data" => ["route_name" => "checkout_transfer"]];
        } catch (\Throwable $th) {
           $this->em->remove($invoice); $this->em->flush();             
           return ["code" => 500, "message" => 'Error processing invoice'];
        }
    }
}