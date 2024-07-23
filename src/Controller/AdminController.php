<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    private UserPasswordHasherInterface $userPasswordHasher;

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

        #[Route('/admin/user/edit/{id}', name:'admin_useredit')]
    public function editpost($id, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher , Request $request): Response
    {   
        $this->userPasswordHasher = $userPasswordHasher;

        $user=$em->getRepository(User::class)->find($id);
        if($this->isGranted('ROLE_ADMIN') )
        {
        $formEmail = $this->createFormBuilder()
        ->add('email', EmailType::class,[
            'label' => 'Email',
            'attr' => array('class' => 'form-control','value' => $user->getEmail())
        ])->add('submit', SubmitType::class, [
            'attr' => array('class' => 'btn btn-outline-danger btn-sm')
        ])
        ->getForm();

        $formEmail->handleRequest($request);
        $fromPassword = $this->createFormBuilder()
        ->add('password', PasswordType::class, [
            'attr' => array('class' => 'form-control')
        ])
        ->add('submit', SubmitType::class, [
            'attr' => array('class' => 'btn btn-outline-danger btn-sm')
        ])
        ->getForm();

        $fromPassword->handleRequest($request);
        $formRoles = $this->createFormBuilder($user)
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                    'Manager' => 'ROLE_MANAGER',
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Roles'
            ])
            ->add('submit', SubmitType::class, [
            'attr' => array('class' => 'btn btn-outline-danger btn-sm')
            ])
            ->getForm();
        $formRoles->handleRequest($request);
        if ($formEmail->isSubmitted() && $formEmail->isValid())
        {
                $user->setEmail($formEmail->get('email')->getData());

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'The User has been edited successfully!');
                return $this->redirect($this->generateUrl('home'));
                
        } elseif($fromPassword->isSubmitted() && $fromPassword->isValid()) {

            $hashedPassword = $this->userPasswordHasher->hashPassword($user, $fromPassword->get('password')->getData());
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'The User Password has been edited successfully!');
            return $this->redirect($this->generateUrl('home'));
            
        } elseif($formRoles->isSubmitted() && $formRoles->isValid()) {

            $roles = $formRoles->get('roles')->getData();
            $user->setRoles($roles);
            
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User roles updated successfully');

            return $this->redirectToRoute('admin_useredit', ['id' => $user->getId()]);
        }
            return $this->render('admin/edit_user.html.twig',[
                'editFormEmail' => $formEmail->createView(),
                'editFormPassword' => $fromPassword->createView(),
                'editFormRoles' => $formRoles->createView(),
                'post' => $user
            ]);
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    #[Route('/admin/user/remove/{id}', name:'admin_userremove')]
    public function removeUser(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);
        //$posts = $em->getRepository(Post::class)->findBy(['author'=> $id]);
        

        // CSRF Token validation
        $token = $request->query->get('token');
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $token)) {
        
            if( $this->isGranted('ROLE_ADMIN') )
            {
                $deleteComments = $em->createQuery('DELETE FROM App\Entity\Comment c WHERE c.author = :authorId');
                $deleteComments->setParameter('authorId', $id);
                $deleteComments->execute();

                $deletePosts = $em->createQuery('DELETE FROM App\Entity\Post p WHERE p.author = :authorId');
                $deleteComments->setParameter('authorId', $id);
                $deletePosts->execute();
                $em->remove($user);
                $em->flush();

                $this->addFlash('success', 'The user '.$id.' has been deleted successfully!');
            } else {
                $this->addFlash('failure', 'You has to be admin to delete the user!');
            }
        } else {
            $this->addFlash('error', 'Invalid CSRF token');
        }

        return $this->redirectToRoute('admin_userlist');
    }


    #[Route('/admin/newcategory', name: 'newcategory')]
    public function newcategory(EntityManagerInterface $em, Request $request): Response
    {      

        $categorys = $em->getRepository(Category::class)->findBy(array(),array('id'=>'ASC'));


        $category = new Category();
        $form = $this->createFormBuilder()
        ->add('name', TextType::class,[
            'label' => 'Category name',
            'attr' => array('class' => 'form-control')
        ])
        ->add('save', SubmitType::class, [
            'attr' => array('class' => 'btn btn-outline-danger btn-sm')
        ])
        ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->isGranted('ROLE_ADMIN'))
        {
        $eingabe = $form->getData();

        $category->setName($eingabe['name']);

        $em->persist($category);
        $em->flush();

        $this->addFlash('success', 'New Category has been created successfully!');
        return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('admin/newcategory.html.twig', [
            'newPostForm' => $form->createView(),
            'categorys' => $categorys,
        ]);
    }

    #[Route('/admin/category/edit/{id}', name:'admin_categoryedit')]
    public function editCategory(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(Category::class)->find($id);        

        // CSRF Token validation
        $token = $request->query->get('token');
        if($this->isGranted('ROLE_ADMIN') )
        {
            $form = $this->createFormBuilder()
                ->add('name', TextType::class,[
            'label' => 'Category Name',
            'attr' => array('class' => 'form-control','value' => $category->getName())
                ])
                ->add('save', SubmitType::class, [
            'attr' => array('class' => 'btn btn-outline-danger btn-sm')
            ])
            ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid())
            {
                $eingabe = $form->getData();

                $category->setName($form->get('name')->getData());


                $em->persist($category);
                $em->flush();

                $this->addFlash('success', 'category has been edited successfully!');
                return $this->redirect($this->generateUrl('newcategory'));
                
            }
            return $this->render('admin/edit_category.html.twig',[
                'editForm' => $form->createView(),
                'category' => $category
            ]);
        } else {
            return $this->redirect($this->generateUrl('newcategory'));
        }

        return $this->redirectToRoute('newcategory');
    }


}