<?php

namespace App\Controller;


use App\Repository\UserRepository;
use App\Repository\AbonnementRepository;
use App\Entity\User;
use App\Form\UserType;
use App\Form\EditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use function PHPUnit\Framework\countOf;
use Dompdf\Dompdf ;
use Dompdf\Options;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\DependencyInjection\Loader\Configurator;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mime\Email;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{


     /**
     * @Route("/admin", name="app_user")
     */
    public function index(UserRepository $userRepository ): Response
    {
        return $this->render('admin/user_index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    
    
    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('Admin/show.html.twig', [
            'user' => $user,
        ]);
    }
    /**
     * @Route("/{id}/user", name="show_userr", methods={"GET"})
     */
    public function showuser(User $user): Response
    {
        return $this->render('user/show_user.html.twig', [
            'user' => $user,
        ]);
    }
    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user,UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->flush();

            return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/{id}/editStat", name="user_status_edit", methods={"GET", "POST"})
     */
    public function edit_status(Request $request, ManagerRegistry $mg ,User $user, $id,UserRepository $em): Response
    {
        
        $user=$em->find($id);
        if($user->getStatuts()=="Banned"){
        $user->setStatuts("Actif");
        $email=$user->getEmail();
        $mail=(new Email())
        ->from('pidevmycompany2023@gmail.com')
        ->to($email)
        ->subject('statut updated')
        ->text("your account has been Actif !");
        $trasport= new GmailSmtpTransport('pidevmycompany2023@gmail.com','guyuwthwzlzquasf');
        $mailer= new mailer($trasport);
        $mailer->send($mail);
    
    
    }
        elseif($user->getStatuts()=="Actif"){
        $user->setStatuts("Banned");
        $email=$user->getEmail();
        $mail=(new Email())
        ->from('pidevmycompany2023@gmail.com')
        ->to($email)
        ->subject('statut updated')
        ->text("your account has been Bannedd !");
        $trasport= new GmailSmtpTransport('pidevmycompany2023@gmail.com','guyuwthwzlzquasf');
        $mailer= new mailer($trasport);
        $mailer->send($mail);
    
    }
        $em=$mg->getManager();
        $em->persist($user); 
        $em->flush();
            return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
    }
}
