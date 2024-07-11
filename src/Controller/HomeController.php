<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\LikesHistory;
use App\Repository\CategoryRepository;
use App\Repository\LikesHistoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(PostRepository $postRepository,LikesHistoryRepository $likesHistoryRepository, PaginatorInterface $paginator, Request $request): Response
    {

    $sort = $request->query->get('sort', 'date_desc');
    switch ($sort) {
        case 'date_asc':
            $orderBy = ['date' => 'ASC'];
            break;
        case 'thumbsup_desc':
            $orderBy = ['likeCount' => 'DESC'];
            break;
        case 'thumbsup_asc':
            $orderBy = ['likeCount' => 'ASC'];
            break;
        case 'thumbsdown_desc':
            $orderBy = ['dislikeCount' => 'DESC'];
            break;
        case 'thumbsdown_asc':
            $orderBy = ['dislikeCount' => 'ASC'];
            break;
        case 'date_desc':
        default:
            $orderBy = ['date' => 'DESC'];
            break;
    }

        $posts = $postRepository->findBy([], $orderBy);
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
            'pagination' => $pagination,
            'currentSort' => $sort

        ]);

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

    public function getcatergory($navslug,CategoryRepository $categoryRepository):Response 
    {
        $categorys = $categoryRepository->findBy(array(),array('id' => 'ASC'));
        if($navslug=="sidebar"){
            return $this->render('navbar/_sidebar_categorys.html.twig', [
                'categorys' => $categorys
                        
        ]);
        } elseif($navslug=="headnavbar"){
            return $this->render('navbar/_navbar_categorys.html.twig', [
                'categorys' => $categorys
        ]);
        }
        
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

        $sort = $request->query->get('sort', 'date_desc');
        switch ($sort) {
        case 'date_asc':
            $orderBy = ['date' => 'ASC'];
            break;
        case 'thumbsup_desc':
            $orderBy = ['likeCount' => 'DESC'];
            break;
        case 'thumbsup_asc':
            $orderBy = ['likeCount' => 'ASC'];
            break;
        case 'thumbsdown_desc':
            $orderBy = ['dislikeCount' => 'DESC'];
            break;
        case 'thumbsdown_asc':
            $orderBy = ['dislikeCount' => 'ASC'];
            break;
        case 'date_desc':
        default:
            $orderBy = ['date' => 'DESC'];
            break;
    }
        //$posts = $postRepository->findBy(['category' => $category->getId()], ['id' => 'DESC']); $orderBy

        $posts = $postRepository->findBy(['category' => $category->getId()], $orderBy);

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
            'pagination' => $pagination,
            'currentSort' => $sort
        ]);
    }


    
}