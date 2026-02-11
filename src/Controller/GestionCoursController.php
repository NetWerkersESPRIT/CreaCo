<?php

namespace App\Controller;

use App\Entity\CategorieCours;
use App\Entity\Cours;
use App\Entity\Ressource;
use App\Form\CategorieCoursType;
use App\Form\CoursType;
use App\Form\RessourceType;
use App\Repository\CategorieCoursRepository;
use App\Repository\CoursRepository;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur unifié pour la gestion des cours, catégories et ressources
 * 
 * Ce contrôleur regroupe toutes les opérations CRUD pour :
 * - Les catégories de cours (CategorieCours)
 * - Les cours (Cours)
 * - Les ressources pédagogiques (Ressource)
 */
class GestionCoursController extends AbstractController
{
    // ========================================
    // GESTION DES CATÉGORIES DE COURS
    // ========================================

    /**
     * Liste toutes les catégories de cours
     */
    #[Route('/categorie-cours', name: 'app_categorie_cours_index', methods: ['GET'])]
    public function categorieIndex(CategorieCoursRepository $categorieCoursRepository): Response
    {
        return $this->render('back/categorie_cours/index.html.twig', [
            'categorie_cours' => $categorieCoursRepository->findAll(),
        ]);
    }

    /**
     * Crée une nouvelle catégorie de cours
     * Accessible uniquement aux administrateurs
     */
    #[Route('/categorie-cours/new', name: 'app_categorie_cours_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function categorieNew(Request $request, CategorieCoursRepository $categorieCoursRepository, EntityManagerInterface $entityManager): Response
    {   
        // Création d'une nouvelle instance de l'entité
        $categorieCours = new CategorieCours();
        // Création du formulaire lié à l'entité
        $form = $this->createForm(CategorieCoursType::class, $categorieCours);
        // traitement de la requete https
        $form->handleRequest($request);
        // verif formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // sauvegarde de la categorie dans la BD
            $entityManager->persist($categorieCours);
            $entityManager->flush();
            // redirection vers liste de categories
            return $this->redirectToRoute('app_categorie_cours_index', [], Response::HTTP_SEE_OTHER);
        }
        // affichage du  form de creation de la categorie
        return $this->render('back/categorie_cours/new.html.twig', [
            'categorie_cours' => $categorieCours,
            'form' => $form,
        ]);
    }

    /**
     * Affiche les détails d'une catégorie de cours
     */
    #[Route('/categorie-cours/{id}', name: 'app_categorie_cours_show', methods: ['GET'])]
    public function categorieShow(CategorieCours $categorieCours): Response
    {
        return $this->render('back/categorie_cours/show.html.twig', [
            'categorie_cours' => $categorieCours,
        ]);
    }

    /**
     * Affiche la liste des cours d'une catégorie spécifique
     */
    #[Route('/categorie-cours/{id}/courses', name: 'app_categorie_cours_courses', methods: ['GET'])]
    public function categorieCourses(CategorieCours $categorieCours): Response
    {
        return $this->render('back/categorie_cours/courses.html.twig', [
            'categorie_cours' => $categorieCours,
            'cours' => $categorieCours->getCours(),
        ]);
    }

    /**
     * Modifie une catégorie de cours existante
     * Accessible uniquement aux administrateurs
     */
    #[Route('/categorie-cours/{id}/edit', name: 'app_categorie_cours_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function categorieEdit(Request $request, CategorieCours $categorieCours, CategorieCoursRepository $categorieCoursRepository): Response
    {
        // creation du formulaire avec données existantes
        $form = $this->createForm(CategorieCoursType::class, $categorieCours);
        // traitement requete https
        $form->handleRequest($request);
        // verif formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // màj de la categorie
            $categorieCoursRepository->save($categorieCours, true);
            // redirection vers liste des categories
            return $this->redirectToRoute('app_categorie_cours_index', [], Response::HTTP_SEE_OTHER);
        }
        // affichage du formulaire de modif
        return $this->render('back/categorie_cours/edit.html.twig', [
            'categorie_cours' => $categorieCours,
            'form' => $form,
        ]);
    }

    /**
     * Supprime une catégorie de cours
     * Accessible uniquement aux administrateurs
     */
    #[Route('/categorie-cours/{id}', name: 'app_categorie_cours_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function categorieDelete(Request $request, CategorieCours $categorieCours, CategorieCoursRepository $categorieCoursRepository): Response
    {
        // verif de token avant suppression
        if ($this->isCsrfTokenValid('delete'.$categorieCours->getId(), $request->request->get('_token'))) {
            // suppression de la categorie de la BD
            $categorieCoursRepository->remove($categorieCours, true);
        }
        // redirection vers liste des categories
        return $this->redirectToRoute('app_categorie_cours_index', [], Response::HTTP_SEE_OTHER);
    }

    // ========================================
    // GESTION DES COURS
    // ========================================

    /**
     * Liste tous les cours avec filtres et tri
     */
    #[Route('/cours', name: 'app_cours_index', methods: ['GET'])]
    public function coursIndex(Request $request, CoursRepository $coursRepository): Response
    {
        $filters = [
            'search' => $request->query->get('search'),
            'titre'  => $request->query->get('titre'),
            'categorie' => $request->query->get('categorie'),
        ];

        $sort = [
            'field' => $request->query->get('sort'),
            'order' => $request->query->get('order', 'DESC'),
        ];

        $cours = $coursRepository->findWithFilters($filters, $sort);

        return $this->render('back/cours/index.html.twig', [
            'cours' => $cours,
            'filters' => $filters,
            'sort' => $sort,
            'search' => $filters['search'],
        ]);
    }

    /**
     * Crée un nouveau cours
     * Accessible uniquement aux administrateurs
     */
    #[Route('/cours/new', name: 'app_cours_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function coursNew(Request $request, EntityManagerInterface $entityManager, CategorieCoursRepository $categoryRepo, SluggerInterface $slugger): Response
    {
        // nouvelle instance de cours
        $cours = new Cours();
        $cours->setDateDeCreation(new \DateTime()); // Set creation date

        // récup del'id de la categorie de ce cours
        $catId = $request->query->get('category');
        // selection de la categorie si elle existe et null si elle n'existe pas
        $selectedCategory = $catId ? $categoryRepo->find($catId) : null;

        // si une categorie est selectionnée on l'assicie au cours
        if ($selectedCategory) {
            $cours->setCategorie($selectedCategory);
        }

        // creation du form lié à l'entité cours
        $form = $this->createForm(CoursType::class, $cours);
        // traitement requete https
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/cours',
                        $newFilename
                    );
                } catch (FileException) {
                    // Gestion des erreurs d'upload
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }

                // Store relative path or filename
                $cours->setImage('/uploads/cours/' . $newFilename);
            }

            // Réaffectation de la catégorie sélectionnée
            if ($selectedCategory) {
                $cours->setCategorie($selectedCategory);
            }
            // Préparation de l'entité pour l'insertion en base de données
            $entityManager->persist($cours);
            // Exécution de la requête SQL (INSERT)
            $entityManager->flush();
            // redirection vers liste des cours
            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }
        // affichage du formulaire de creation de cours
        return $this->render('back/cours/new.html.twig', [
            'cours' => $cours,
            'form' => $form,
            'selected_category' => $selectedCategory,
        ]);
    }

    /**
     * Affiche les détails d'un cours
     */
    #[Route('/cours/{id}', name: 'app_cours_show', methods: ['GET'])]
    public function coursShow(Cours $cours): Response
    {
        return $this->render('back/cours/show.html.twig', [
            'cours' => $cours,
        ]);
    }

    /**
     * Affiche la liste des ressources d'un cours spécifique
     */
    #[Route('/cours/{id}/resources', name: 'app_cours_resources', methods: ['GET'])]
    public function coursResources(Cours $cours): Response
    {
        return $this->render('back/cours/resources.html.twig', [
            'cours' => $cours,
            'ressources' => $cours->getRessources(),
        ]);
    }

    /**
     * Modifie un cours existant
     * Accessible uniquement aux administrateurs
     */
    #[Route('/cours/{id}/edit', name: 'app_cours_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function coursEdit(Request $request, Cours $cours, CoursRepository $coursRepository, SluggerInterface $slugger): Response
    {
        // creation du formulaire avec données existantes
        $form = $this->createForm(CoursType::class, $cours);
        // traitement requete https
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/cours',
                        $newFilename
                    );
                } catch (FileException) {
                    // Gestion des erreurs d'upload
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }

                $cours->setImage('/uploads/cours/' . $newFilename);
            }

            // sauvegarde de la categorie
            $coursRepository->save($cours, true);

            // Message de succès
            $this->addFlash('success', 'Le cours a été modifié avec succès !');

            // redirection vers liste des cours
            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        // Si le formulaire a été soumis mais n'est pas valide, afficher un message d'erreur
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Le formulaire contient des erreurs. Veuillez vérifier les champs en rouge.');
        }

        // affichage du formulaire de modification de cours
        return $this->render('back/cours/edit.html.twig', [
            'cours' => $cours,
            'form' => $form->createView(),
            'selected_category' => null,
        ]);
    }

    /**
     * Supprime un cours
     * Accessible uniquement aux administrateurs
     */
    #[Route('/cours/{id}', name: 'app_cours_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function coursDelete(Request $request, Cours $cours, CoursRepository $coursRepository): Response
    {
        // verif de token avant suppression
        if ($this->isCsrfTokenValid('delete'.$cours->getId(), $request->request->get('_token'))) {
            // suppression de la categorie
            $coursRepository->remove($cours, true);
        }
        // redirection vers liste des cours
        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }

    // ========================================
    // GESTION DES RESSOURCES
    // ========================================

    /**
     * Liste toutes les ressources avec filtres et tri
     */
    #[Route('/ressource', name: 'app_ressource_index', methods: ['GET'])]
    public function ressourceIndex(Request $request, RessourceRepository $ressourceRepository): Response
    {
        $filters = [
            'search' => $request->query->get('search'),
            'type'   => $request->query->get('type'),
            'cours'  => $request->query->get('cours'), // ID or Title
            'nom'    => $request->query->get('nom'),
        ];

        $sort = [
            'field' => $request->query->get('sort'),
            'order' => $request->query->get('order', 'DESC'),
        ];

        $ressources = $ressourceRepository->findWithFilters($filters, $sort);

        return $this->render('back/ressource/index.html.twig', [
            'ressources' => $ressources,
            'filters' => $filters,
            'sort' => $sort,
            'search' => $filters['search'], // Compatibility
        ]);
    }

    /**
     * Crée une nouvelle ressource
     * Accessible uniquement aux administrateurs
     */
    #[Route('/ressource/new', name: 'app_ressource_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function ressourceNew(Request $request, EntityManagerInterface $entityManager, CoursRepository $coursRepo, SluggerInterface $slugger): Response
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
            $nature = $form->get('nature')->getData();
            $file = $form->get('fichier')->getData();
            $contenu = $form->get('contenu')->getData();

            // Validate based on nature
            if ($nature === 'fichier' && !$file) {
                 $this->addFlash('error', 'Veuillez sélectionner un fichier.');
                 return $this->render('back/ressource/new.html.twig', [
                    'ressource' => $ressource,
                    'form' => $form->createView(),
                    'selected_course' => $selectedCourse,
                ]);
            }
            if ($nature === 'texte' && empty($contenu)) {
                $this->addFlash('error', 'Veuillez saisir du contenu texte.');
                 return $this->render('back/ressource/new.html.twig', [
                    'ressource' => $ressource,
                    'form' => $form->createView(),
                    'selected_course' => $selectedCourse,
                ]);
            }

            if ($nature === 'fichier' && $file) {
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

                    // Clear content if file uploaded (optional, but cleaner)
                    $ressource->setContenu(null);

                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload : ' . $e->getMessage());
                    return $this->render('back/ressource/new.html.twig', [
                        'ressource' => $ressource,
                        'form' => $form->createView(),
                        'selected_course' => $selectedCourse,
                    ]);
                }
            } elseif ($nature === 'texte') {
                $ressource->setType('FILE'); // Default type for text resources
                $ressource->setUrl(null);   // Ensure no URL
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

    /**
     * Affiche les détails d'une ressource
     */
    #[Route('/ressource/{id}', name: 'app_ressource_show', methods: ['GET'])]
    public function ressourceShow(Ressource $ressource): Response
    {
        return $this->render('back/ressource/show.html.twig', [
            'ressource' => $ressource,
        ]);
    }

    /**
     * Modifie une ressource existante
     * Accessible uniquement aux administrateurs
     */
    #[Route('/ressource/{id}/edit', name: 'app_ressource_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function ressourceEdit(Request $request, Ressource $ressource, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nature = $form->get('nature')->getData();
            $file = $form->get('fichier')->getData();
            $contenu = $form->get('contenu')->getData();

            if ($nature === 'fichier' && $file) {
                // ... file upload logic ...
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

                    // Clear content if switching to file and uploading new one
                    $ressource->setContenu(null);

                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload : ' . $e->getMessage());
                    return $this->render('back/ressource/edit.html.twig', [
                        'ressource' => $ressource,
                        'form' => $form->createView(),
                    ]);
                }
            } elseif ($nature === 'texte') {
                if (empty($contenu)) {
                    $this->addFlash('error', 'Veuillez saisir du contenu texte.');
                    return $this->render('back/ressource/edit.html.twig', [
                        'ressource' => $ressource,
                        'form' => $form->createView(),
                    ]);
                }
                $ressource->setType('FILE');
                $ressource->setUrl(null);
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

    /**
     * Supprime une ressource
     */
    #[Route('/ressource/{id}', name: 'app_ressource_delete', methods: ['POST'])]
    public function ressourceDelete(Request $request, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ressource->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ressource);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ressource_index', [], Response::HTTP_SEE_OTHER);
    }
}

