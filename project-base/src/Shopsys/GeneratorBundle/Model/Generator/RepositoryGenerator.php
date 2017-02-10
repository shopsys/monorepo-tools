<?php

namespace Shopsys\GeneratorBundle\Model\Generator;

use Shopsys\GeneratorBundle\Model\AbstractGenerator;
use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Validator\Constraints;

class RepositoryGenerator extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder) {
        $builder
            ->add('entityNamespace', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please insert entity namespace']),
                ],
            ])
            ->add('entityName', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please insert entity name']),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function generate(BundleInterface $bundle, array $formData) {
        $entityNamespace = $formData['entityNamespace'];
        $entityName = $formData['entityName'];
        $targetFilepath = $bundle->getPath() . '/Model/' . $entityNamespace . '/' . $entityName . 'Repository.php';
        $this->renderFile('Repository.php.twig', $targetFilepath, [
            'entityName' => $entityName,
            'namespace' => $bundle->getNamespace() . '\Model\\' . $entityNamespace,
        ]);

        return $targetFilepath;
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'repository';
    }
}
