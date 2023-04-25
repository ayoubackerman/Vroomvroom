<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Role;

use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use App\Security\EmailVerifier;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request,\Swift_Mailer $mailer, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager , TexterInterface $texter): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setActivationToken(md5(uniqid()));
            

            if (in_array('2',$user->getRoles())) {
                $roleId = 2; // Example ID
                $role = $entityManager->getRepository(Role::class)->find($roleId);
                
                // Set the role for a User entity
                $user->setIdRole($role);         
               }elseif(in_array('3',$user->getRoles())) {
                $roleId = 3; // Example ID
$role = $entityManager->getRepository(Role::class)->find($roleId);

// Set the role for a User entity
$user->setIdRole($role);
                }
            

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email


            $message =( new \Swift_Message('Please Confirm your registration'))
            ->setFrom('benbrahimayoubben@gmail.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('registration/confirmation_email.html.twig',
                ['token'=>$user->getActivationToken()]),'text/html') ;
        $mailer->send($message);
        $this->addFlash('message','Demande dinscription envoyé!');

        $sms = new SmsMessage(
            '+21694937629',
            'Bonjour '.$user->getNomd().', Bienvenue à VROOM VROOM'
        );

         $texter->send($sms);


            return $this->redirectToRoute('app_login');
        }



         return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

    }

        /**
     * @Route("/verify/email/{token}", name="verify_email")
     */
    public function verifyUserEmail($token,UserRepository $userRepository)
    {
        $user=$userRepository->findOneBy(['activationToken'=>$token]);

        if(!$user){
            return $this->redirectToRoute('app_login');

        }

        $user->setActivationToken(null);
        $entityManager=$this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }


}