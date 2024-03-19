<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Post;
use App\Entity\Category;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PostFixtures extends Fixture
{   
    private $parameters;
        
    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    public function load(ObjectManager $manager)
    {
        //to activate check parameter at services.yaml
        if ($this->parameters->get('app.fixtures.post_enabled') === false) {
            return;
        }

        $faker = Factory::create();

        $authorIds = [1, 2];

        for ($i = 1; $i <= 20; $i++) {
            $newPost = new Post();
            $newPost->setAuthorid($authorIds[array_rand($authorIds)]);
            $newPost->setName($faker->sentence());
            $newPost->setText($faker->paragraph());
            $newPost->setStatus(1);
            $newPost->setDate($faker->dateTimeThisMonth()->format('Y-m-d H:i:s'));
            $newPost->setLikeCount(0);
            $newPost->setDislikeCount(0);



            // Zufällige Kategorie aus der Datenbank abrufen und setzen
            $categoryId = $faker->randomElement([1, 2, 5]);
            $category = $manager->getRepository(Category::class)->find($categoryId);
            if ($category) {
                $newPost->setCategory($category);
            } else {
                $newPost->setCategory(1);
            }

            $manager->persist($newPost);
        }

        $manager->flush();
    }
}