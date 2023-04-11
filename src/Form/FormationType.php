<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('publishedAt', DateType::class,[
                 'label'  => 'Parution',                  
                 'format' => 'dd MMM yyyy',
                 'widget' => 'choice',
                 
                 'data' => isset($option['data'])&& $option['data']->getPublishedAt()!= null ?
                     $option['data']->getPublishedAt(): new DateTime('now')
               
            ])
           
            ->add('title', TextType::class,[
                'required' => true
            ])
            ->add('description', TextType::class,[
                'required' => false
            ])
            ->add('videoId')
            ->add('playlist', EntityType::class,[
                'class' => Playlist::class,
                'choice_label' => 'name',
                'multiple' => false,
                'required' => true
            ])
            ->add('categories', EntityType::class,[
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
