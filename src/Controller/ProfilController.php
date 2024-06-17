<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\PostRemove;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
  #[Route('/user/profil',name:'user_profil')]
  public function index(EntityManagerInterface $em ,PostRepository $postRepository, PaginatorInterface $paginator, Request $request):Response {
    $posts = $em->getRepository(Post::class)->findBy(['author' => $this->getUser()]);

    $pagination = $paginator->paginate(
        $posts, /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
        10 /*limit per page*/);
        $pagination->setTemplate('home/my_pagination.html.twig');
        
    return $this->render('profil/userprofil.html.twig', [
      'posts'=>$posts,
      'pagination' => $pagination,

  ]);
  }

  #[Route('/user/view/{id}',name:'user_view')]
  public function viewUser($id, EntityManagerInterface $em, PaginatorInterface $paginator, Request $request):Response {
    
    $user=$em->getRepository(User::class)->find($id);

    $posts = $em->getRepository(Post::class)->findBy(['author' => $id]);

    $pagination = $paginator->paginate(
        $posts, /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
        10 /*limit per page*/);
        $pagination->setTemplate('home/my_pagination.html.twig');


    return $this->render('profil/userview.html.twig', [
      'user'=>$user,
      'posts'=>$posts,
      'pagination' => $pagination,

  ]);
  }
}