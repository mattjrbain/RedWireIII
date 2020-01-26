<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\Rubrique;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Annonce1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('entete')
            ->add('corps')
//            ->add('createdAt')
//            ->add('expiredAt')
            ->add('rubrique', EntityType::class, [
                'class'        => Rubrique::class,
                'choice_label' => 'libelle'
            ])
//            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
