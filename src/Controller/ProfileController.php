<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ProfileService;

class ProfileController extends AbstractController
{
    /**
     * Function to edit the user profile
     * @Route("/edit_user_profile", name="edit_user_profile")
     */
    public function editProfileAction(Request $request, UserPasswordEncoderInterface $encoder, ProfileService $profileService){

        if($this->getUser()){
            $em = $this->getDoctrine()->getManager();
            parse_str($request->request->get('form'), $formData); 

            //----------Validations
            $validatorResponse = $profileService->validateProfileForm($formData);
            if($validatorResponse['code'] == 500){
                return new JsonResponse($validatorResponse);
            }
            
            //--------Encode password
            $pass = $encoder->encodePassword($this->getUser(), $formData['pass1']);

            //--------Setters
            $this->getUser()->setFirstname($formData['firstname']);
            $this->getUser()->setLastname($formData['lastname']);
            $this->getUser()->setAvatarName($formData['avatar']);
            $this->getUser()->setEmail($formData['email']);
            $this->getUser()->setPassword($pass);

            $em->persist($this->getUser());
            $em->flush();

            return new JsonResponse(["code" => 200, "message" => 'Changes saved']);

        }else{
            return $this->redirectToRoute('app_login');
        }

    }


}
