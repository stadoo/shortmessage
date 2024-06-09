<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Post;
use App\Entity\Category;
use App\Entity\User;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostFixtures extends Fixture implements DependentFixtureInterface
{   
    private $parameters;

    public function __construct(ParameterBagInterface $parameters) {
        $this->parameters = $parameters;
    }

    public function load(ObjectManager $manager)
    {
        //to activate check parameter at services.yaml
        if ($this->parameters->get('app.fixtures.post_enabled') === false) {
            return;
        }

        $faker = Factory::create();

        for ($i = 1; $i <= 100; $i++) {
            $newPost = new Post();
            $randomUserId = rand(1, 10);
            $author = $manager->getRepository(User::class)->find($randomUserId);

            if ($author) {
                $newPost->setAuthor($author);
            } else {
                $defaultAuthor = $manager->getRepository(User::class)->find(1);
                $newPost->setAuthor($defaultAuthor);
            }
            $newPost->setName($faker->sentence());
            $newPost->setText($faker->paragraph());
            $newPost->setStatus(1);
            $newPost->setDate($faker->dateTimeBetween('-30 days', 'now'));
            $newPost->setLikeCount(0);
            $newPost->setDislikeCount(0);
            $categoryId = $faker->randomElement([1, 2, 3]);
            $category = $manager->getRepository(Category::class)->find($categoryId);
            if ($category) {
                $newPost->setCategory($category);
            } else {
                $defaultCategory = $manager->getRepository(Category::class)->find(1);
                $newPost->setCategory($defaultCategory);
            }

            $manager->persist($newPost);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
