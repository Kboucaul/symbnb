<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\PaginationService;
use App\Repository\CommentRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="admin_users_index")
     */
    public function index(CommentRepository $repo, $page = 1, PaginationService $pagination)
    {
        $pagination->setEntityClass(User::class)
                    ->setCurrentPage($page)
                    ->setLimit(5);
        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination,
            'page' => $pagination->getCurrentPage()
        ]);
    }
}
