<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     */
    public function index(CommentRepository $repo, $page = 1, PaginationService $pagination)
    {   
        $pagination->setEntityClass(Comment::class)
                    ->setCurrentPage($page)
                    ->setLimit(5);
        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    
    /**
     * Permet d'editer le commentaire
     *
     * @Route("/admin/comments/{id}/edit", name="admin_comments_edit")
     *  
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */

    public function edit(Comment $comment, EntityManagerInterface $manager, Request $request)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash(
                'success',
                'Les modifications ont bien été enregistrées'
            );
            return $this->redirectToRoute('admin_comments_index');
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     *
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     * 
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function delete(Comment $comment , EntityManagerInterface $manager)
    {
        $manager->remove($comment);
        $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire de l'annonce a bien été supprimé"
            );
            return $this->redirectToRoute('admin_comments_index');
    }
}
