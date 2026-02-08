<?php

namespace App\Controller;

use App\Entity\Ressource;
use App\Form\RessourceType;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/ressource')]
final class RessourceController extends AbstractController
{
    #[Route(name: 'app_ressource_index', methods: ['GET'])]
    public function index(RessourceRepository $ressourceRepository): Response
    {
        return $this->render('back/ressource/index.html.twig', [
            'ressources' => $ressourceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ressource_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, \App\Repository\CoursRepository $coursRepo, SluggerInterface $slugger): Response
    {
        $ressource = new Ressource();
        $courseId = $request->query->get('course');
        $selectedCourse = $courseId ? $coursRepo->find($courseId) : null;

        if ($selectedCourse) {
            $ressource->setCours($selectedCourse);
        }

        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('fichier')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/ressources';
                    
                    if (!is_dir($uploadDirectory)) {
                        mkdir($uploadDirectory, 0777, true);
                    }

                    // On récupère le mimeType AVANT de déplacer le fichier
                    $mimeType = $file->getMimeType();

                    $file->move($uploadDirectory, $newFilename);
                    $ressource->setUrl('/uploads/ressources/'.$newFilename);
                    
                    if (str_contains($mimeType, 'pdf')) {
                        $ressource->setType('PDF');
                    } elseif (str_contains($mimeType, 'image')) {
                        $ressource->setType('IMAGE');
                    } elseif (str_contains($mimeType, 'video')) {
                        $ressource->setType('VIDEO');
                    } else {
                        $ressource->setType('FILE');
                    }
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload : ' . $e->getMessage());
                    return $this->render('back/ressource/new.html.twig', [
                        'ressource' => $ressource,
                        'form' => $form->createView(),
                        'selected_course' => $selectedCourse,
                    ]);
                }
            }

            // Important: Force re-assignment of course if it was pre-selected
            if ($selectedCourse) {
                $ressource->setCours($selectedCourse);
            }

            try {
                $entityManager->persist($ressource);
                $entityManager->flush();
                $this->addFlash('success', 'Ressource créée avec succès !');
                return $this->redirectToRoute('app_ressource_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
            }
        }

        return $this->render('back/ressource/new.html.twig', [
            'ressource' => $ressource,
            'form' => $form->createView(),
            'selected_course' => $selectedCourse,
        ]);
    }

    #[Route('/{id}', name: 'app_ressource_show', methods: ['GET'])]
    public function show(Ressource $ressource): Response
    {
        return $this->render('back/ressource/show.html.twig', [
            'ressource' => $ressource,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ressource_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ressource $ressource, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('fichier')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/ressources';
                    
                    if (!is_dir($uploadDirectory)) {
                        mkdir($uploadDirectory, 0777, true);
                    }

                    $file->move($uploadDirectory, $newFilename);
                    $ressource->setUrl('/uploads/ressources/'.$newFilename);
                    
                    $mimeType = $file->getMimeType();
                    if (str_contains($mimeType, 'pdf')) {
                        $ressource->setType('PDF');
                    } elseif (str_contains($mimeType, 'image')) {
                        $ressource->setType('IMAGE');
                    } elseif (str_contains($mimeType, 'video')) {
                        $ressource->setType('VIDEO');
                    } else {
                        $ressource->setType('FILE');
                    }
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload : ' . $e->getMessage());
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Ressource modifiée avec succès !');

            return $this->redirectToRoute('app_ressource_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/ressource/edit.html.twig', [
            'ressource' => $ressource,
            'form' => $form->createView(),
            'selected_course' => null,
        ]);
    }

    #[Route('/{id}', name: 'app_ressource_delete', methods: ['POST'])]
    public function delete(Request $request, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ressource->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ressource);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ressource_index', [], Response::HTTP_SEE_OTHER);
    }
}
