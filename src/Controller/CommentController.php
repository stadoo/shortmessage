<?php 
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{

  #[Route('/view/{id}/comment',name:'add_comment', methods:'POST')]
  public function addComment(Request $request, Post $post, EntityManagerInterface $em): Response
  {
    $comment = new Comment();
    $form = $this->createFormBuilder()
      ->add('content', TextareaType::class, [
                      'label' => 'Kommentar',
                  ])
                  ->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $comment->setPost($post);
      $comment->setCreatedAt(new \DateTime());
      $em->persist($comment);
      $em->flush();

      return $this->redirectToRoute('view', ['id' => $post->getId()]);
    }

    return $this->render('comment/new.html.twig', [
      'post' => $post,
      'form' => $form->createView(),
    ]);
  }
}