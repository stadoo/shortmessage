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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
  #[Route('/user/profile',name:'user_profile')]
  public function index(EntityManagerInterface $em ,PostRepository $postRepository, PaginatorInterface $paginator, Request $request):Response {
    $posts = $em->getRepository(Post::class)->findBy(['author' => $this->getUser()]);

    $pagination = $paginator->paginate(
        $posts, /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
        10 /*limit per page*/);
        $pagination->setTemplate('home/my_pagination.html.twig');
        
    return $this->render('profile/userprofile.html.twig', [
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


    return $this->render('profile/userview.html.twig', [
      'user'=>$user,
      'posts'=>$posts,
      'pagination' => $pagination,

  ]);
  }

    #[Route('/user/profile/edit',name:'user_edit')]
    public function editProfile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (is_null($user->getSocialLinks())) {
            $user->setSocialLinks([]);
        }

        $formSocialLink = $this->createFormBuilder()
            ->add('newSocialLink', UrlType::class, [
                'label' => 'New Social Link',
                'attr' => ['placeholder' => 'https://']
            ])
            ->add('save', SubmitType::class, ['label' => 'Add'])
            ->getForm();

        $formSocialLink->handleRequest($request);

        $fromPassword = $this->createFormBuilder()
        ->add('password', PasswordType::class, [
            'attr' => array('class' => 'form-control')
        ])
        ->add('save', SubmitType::class, [
            'attr' => array('class' => 'btn btn-outline-danger btn-sm')
        ])
        ->getForm();

        $fromPassword->handleRequest($request);

        if ($formSocialLink->isSubmitted() && $formSocialLink->isValid()) {
            $newLink = $formSocialLink->get('newSocialLink')->getData();

            if (count($user->getSocialLinks()) < 3 && preg_match('/(twitter\.com|x\.com|facebook\.com|linkedin\.com)/', $newLink)) {
                $user->addSocialLink($newLink);

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Link added successfully!');
            } else {
                $this->addFlash('failure', 'You cannot add more than 3 social links or the link is invalid.');
            }

            return $this->redirectToRoute('user_edit');
        } elseif($fromPassword->isSubmitted() && $fromPassword->isValid()) {

            $hashedPassword = $this->userPasswordHasher->hashPassword($user, $fromPassword->get('password')->getData());
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'The User Password has been edited successfully!');
            return $this->redirect($this->generateUrl('home'));
            
        }

        return $this->render('profile/useredit.html.twig', [
            'formSocialLink' => $formSocialLink->createView(),
            'formEditPassword' => $fromPassword->createView(),
            'user' => $user
        ]);
    }


    #[Route('/profile/remove-link/{index}', name:'remove_social_link')]
    public function removeSocialLink(int $index, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $links = $user->getSocialLinks();

        if (isset($links[$index])) {
            unset($links[$index]);
            $user->setSocialLinks(array_values($links)); // Reindex array

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Link removed successfully!');
        }

        return $this->redirectToRoute('user_edit');
    }
}