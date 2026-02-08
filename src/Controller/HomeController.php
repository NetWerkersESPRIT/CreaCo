<?php

namespace App\Controller;

use App\Repository\CategorieCoursRepository;
use App\Repository\CoursRepository;
use App\Repository\RessourceRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(
        CategorieCoursRepository $catRepo,
        CoursRepository $coursRepo,
        RessourceRepository $resRepo,
        UsersRepository $userRepo
    ): Response
    {
        return $this->render('back/home/index.html.twig', [
            'count_categories' => $catRepo->count([]),
            'count_courses' => $coursRepo->count([]),
            'count_resources' => $resRepo->count([]),
            'count_users' => $userRepo->count([]),
        ]);
    }
}
