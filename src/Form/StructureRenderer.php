<?php
declare(strict_types = 1);

namespace App\Form;


use Symfony\Component\Form\FormBuilderInterface;

class StructureRenderer
{
    /** @var FormBuilderInterface  */
    private $form;

    public function __construct(FormBuilderInterface $form)
    {
        $this->form = $form;
    }

    public function render() : array
    {
        $structure = [];

        foreach ($this->form->all() as $field) {

            $structure[] = $this->recursiveRender($field);
        }

        return $structure;
    }

    private function recursiveRender(FormBuilderInterface $field) : array
    {
        $type = $field->getType();

        if ($field->getOption('use_parent_type')) {
            $type = $type->getParent();
        }

        $info = [
            'name' => $field->getName(),
            'type' => $type->getBlockPrefix(),
            'label' => $field->getOption('label'),
            'required' => $field->getRequired(),
        ];

        if ($fields = $field->all()) {
            $info['fields'] = array_map(function ($subfield) {
                return $this->recursiveRender($subfield);
            }, array_values($fields));
        }

        return $info;
    }
}