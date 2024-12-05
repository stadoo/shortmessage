<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\LikesHistory;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostEditFormType;
use App\Form\PostType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    #[Route('/view/{id}', name:'view')]
    public function view($id, PostRepository $pr, CommentRepository $cmr , Request $request, EntityManagerInterface $em): Response 
    {
        $post = $pr->find($id);
        if($post){
            $comments = $cmr->findBy(['post' => $post], ['date' => 'DESC']);

            $comment = new Comment();
            $form = $this->createFormBuilder()
                ->add('content', TextareaType::class, [
                'label' => 'Comment',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control',
                            'placeholder'=> 'Comment',
                            'row' => 3
                            ]
            ])
            ->getForm();
            $form->handleRequest($request);
            $eingabe = $form->getData();

            if ($form->isSubmitted() && $form->isValid()) { 
                $comment->setPost($post);
                $comment->setAuthor($this->getUser());
                $comment->setText($eingabe['content']);
                $comment->setStatus('1');
                $comment->setDate(new \DateTime());
                $comment->setLikeCount(0);
                $comment->setDislikeCount(0);

                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('view', ['id' => $post->getId()]);
            }

            return $this->render('post/view.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'form' => $form->createView()
        ]);
        } else {
            $this->addFlash('failure', 'This post does not exist!');
            return $this->redirect($this->generateUrl('home'));
        }
        
    }

    #[Route('/post/comment/delete/{id}', name: 'comment_delete', methods: ['GET'])]
    public function deleteComment($id, Request $request, EntityManagerInterface $em, Comment $comment): Response
    {

        $comment = $em->getRepository(Comment::class)->find($id);
        // CSRF Token validation
        $token = $request->query->get('token');
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $token)) {
        
            if($comment->getAuthor() == $this->getUser() || $this->isGranted('ROLE_ADMIN') )
            {
                $em->remove($comment);
                $em->flush();

                $this->addFlash('success', 'Your post has been deleted successfully!');
            } else {
                $this->addFlash('failure', 'You are not the owner of this post!');
            }
        } else {
            $this->addFlash('error', 'Invalid CSRF token');
        }


        return $this->redirectToRoute('view', ['id' => $comment->getPost()->getId()]);
    }
    
    #[Route('/newpost', name: 'newpost')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $newPost = new Post();
        $form = $this->createFormBuilder()
        ->add('name', TextType::class,[
            'label' => 'Titel',
            'attr' => array('class' => 'form-control')
        ])
        ->add('text', TextareaType::class, [
            'label' => 'Message',
            'attr' => array('class' => 'form-control')
        ])
        ->add('category', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name',
            
        ])
        ->add('post', SubmitType::class, [
            'attr' => array('class' => 'btn btn-outline-danger btn-sm')
        ])
        ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
        $eingabe = $form->getData();
        $date= date('Y-m-d H:i:s');

        //$category = new Category();
        //$category->setName('Home');
        
        $newPost->setAuthor($this->getUser());
        $newPost->setName($eingabe['name']);
        $newPost->setText($eingabe['text']);
        $newPost->setStatus('1');
        $newPost->setDate(new \DateTime());
        $newPost->setCategory($eingabe['category']);
        $newPost->setLikeCount(0);
        $newPost->setDislikeCount(0);
        
        //$em->persist($category);
        $em->persist($newPost);
        $em->flush();
        //$this->addFlash('erfolg', 'Your message has been posted successfully!');
        ///return $this->redirect($this->generateUrl('home'));
        return $this->redirectToRoute('view', ['id' => $newPost->getId()]);


        }

        return $this->render('post/index.html.twig', [
            'newPostForm' => $form->createView(),
        ]);

    }

    #[Route('/edit/{id}', name:'editpost')]
    public function editpost($id, EntityManagerInterface $em, Request $request): Response
    {   
        $post=$em->getRepository(Post::class)->find($id);
        if($post->getAuthor() == $this->getUser()  || $this->isGranted('ROLE_ADMIN') )
        {
            $form = $this->createForm(PostEditFormType::class, $post);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid())
            {
                $eingabe = $form->getData();

                $post->setName($form->get('name')->getData());
                $post->setText($form->get('text')->getData());
                $post->setCategory($form->get('category')->getData());

                $em->persist($post);
                $em->flush();

                $this->addFlash('success', 'Your message has been edited successfully!');
                return $this->redirect($this->generateUrl('home'));
                
            }
            return $this->render('post/edit.html.twig',[
                'editForm' => $form->createView(),
                'post' => $post
            ]);
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id, EntityManagerInterface $em)
    {
        $post = $em->getRepository(Post::class)->find($id);
        if($post->getAuthor() == $this->getUser() || $this->isGranted('ROLE_ADMIN') )
        {
            $em->remove($post);
            $em->flush();

            $this->addFlash('success', 'Your post has been deleted successfully!');
        } else {
            $this->addFlash('failure', 'You are not the owner of this post!');
        }
        return $this->redirect($this->generateUrl('home')); 
    }

    

    


    public function showsearchbar(Request $request):Response
    {
        // $form = $this->createFormBuilder(null, [
        //         'method' => 'GET',
        //         'csrf_protection' => true,
        //         'action' => $this->generateUrl('post_search')
        //     ])
        //     ->add('query', SearchType::class, [
        //         'label' => false,
        //         'attr' => ['placeholder' => 'Search...']
        //     ])
        //     ->getForm();
        

        return $this->render('post/searchbar.html.twig'
        //, ['searchform' => $form->createView(),]
    );
    }

    
    #[Route('/search', name: 'post_search')]
    public function search(Request $request, PostRepository $postRepository, PaginatorInterface $paginator): Response
    {
        /*
        $form = $this->createFormBuilder()
        ->setMethod('GET')
        ->setAction($this->generateUrl('post_search'))
        ->add('query', SearchFieldType::class, [
                'label' => 'Search',
                'required' => false,
                'attr' => ['placeholder' => 'Search...']

        ])
        ->getForm();
        $form->handleRequest($request);

        $posts = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->get('query')->getData();

            if ($query) {
                $posts = $postRepository->findBySearchQuery($query);

                $pagination = $paginator->paginate(
                $posts,
                $request->query->getInt('page', 1),10);
                $pagination->setTemplate('home/my_pagination.html.twig');


                return $this->render('post/searchoverview.html.twig', [
                    'searchform' => $form->createView(), 
                    'posts' => $posts,
                    'pagination' => $pagination
            ]);
            }
            
        }

        return $this->render('post/searchbar.html.twig', [
            'searchform' => $form->createView()
            ]);
            */
        
        $query = $request->query->get('query', '');

        if ($query) {
            $posts = $postRepository->findBySearchQuery($query);
            $pagination = $paginator->paginate(
                $posts,
                $request->query->getInt('page', 1),10);
                $pagination->setTemplate('home/my_pagination.html.twig');
        } else {
            $posts = [];
        }

                


            return $this->render('post/search_results.html.twig', [
                    'posts' => $posts,
                    'query' => $query,
                    'pagination' => $pagination
            ]);
        
            

    }

    #[Route('/thumbsup/{id}', name: 'thumbsup')]
    public function thumbsup($id, EntityManagerInterface $em) 
    {
        $post = $em->getRepository(Post::class)->find($id);
        $like = $em->getRepository(LikesHistory::class)->findOneBy(['uid' => $this->getUser()->getId(), 'postid' => $id]);
        
        if (!$post) {
            $this->addFlash('failure', 'ID existiert nicht');
            return new JsonResponse(['error' => 'ID existiert nicht']);
        }

        if(!$like){
        //create new entry in LikesHistory with uid and postid
            $date= date('Y-m-d H:i:s');
            $newLike = new LikesHistory();
            $newLike->setUid($this->getUser()->getId());
            $newLike->setPostid($id);
            $newLike->setLikestatus('1'); // Like is 1 Dislike is 0
            $newLike->setDate(new \DateTime());
            $em->persist($newLike);
            $em->flush();

            $post->incrementLikeCount();
            $em->flush();
            
            return new JsonResponse(['likes' => $post->getLikeCount(), 'dislikes' => $post->getDislikeCount(), 'status' => '1']);
        }
        return new JsonResponse(['likes' => $post->getLikeCount(), 'dislikes' => $post->getDislikeCount(), 'status' => $like->getLikestatus()]);
    }

    #[Route('/thumbsdown/{id}', name: 'thumbsdown')]
    public function thumbsdown($id, EntityManagerInterface $em) 
        {
        $post = $em->getRepository(Post::class)->find($id);
        $like = $em->getRepository(LikesHistory::class)->findOneBy(['uid' => $this->getUser()->getId(), 'postid' => $id]);
        
        if (!$post) {
            $this->addFlash('failure', 'ID existiert nicht');
            return new JsonResponse(['error' => 'ID existiert nicht']);
        }

        if(!$like){
        //create new entry in LikesHistory with uid and postid
            $date= date('Y-m-d H:i:s');
            $newLike = new LikesHistory();
            $newLike->setUid($this->getUser()->getId());
            $newLike->setPostid($id);
            $newLike->setLikestatus('0'); // Like is 1 Dislike is 0
            $newLike->setDate(new \DateTime());
            $em->persist($newLike);
            $em->flush();

            $post->incrementDislikeCount();
            $em->flush();
            
            return new JsonResponse(['likes' => $post->getLikeCount(), 'dislikes' => $post->getDislikeCount(), 'status' => '1']);
        }
        return new JsonResponse(['likes' => $post->getLikeCount(), 'dislikes' => $post->getDislikeCount(), 'status' => $like->getLikestatus()]);
    }
    
    #[Route('/thumbsstatus/{id}', name: 'thumbsstatus')]
    public function likesdislikestatus($id, EntityManagerInterface $em):Response
    {
        $post = $em->getRepository(Post::class)->find($id);
                //$like = $em->getRepository(LikesHistory::class)->findOneBy(['uid' => $this->getUser()->getId(), 'postid' => $id]);

        if (!$post) {
            $this->addFlash('failure', 'ID existiert nicht');
            return new JsonResponse(['error' => 'ID existiert nicht']);
        }

        return new JsonResponse(['likes' => $post->getLikeCount(), 'dislikes' => $post->getDislikeCount()]);


    }

        #[Route('/like/{id}/{status}', name: 'like')]
    public function like($id,$status, EntityManagerInterface $em) 
    {
        $post = $em->getRepository(Post::class)->find($id);
        $like = $em->getRepository(LikesHistory::class)->findOneBy(['uid' => $this->getUser()->getId(), 'postid' => $id]);
        
        if (!$post) {
            $this->addFlash('failure', 'ID existiert nicht');
            return new JsonResponse(['error' => 'ID existiert nicht']);
        }

        if(!$like){
        //create new entry in LikesHistory with uid and postid
            $date= date('Y-m-d H:i:s');
            $newLike = new LikesHistory();
            $newLike->setUid($this->getUser()->getId());
            $newLike->setPostid($id);
            $newLike->setLikestatus($status); // Like is 1 Dislike is 0
            $newLike->setDate(new \DateTime());
            $em->persist($newLike);
            $em->flush();
            if($status){
                $post->incrementLikeCount();
            } elseif(!$status) {
                $post->incrementDislikeCount();
            }
            $em->flush();
            
            return new JsonResponse(['likes' => $post->getLikeCount(), 'dislikes' => $post->getDislikeCount(), 'status' => '1']);
        }
        return new JsonResponse(['likes' => $post->getLikeCount(), 'dislikes' => $post->getDislikeCount(), 'status' => $like->getLikestatus()]);
    }


}