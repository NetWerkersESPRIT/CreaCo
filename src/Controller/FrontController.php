<?php

namespace App\Controller;

use App\Repository\CategorieCoursRepository;
use App\Repository\CoursRepository;
use App\Repository\RessourceRepository;
use App\Entity\CategorieCours;
use App\Entity\Cours;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
final class FrontController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategorieCoursRepository $catRepo): Response
    {
        return $this->render('front/home/index.html.twig', [
            'categories' => $catRepo->findAll(),
        ]);
    }

    #[Route('/category/{id}', name: 'app_front_category')]
    public function category(CategorieCours $category): Response
    {
        return $this->render('front/category/show.html.twig', [
            'category' => $category,
            'courses' => $category->getCours(),
        ]);
    }

    #[Route('/course/{id}', name: 'app_front_course')]
    public function course(Cours $course): Response
    {
        return $this->render('front/course/show.html.twig', [
            'course' => $course,
            'ressources' => $course->getRessources(),
        ]);
    }
}
