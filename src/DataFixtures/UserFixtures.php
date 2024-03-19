<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private $parameters;


    public function __construct(UserPasswordHasherInterface $userPasswordHasher, ParameterBagInterface $parameters)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->parameters = $parameters;
    }

    public function load(ObjectManager $manager): void
    {
        //to activate check parameter at services.yaml
        if ($this->parameters->get('app.fixtures.user_enabled') === false) {
            return;
        }

        $user = new User();
        $user->setEmail('test3@test.de');
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, 'testtest');
        $user->setPassword($hashedPassword);
        $manager->persist($user);
        $manager->flush();
    }
}