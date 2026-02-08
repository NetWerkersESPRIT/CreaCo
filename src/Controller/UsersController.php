<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UserType;
use App\Form\UsersType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UsersController extends AbstractController
{
    #[Route('/user/new', name: 'app_useradd')]
    public function createuser(Request $request, EntityManagerInterface $em, \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new Users();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $em->persist($user);
            $em->flush();

            // Handle Manager/Creator selection
            $role = $user->getRole();
            if ($role === 'ROLE_MANAGER') {
                $creator = $form->get('creatorSelection')->getData();
                if ($creator) {
                    $user->setCreatorId($creator->getId());
                }
            } elseif ($role === 'ROLE_CREATOR') {
                $manager = $form->get('managerSelection')->getData();
                if ($manager) {
                    $user->setManagerId($manager->getId());
                }
            } elseif ($role === 'ROLE_EDITOR') {
                $creator = $form->get('creatorSelection')->getData();
                $manager = $form->get('managerSelection')->getData();
                if ($creator) {
                    $user->setCreatorId($creator->getId());
                }
                if ($manager) {
                    $user->setManagerId($manager->getId());
                }
            }

            $user->setGroupid($user->getId());
            $em->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users', name: 'app_users_index', methods: ['GET'])]
    public function index(UsersRepository $usersRepository): Response
    {
        return $this->render('back/users/index.html.twig', [
            'users' => $usersRepository->findAll(),
        ]);
    }

    #[Route('/admin/users/new', name: 'app_users_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/users/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/admin/users/{id}', name: 'app_users_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Users $user): Response
    {
        return $this->render('back/users/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/admin/users/{id}/edit', name: 'app_users_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/admin/users/{id}', name: 'app_users_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
    }
}
