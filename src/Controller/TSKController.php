<?php

namespace App\Controller;

use App\Entity\Idea;
use App\Form\IdeaType;
use App\Repository\IdeaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tsk')]
final class TSKController extends AbstractController
{
    #[Route('/', name: 'app_tsk_index', methods: ['GET'])]
    public function index(IdeaRepository $ideaRepository): Response
    {
        return $this->render('tsk/index.html.twig', [
            'ideas' => $ideaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tsk_new', methods: ['GET', 'POST'])]
    // #[IsGranted('ROLE_USER')] // Uncomment if you want to restrict creation to logged-in users
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $idea = new Idea();
        $form = $this->createForm(IdeaType::class, $idea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idea->setCreatedAt(new \DateTimeImmutable());

            // Set the creator if user is logged in
            // Set creator to User with ID 1
            $creator = $entityManager->getRepository(\App\Entity\Users::class)->find(1);
            if ($creator) {
                $idea->setCreator($creator);
            }

            $entityManager->persist($idea);
            $entityManager->flush();

            $this->addFlash('success', 'Idée créée avec succès !');

            return $this->redirectToRoute('app_tsk_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tsk/new.html.twig', [
            'idea' => $idea,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tsk_show', methods: ['GET'])]
    public function show(Idea $idea): Response
    {
        return $this->render('tsk/show.html.twig', [
            'idea' => $idea,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tsk_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Idea $idea, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IdeaType::class, $idea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Idée modifiée avec succès !');

            return $this->redirectToRoute('app_tsk_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tsk/edit.html.twig', [
            'idea' => $idea,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tsk_delete', methods: ['POST'])]
    public function delete(Request $request, Idea $idea, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $idea->getId(), $request->request->get('_token'))) {
            $entityManager->remove($idea);
            $entityManager->flush();
            $this->addFlash('success', 'Idée supprimée avec succès !');
        }

        return $this->redirectToRoute('app_tsk_index', [], Response::HTTP_SEE_OTHER);
    }
}
