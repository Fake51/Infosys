<?php
declare(strict_types = 1);

namespace App\Form;


use Symfony\Component\Form\FormBuilderInterface;

class StructureRenderer
{
    /** @var FormBuilderInterface  */
    private $form;

    /** @var array */
    private $sizes = [
        'participant__id' => 1,
        'participant__name' => 2,
        'participant__email' => 2,
    ];

    /** @var array */
    private $readOnly = [
        'participant__id' => true,
    ];

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
            'size' => $this->sizes[$field->getName()] ?? 1,
            'readOnly' => $this->readOnly[$field->getName()] ?? false,
        ];

        if ($fields = $field->all()) {
            $info['fields'] = array_map(function ($subfield) {
                return $this->recursiveRender($subfield);
            }, array_values($fields));
        }

        return $info;
    }
}