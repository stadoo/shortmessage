<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\LikesHistory;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostEditFormType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
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
        
        $newPost->setAuthorid($this->getUser()->getId());
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
                return $this->redirect($request->getUri());

        }

        return $this->render('post/index.html.twig', [
            'newPostForm' => $form->createView(),
        ]);

    }

    #[Route('/edit/{id}', name:'editpost')]
    public function editpost($id, EntityManagerInterface $em, Request $request): Response
    {   
        $post=$em->getRepository(Post::class)->find($id);
        if($post->getAuthorid() == $this->getUser()->getId()  || $this->isGranted('ROLE_ADMIN') )
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
        if($post->getAuthorid() == $this->getUser()->getId() || $this->isGranted('ROLE_ADMIN') )
        {
            $em->remove($post);
            $em->flush();

            $this->addFlash('success', 'Your post has been deleted successfully!');
        } else {
            $this->addFlash('failure', 'You are not the owner of this post!');
        }
        return $this->redirect($this->generateUrl('home')); 
    }

    

    #[Route('/newcategory', name: 'newcategory')]
    public function newcategory(EntityManagerInterface $em, Request $request): Response
    {      
        $category = new Category();
        $form = $this->createFormBuilder()
        ->add('name', TextType::class,[
            'label' => 'Category name',
            'attr' => array('class' => 'form-control')
        ])
        ->add('post', SubmitType::class, [
            'attr' => array('class' => 'btn btn-outline-danger btn-sm')
        ])
        ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
        $eingabe = $form->getData();

        $category->setName($eingabe['name']);

        $em->persist($category);
        $em->flush();

        $this->addFlash('success', 'New Category has been created successfully!');
        return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('post/newcategory.html.twig', [
            'newPostForm' => $form->createView(),
        ]);
    }

    #[Route('/thumpsup/{id}', name: 'thumpsup')]
    public function thumpsup($id, EntityManagerInterface $em) 
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

    #[Route('/thumpsdown/{id}', name: 'thumpsdown')]
    public function thumpsdown($id, EntityManagerInterface $em) 
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