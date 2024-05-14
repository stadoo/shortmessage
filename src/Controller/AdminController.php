<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/user/list', name: 'admin_userlist')]
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $users = $userRepository->findBy(array(),array('id'=>'ASC'));

        $pagination = $paginator->paginate(
        $users, /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
        10 /*limit per page*/);
        $pagination->setTemplate('home/my_pagination.html.twig');

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'pagination' => $pagination
        ]);

    }
}