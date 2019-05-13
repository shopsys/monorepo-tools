<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Tests\ShopBundle\Test\FunctionalTestCase;

class PositionExtensionTest extends FunctionalTestCase
{
    public function testFormFieldsPosition(): void
    {
        $form = $this->getForm();
        $this->assertPositions($form->createView(), ['a', 'b', 'c', 'd', 'e', 'f', 'g']);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    private function getForm(): FormInterface
    {
        /** @var \Symfony\Component\Form\FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get('form.factory');

        $builder = $formFactory->createBuilder();

        $builder
            ->add('g', TextType::class, ['position' => 'last'])
            ->add('a', TextType::class, ['position' => 'first'])
            ->add('c', TextType::class)
            ->add('f', TextType::class)
            ->add('e', TextType::class, ['position' => ['before' => 'f']])
            ->add('d', TextType::class, ['position' => ['after' => 'c']])
            ->add('b', TextType::class, ['position' => 'first']);

        return $builder->getForm();
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param string[] $expected
     */
    private function assertPositions(FormView $view, array $expected): void
    {
        $children = array_values($view->children);

        foreach ($expected as $index => $fieldName) {
            $this->assertArrayHasKey($index, $children);
            $this->assertArrayHasKey($fieldName, $view->children);
            $this->assertSame($children[$index], $view->children[$fieldName]);
        }
    }
}
