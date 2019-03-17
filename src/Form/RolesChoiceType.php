<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RolesChoiceType extends AbstractType
{

    /**
     * @var array
     */
    protected $roles;

    /**
     * RolesChoiceType constructor.
     * @param array $roles
     */
    public function __construct(array $roles)
    {
        $this->roles = array_keys($roles);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles[$role] = $role;
        }

        $resolver->setDefaults([
            'choices' => $roles,
            'multiple' => true
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}