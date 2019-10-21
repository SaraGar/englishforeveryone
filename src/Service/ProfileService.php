<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ProfileService
{
    private $em;
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }


    public function validateProfileForm($formData){

        if(!$formData['firstname'] || !$formData['lastname'] || !$formData['avatar'] || !$formData['email'] || !$formData['pass1'] || !$formData['pass2']){
            return ["code" => 500, "message" => 'Please fill in all the fields'];
        }

        if($formData['pass1'] != $formData['pass2']){
            return ["code" => 500, "message" => "Passwords don't match"];
        }

        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            return ["code" => 500, "message" => "Invalid email format"];
        }

        $user = $this->em->getRepository('App:User')->findOneByEmail($formData['email']);
        if($user && ($user->getId() != $this->security->getUser()->getId())){
            return ["code" => 500, "message" => "We have another user with this email!"];
        }

        return ["code" => 200, "message" => "Data OK!"];
    }

}