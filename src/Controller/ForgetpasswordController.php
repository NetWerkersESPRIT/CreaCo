<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ForgetpasswordController extends AbstractController
{
    #[Route('/forgetpassword', name: 'app_forgetpassword')]
    public function index(): Response
    {
        return $this->render('forgetpassword/index.html.twig', [
            'controller_name' => 'ForgetpasswordController',
        ]);
    }
}
