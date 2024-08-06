<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class PasswordResetController extends AbstractController
{
    private $parameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }
  #[Route('/password-reset-request', name:'password_reset_request')]
  public function requestpassword(Request $request, UserRepository $userRepository, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $em)
  {
    $form = $this->createFormBuilder()
        ->add('email', EmailType::class,[
            'label' => 'Email',
            'attr' => array('class' => 'form-control')
        ])
        ->add('request', SubmitType::class, [
            'attr' => array('class' => 'btn btn-outline-danger btn-sm')
        ])
        ->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $email = $form->get('email')->getData();
        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user) {
            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);
            $user->setTokenExpirationDate(new \DateTime('+1 hour'));
            $em->persist($user);
            $em->flush();

            $resetUrl = $this->generateUrl('password_reset_token', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            $email = (new Email())
                ->from($this->parameters->get('app.passwortreset_mail'))
                ->to($user->getEmail())
                ->subject('Your password reset request')
                ->html('<p>To reset your password, please visit <a href="'.$resetUrl.'">'.$resetUrl.'</a></p>');

            $mailer->send($email);
        }

        $this->addFlash('success', 'If an account exists, a password reset email has been send!');
        return $this->redirectToRoute('home');
    }

    return $this->render('security/passwordresetrequest.html.twig', [
        'requestForm' => $form->createView(),
    ]);
  }
  
  #[Route('/reset-password/{token}', name:'password_reset_token')]
  public function resetpassword(Request $request, string $token, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em)
  {
      $user = $userRepository->findOneByResetToken($token);

        if (!$user || $user->isTokenExpired()) {
          $this->addFlash('failure', 'Token not found or expired.');
          return $this->redirectToRoute('home');
        }

        $form = $this->createFormBuilder()
        ->add('plainPassword', PasswordType::class,[
            'label' => 'New Password',
            'attr' => array('class' => 'form-control')
        ])
        ->add('request', SubmitType::class, [
            'label' => 'Reset Password',
            'attr' => array('class' => 'btn btn-outline-danger btn-sm')
        ])
        ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            ));
            $user->setResetToken(null);
            $user->setTokenExpirationDate(null);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('security/passwordreset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}