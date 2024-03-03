<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $post = $options['data'];
        $builder
            ->add('name', TextType::class,[
            'label' => 'Titel',
            'attr' => array('class' => 'form-control')
            ])
            ->add('text', TextareaType::class, [
            'label' => 'Message',
            'attr' => array('class' => 'form-control')
            ])
            ->add('category',EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name',
            'data' => $post->getCategory(),
            'attr' => array('class' => 'form-control')
            ])
            ->add('submit', SubmitType::class, [
            'attr' => array('class' => 'btn btn-sm')
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}