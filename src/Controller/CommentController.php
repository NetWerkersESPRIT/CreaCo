<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{
    #[Route('/post/{id}/comment/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        $comment = new Comment();

        // lier le commentaire au post directement
        $comment->setPost($post);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();
            $comment->setCreatedAt($now);
            $comment->setUpdatedAt($now);
            $comment->setUser($this->getUser());
            if ($comment->getStatus() === null) {
                $comment->setStatus('visible');
            }
            if ($comment->getLikes() === null) {
                $comment->setLikes(0);
            }
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        return $this->render('comment/new.html.twig', [
            'form' => $form,
            'post' => $post,
        ]);
    }

    #[Route('/comment/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setUpdatedAt(new \DateTimeImmutable());

            $em->flush();

            return $this->redirectToRoute('app_post_show', [
                'id' => $comment->getPost()->getId()
            ]);
        }

        return $this->render('comment/edit.html.twig', [
            'form' => $form,
            'comment' => $comment,
        ]);
    }

    #[Route('/comment/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $em): Response
    {
        $postId = $comment->getPost()->getId();

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $em->remove($comment);
            $em->flush();
        }

        return $this->redirectToRoute('app_post_show', ['id' => $postId]);
    }
}
