<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\DependencyInjection\Loader\Configurator;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils ,  Request $request): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    
    /**
     * @Route("/reset_pass_page", name="app_reset_password_page")
     */
    public function resetPasswordPage(Request $request, EntityManagerInterface $entityManager,\Swift_Mailer $mailer)
    {
        if ($request->isMethod('POST')) {
            $user = $this->getDoctrine()->getRepository(User::class)
                ->findOneBy(['email' => $request->request->get('email')]);

            $user->setResetToken(md5(uniqid()));
            $entityManager->flush();

            $message =( new \Swift_Message('Please Reset your password'))
                ->setFrom('pidevmycompany2023@gmail.com')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('security/reset_password_email.html.twig',
                    ['reset_token'=>$user->getResetToken()]),'text/html') ;
            $mailer->send($message);

            return $this->redirectToRoute('app_login');
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        }
           else{
               return $this->render('security/reset_password.html.twig', ['page' => "page1"]);
           }

        }

    /**
     * @Route("/reset_pass/{reset_token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, string $reset_token, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['resetToken' => $reset_token]);
        if ($user == null){
            $this->addFlash('Danger message','utilisateur n existe pas');
            return $this->redirectToRoute('app_login');
        }
        if ($request->isMethod('POST')) {
            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user,$request->request->get('password')));
            $entityManager = $this ->getDoctrine()->getManager();
            $entityManager->flush();
            $email=$user->getEmail();
               $mail=(new Email())
               ->from('pidevmycompany2023@gmail.com')
               ->to($email)
               ->subject('PASSWORD CHANGED')
               ->text("your PASSWORD has been changed !");
               $trasport= new GmailSmtpTransport('pidevmycompany2023@gmail.com','guyuwthwzlzquasf');
               $mailer= new mailer($trasport);
               $mailer->send($mail);
            return $this->redirectToRoute('app_login');}
        else{
            return $this->render('security/reset_password.html.twig',['page' => "page2",'reset_token' => $reset_token]);
        }

    }
}
