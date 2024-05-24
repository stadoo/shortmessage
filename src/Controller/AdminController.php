<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            $form = $this->createFormBuilder()
        ->add('email', EmailType::class,[
            'label' => 'Email',
            'attr' => array('class' => 'form-control','value' => $user->getEmail())
        ])
        ->add('password', PasswordType::class, [
            'attr' => array('class' => 'form-control')
        ])
        ->add('submit', SubmitType::class, [
            'attr' => array('class' => 'btn btn-outline-danger btn-sm')
        ])
        ->getForm();
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid())
            {
                $eingabe = $form->getData();

                $user->setEmail($form->get('email')->getData());
                $hashedPassword = $this->userPasswordHasher->hashPassword($user, $form->get('password')->getData());
                $user->setPassword($hashedPassword);

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'The User has been edited successfully!');
                return $this->redirect($this->generateUrl('home'));
                
            }
            return $this->render('admin/edit_user.html.twig',[
                'editForm' => $form->createView(),
                'post' => $user
            ]);
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }
}