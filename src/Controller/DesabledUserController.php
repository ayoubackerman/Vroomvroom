<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DesabledUserController extends AbstractController
{
    /**
     * @Route("/desabled/user", name="app_desabled_user")
     */
    public function index(): Response
    {
        return $this->render('desabled_user/index.html.twig', [
            'controller_name' => 'DesabledUserController',
        ]);
    }
}
