<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\PostType;
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

        if ($form->isSubmitted())
        {
        $eingabe = $form->getData();
        $date= date('Y-m-d H:i:s');

        //$category = new Category();
        //$category->setName('Home');
        
        $newPost->setAuthorid('1');
        $newPost->setName($eingabe['name']);
        $newPost->setText($eingabe['text']);
        $newPost->setStatus('1');
        $newPost->setDate($date);
        $newPost->setCategory($eingabe['category']);
        
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

        if ($form->isSubmitted())
        {
        $eingabe = $form->getData();

        $category->setName($eingabe['name']);

        $em->persist($category);
        $em->flush();

        $this->addFlash('erfolg', 'New Category has been created successfully!');
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
        if(!$post)
        {
            $this->addFlash('erfolg', 'ID exestiert nicht');
        }
        $post->incrementLikeCount();
        $em->flush();
        //return $this->redirect($this->generateUrl('home'));
        return new JsonResponse(['likes' => $post->getLikeCount(), 'dislikes' => $post->getDislikeCount()]);
    }

    #[Route('/thumpsdown/{id}', name: 'thumpsdown')]
    public function thumpsdown($id, EntityManagerInterface $em) 
    {
        $post = $em->getRepository(Post::class)->find($id);
        if(!$post)
        {
            $this->addFlash('erfolg', 'ID exestiert nicht');
        }
        //$newDislikeCount = $post->incrementDislikeCount();
        $post->incrementDislikeCount();
        $em->flush();
        //return $this->redirect($this->generateUrl('home'));
        return new JsonResponse(['likes' => $post->getLikeCount(), 'dislikes' => $post->getDislikeCount()]);

    }

    /*    #[Route('/newpost', name: 'newpost')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $newPost = new Post();
        $form = $this->createForm(PostType::class, $newPost);
        $form->handleRequest($request);


        if ($form->isSubmitted())
        {

        $em->persist($newPost);
        $em->flush();

        $this->addFlash('erfolg', 'Your message has been posted successfully!');
        return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('post/index.html.twig', [
            'newPostForm' => $form->createView(),
        ]);

    }*/


}