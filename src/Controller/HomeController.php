<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\LikesHistory;
use App\Repository\CategoryRepository;
use App\Repository\LikesHistoryRepository;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(PostRepository $postRepository,LikesHistoryRepository $likesHistoryRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $posts = $postRepository->findBy(array(),array('date'=>'DESC'));
        $likestatuses = $likesHistoryRepository->findBy(['uid' => '1']);
        //$post_to_show = $postRepository->findBy(array(),array('id'=>'DESC'),$posts_per_page,$posts_started_by);

        $pagination = $paginator->paginate(
        $posts, /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
        10 /*limit per page*/);
        $pagination->setTemplate('home/my_pagination.html.twig');

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'likestatuses' => $likestatuses,
            'pagination' => $pagination
        ]);

    }

    #[Route('/view/{id}', name:'view')]
    public function view($id, PostRepository $pr): Response 
    {
        $post = $pr->find($id);
        if($post){
            return $this->render('home/view.html.twig', [
            'post' => $post
        ]);
        } else {
            $this->addFlash('failure', 'This post does not exist!');
            return $this->redirect($this->generateUrl('home'));
        }
        
    }

    public function getlikestatus($postid,LikesHistoryRepository $likesHistoryRepository): Response
    {
        $user = empty($this->getUser());
        if(!$user){$uid=$this->getUser()->getId();} else {$uid=0;};
        //$likestatuses = $likesHistoryRepository->findBy(['uid' => $uid, 'postid' => $postid]);
        $likestatuses = $likesHistoryRepository->findBy(['uid' => $uid, 'postid' => $postid]);
        if($likestatuses) {
            return new response("disabled");
        } elseif($user){
            return new response("disabled");
        }
        return new response();

    }

    public function getcatergory(CategoryRepository $categoryRepository):Response 
    {
        $categorys = $categoryRepository->findBy(array(),array('id' => 'ASC'));
        $count = count($categorys);
        
        return $this->render('navbar/_sidebar_categorys.html.twig', [
            'categorys' => $categorys
        ]);
    }

    #[Route('/category/{idOrSlug}', name: 'showcategory', requirements: ['idOrSlug' => '[a-zA-Z0-9\-]+'])]
    public function showcategorybyidOrslug($idOrSlug, PostRepository $postRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if (is_numeric($idOrSlug)) {
            $category = $categoryRepository->find($idOrSlug);
        } else {
            $categoryName = str_replace('-', ' ', $idOrSlug);
            $category = $categoryRepository->findOneBy(['name' => $categoryName]);
        }

        if (!$category) {
            return $this->redirectToRoute('home');
        }

        $posts = $postRepository->findBy(['category' => $category->getId()], ['id' => 'DESC']);

        if (empty($posts)) {
            return $this->redirectToRoute('home');
        }

        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', 1),
            10
        );
        $pagination->setTemplate('home/my_pagination.html.twig');

        return $this->render('home/showcategory.html.twig', [
            'posts' => $posts,
            'pagination' => $pagination
        ]);
    }


    
}