<?php

namespace App\Controller;

use App\Entity\CategorieCours;
use App\Form\CategorieCoursType;
use App\Repository\CategorieCoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categorie-cours')]
class CategorieCoursController extends AbstractController
{
    #[Route('/', name: 'app_categorie_cours_index', methods: ['GET'])]
    public function index(CategorieCoursRepository $categorieCoursRepository): Response
    {
        return $this->render('back/categorie_cours/index.html.twig', [
            'categorie_cours' => $categorieCoursRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_categorie_cours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategorieCoursRepository $categorieCoursRepository): Response
    {
        $categorieCours = new CategorieCours();
        $form = $this->createForm(CategorieCoursType::class, $categorieCours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieCoursRepository->save($categorieCours, true);

            return $this->redirectToRoute('app_categorie_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/categorie_cours/new.html.twig', [
            'categorie_cours' => $categorieCours,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_cours_show', methods: ['GET'])]
    public function show(CategorieCours $categorieCours): Response
    {
        return $this->render('back/categorie_cours/show.html.twig', [
            'categorie_cours' => $categorieCours,
        ]);
    }

    #[Route('/{id}/courses', name: 'app_categorie_cours_courses', methods: ['GET'])]
    public function courses(CategorieCours $categorieCours): Response
    {
        return $this->render('back/categorie_cours/courses.html.twig', [
            'categorie_cours' => $categorieCours,
            'cours' => $categorieCours->getCours(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorie_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieCours $categorieCours, CategorieCoursRepository $categorieCoursRepository): Response
    {
        $form = $this->createForm(CategorieCoursType::class, $categorieCours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieCoursRepository->save($categorieCours, true);

            return $this->redirectToRoute('app_categorie_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/categorie_cours/edit.html.twig', [
            'categorie_cours' => $categorieCours,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_cours_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieCours $categorieCours, CategorieCoursRepository $categorieCoursRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieCours->getId(), $request->request->get('_token'))) {
            $categorieCoursRepository->remove($categorieCours, true);
        }

        return $this->redirectToRoute('app_categorie_cours_index', [], Response::HTTP_SEE_OTHER);
    }
}
