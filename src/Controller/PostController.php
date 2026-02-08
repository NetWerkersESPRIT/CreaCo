<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
 
#[Route('/forum')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class PostController extends AbstractController
{
    #[Route('', name: 'forum_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $query = $request->query->get('q');
        
        if ($query) {
            $posts = $entityManager->getRepository(Post::class)->createQueryBuilder('p')
                ->where('p.title LIKE :query OR p.content LIKE :query')
                ->setParameter('query', '%' . $query . '%')
                ->orderBy('p.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            $posts = $entityManager->getRepository(Post::class)->findBy([], ['createdAt' => 'DESC']);
        }

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $now = new \DateTimeImmutable();
            $post->setCreatedAt($now);
            $post->setUpdatedAt($now);
            
            // Automatically set the logged in user as author
            $user = $this->getUser();
            if (!$user) {
                $this->addFlash('danger', 'Vous devez être connecté pour publier un post.');
                return $this->redirectToRoute('app_login');
            }
            $post->setUser($user);

            // Set defaults if null
            if ($post->getStatus() === null) {
                $post->setStatus('published');
            }

            if ($form->isValid()) {
                /** @var UploadedFile $imageFile */
                $imageFile = $form->get('imageFile')->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFilename
                        );
                        $post->setImageName($newFilename);
                    } catch (FileException $e) {
                         $this->addFlash('danger', 'Erreur lors de l’upload de l’image.');
                    }
                }

                /** @var UploadedFile $pdfFile */
                $pdfFile = $form->get('pdfFile')->getData();
                if ($pdfFile) {
                    $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                    try {
                        $pdfFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFilename
                        );
                        $post->setPdfName($newFilename);
                    } catch (FileException $e) {
                         $this->addFlash('danger', 'Erreur lors de l’upload du PDF.');
                    }
                }

                $em->persist($post);
                $em->flush();

                $this->addFlash('success', 'Votre message a été publié avec succès !');

                return $this->redirectToRoute('forum_index');
            }
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function show(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setPost($post);
            
            // Handle reply
            $parentId = $request->request->get('parent_id');
            if ($parentId) {
                $parentComment = $em->getRepository(Comment::class)->find($parentId);
                if ($parentComment) {
                    $comment->setParentComment($parentComment);
                }
            }

            // Automatically set user
            if ($this->getUser()) {
                 $comment->setUser($this->getUser());
            } else {
                 $this->addFlash('danger', 'Vous devez être connecté pour commenter.');
                 return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
            }

            $now = new \DateTimeImmutable();
            $comment->setCreatedAt($now);
            $comment->setUpdatedAt($now);
            $comment->setStatus('visible');
            $comment->setLikes(0);

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté !');

            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        // Also check for manual reply submission (outside of symfony form)
        if ($request->isMethod('POST') && $request->request->get('comment_body')) {
            $body = $request->request->get('comment_body');
            $parentId = $request->request->get('parent_id');

            $reply = new Comment();
            $reply->setBody($body);
            $reply->setPost($post);
            
            if ($this->getUser()) {
                $reply->setUser($this->getUser());
            } else {
                $this->addFlash('danger', 'Vous devez être connecté pour répondre.');
                return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
            }

            if ($parentId) {
                $parentComment = $em->getRepository(Comment::class)->find($parentId);
                if ($parentComment) {
                    $reply->setParentComment($parentComment);
                }
            }

            $reply->setCreatedAt(new \DateTimeImmutable());
            $reply->setStatus('visible');
            $reply->setLikes(0);

            $em->persist($reply);
            $em->flush();

            $this->addFlash('success', 'Votre réponse a été ajoutée !');
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        // Only show top-level comments in primary loop
        $comments = $em->getRepository(Comment::class)->findBy(
            ['post' => $post, 'parentComment' => null],
            ['createdAt' => 'ASC']
        );

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $comments, // Pass filtered comments
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route('/{id}/comment/{commentId}/solution', name: 'app_post_mark_solution', methods: ['GET'], requirements: ['id' => '\d+', 'commentId' => '\d+'])]
    public function markSolution(Post $post, int $commentId, EntityManagerInterface $em): Response
    {
        // Security check: ensure current user is a manager
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Veuillez vous connecter.');
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        // Check if user is manager (string comparison as requested)
        $currentRole = strtolower(trim($user->getRole()));
        if ($currentRole !== 'manager') {
             $this->addFlash('danger', sprintf('Accès refusé. Seuls les managers peuvent marquer une solution. Votre rôle actuel est : "%s".', $user->getRole()));
             return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        // Find the comment
        $comment = $em->getRepository(Comment::class)->find($commentId);
        if (!$comment || $comment->getPost() !== $post) {
            throw $this->createNotFoundException('Comment not found in this post.');
        }

        $post->setSolution($comment);
        $post->setStatus('solved'); // Use 'solved' as defined in Entity Choice
        $em->flush();

        $this->addFlash('success', 'Le message a été marqué comme résolu ! Plus aucun commentaire ne peut être ajouté.');

        return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        // Ownership check: only the author can edit
        if ($this->getUser() !== $post->getUser()) {
             $this->addFlash('danger', 'Vous n’êtes pas autorisé à modifier ce post.');
             return $this->redirectToRoute('forum_index');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUpdatedAt(new \DateTimeImmutable());

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                // Delete old image if exists
                if ($post->getImageName()) {
                    $oldPath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $post->getImageName();
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFilename
                    );
                    $post->setImageName($newFilename);
                } catch (FileException $e) {
                     $this->addFlash('danger', 'Erreur lors de l’upload de l’image.');
                }
            }

            /** @var UploadedFile $pdfFile */
            $pdfFile = $form->get('pdfFile')->getData();
            if ($pdfFile) {
                // Delete old PDF if exists
                if ($post->getPdfName()) {
                    $oldPath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $post->getPdfName();
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFilename
                    );
                    $post->setPdfName($newFilename);
                } catch (FileException $e) {
                     $this->addFlash('danger', 'Erreur lors de l’upload du PDF.');
                }
            }

            $em->flush();

            $this->addFlash('success', 'Le message a été mis à jour.');

            return $this->redirectToRoute('forum_index');
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_post_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        // Ownership check: only the author can delete
        if ($this->getUser() !== $post->getUser()) {
             $this->addFlash('danger', 'Vous n’êtes pas autorisé à supprimer ce post.');
             return $this->redirectToRoute('forum_index');
        }

        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $em->remove($post);
            $em->flush();
            $this->addFlash('success', 'Le message a été supprimé.');
        }

        return $this->redirectToRoute('forum_index');
    }

    #[Route('/{id}/like', name: 'app_post_like', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function like(Post $post, EntityManagerInterface $em): Response
    {
        $post->setLikes($post->getLikes() + 1);
        $em->flush();

        return $this->redirectToRoute('forum_index');
    }
}
