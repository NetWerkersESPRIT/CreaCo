<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Form\MissionType;
use App\Repository\MissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/mission')]
final class MissionController extends AbstractController
{
    #[Route('/', name: 'app_mission_index', methods: ['GET'])]
    public function index(MissionRepository $missionRepository): Response
    {
        return $this->render('mission/index.html.twig', [
            'missions' => $missionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_mission_new', methods: ['GET', 'POST'])]
    // #[IsGranted('ROLE_USER')] // Uncomment to restrict
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mission = new Mission();

        // Pre-fill Idea if provided in the query string
        $ideaId = $request->query->get('idea_id');
        if ($ideaId) {
            $idea = $entityManager->getRepository(\App\Entity\Idea::class)->find($ideaId);
            if ($idea) {
                $mission->setImplementIdea($idea);
            }
        }

        // Restore other field data if returning from Idea creation
        if ($request->query->has('m_title')) {
            $mission->setTitle($request->query->get('m_title'));
        }
        if ($request->query->has('m_desc')) {
            $mission->setDescription($request->query->get('m_desc'));
        }
        if ($request->query->has('m_state')) {
            $mission->setState($request->query->get('m_state'));
        }

        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mission->setCreatedAt(new \DateTimeImmutable());

            // Set the assignedBy field to the creator of the selected Idea
            $idea = $mission->getImplementIdea();
            if ($idea && $idea->getCreator()) {
                $mission->setAssignedBy($idea->getCreator());
            }

            $entityManager->persist($mission);
            $entityManager->flush();

            $this->addFlash('success', 'Mission crée avec succès !');

            return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mission/new.html.twig', [
            'mission' => $mission,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mission_show', methods: ['GET'])]
    public function show(Mission $mission): Response
    {
        return $this->render('mission/show.html.twig', [
            'mission' => $mission,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_mission_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mission $mission, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mission->setLastUpdate(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Mission modifiée avec succès !');

            return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mission/edit.html.twig', [
            'mission' => $mission,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mission_delete', methods: ['POST'])]
    public function delete(Request $request, Mission $mission, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $mission->getId(), $request->request->get('_token'))) {
            $entityManager->remove($mission);
            $entityManager->flush();
            $this->addFlash('success', 'Mission supprimée avec succès !');
        }

        return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
    }
}
