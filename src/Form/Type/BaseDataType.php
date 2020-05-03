<?php
declare(strict_types = 1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

class BaseDataType extends AbstractType
{
	 public function buildForm(FormBuilderInterface $builder, array $options)
     {
         $builder
             ->add('participant__id', IntegerType::class, ['label' => 'Id'])
             ->add('participant__name', TextType::class, ['label' => 'Name'])
             ->add('participant__email', EmailType::class, ['label' => 'Email'])
             ->add('participant__city', TextType::class, ['label' => 'City'])
             ->add('participant__postalCode', TextType::class, ['label' => 'Postal code'])
             ->add('participant__birthdate', BirthdayType::class, ['label' => 'Birthdate']);
     }

     public function getParent() : string
     {
         return GroupType::class;
     }
}
