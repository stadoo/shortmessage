<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class CategoryFixtures extends Fixture
{

    private $parameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    public function load(ObjectManager $manager): void
    {
        //to activate check parameter at services.yaml
        if ($this->parameters->get('app.fixtures.category_enabled') === false) {
            return;
        }

        $categorys = ['Home', 'Technique', 'Fun'];

        foreach($categorys as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
        }

        $manager->flush();

    }
}