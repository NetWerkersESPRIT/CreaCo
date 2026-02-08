<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use App\Repository\CoursRepository;
use App\Repository\CategorieCoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cours')]
class CoursController extends AbstractController
{
    #[Route('/', name: 'app_cours_index', methods: ['GET'])]
    public function index(CoursRepository $coursRepository): Response
    {
        return $this->render('back/cours/index.html.twig', [
            'cours' => $coursRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CategorieCoursRepository $categoryRepo): Response
    {
        $cours = new Cours();
        $catId = $request->query->get('category');
        $selectedCategory = $catId ? $categoryRepo->find($catId) : null;

        if ($selectedCategory) {
            $cours->setCategorie($selectedCategory);
        }

        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($selectedCategory) {
                $cours->setCategorie($selectedCategory);
            }
            $entityManager->persist($cours);
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/cours/new.html.twig', [
            'cours' => $cours,
            'form' => $form,
            'selected_category' => $selectedCategory,
        ]);
    }

    #[Route('/{id}', name: 'app_cours_show', methods: ['GET'])]
    public function show(Cours $cours): Response
    {
        return $this->render('back/cours/show.html.twig', [
            'cours' => $cours,
        ]);
    }
    #[Route('/{id}/resources', name: 'app_cours_resources', methods: ['GET'])]
    public function resources(Cours $cours): Response
    {
        return $this->render('back/cours/resources.html.twig', [
            'cours' => $cours,
            'ressources' => $cours->getRessources(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cours $cours, CoursRepository $coursRepository): Response
    {
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coursRepository->save($cours, true);

            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/cours/edit.html.twig', [
            'cours' => $cours,
            'form' => $form->createView(),
            'selected_category' => null,
        ]);
    }

    #[Route('/{id}', name: 'app_cours_delete', methods: ['POST'])]
    public function delete(Request $request, Cours $cours, CoursRepository $coursRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cours->getId(), $request->request->get('_token'))) {
            $coursRepository->remove($cours, true);
        }

        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }
}
