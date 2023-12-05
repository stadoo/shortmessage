<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $posts = $postRepository->findBy(array(),array('id'=>'DESC'));
        //$post_to_show = $postRepository->findBy(array(),array('id'=>'DESC'),$posts_per_page,$posts_started_by);

        $pagination = $paginator->paginate(
        $posts, /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
        10 /*limit per page*/);
        $pagination->setTemplate('home/my_pagination.html.twig');


        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'pagination' => $pagination
        ]);
    }
    
}