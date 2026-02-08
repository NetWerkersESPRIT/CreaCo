<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UsersRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/admin.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_user_edit')]
    public function edit(UsersRepository $user): Response
    {
        // build form here later
        return $this->render('user/edit.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/delete/{id}', name: 'user_delete')]
    public function delete( int $id, EntityManagerInterface $em,  UsersRepository $repo): Response {
        $user = $repo->find($id);

        if ($user) {
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('app_admin');
    }
}
