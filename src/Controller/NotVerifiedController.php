<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotVerifiedController extends AbstractController
{
    /**
     * @Route("/notverified/user", name="notverified_user")
     */
    public function index(): Response
    {
        return $this->render('notverified_user/index.html.twig', [
            'controller_name' => 'NotVerifiedControllerr',
        ]);
    }
}