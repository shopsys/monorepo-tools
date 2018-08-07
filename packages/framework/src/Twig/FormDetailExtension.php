<?php

namespace Shopsys\FrameworkBundle\Twig;

use Symfony\Component\Form\FormView;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class FormDetailExtension extends Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $twigEnvironment;

    public function __construct(Twig_Environment $twigEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('form_id', [$this, 'formId'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('form_save', [$this, 'formSave'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param mixed $object
     * @return string
     */
    public function formId($object)
    {
        if ($object === null) {
            return '';
        } else {
            return '<div class="form-line">
                        <label class="form-line__label">ID:</label>
                        <div class="form-line__side">
                            <div class="form-line__item">
                                <input
                                    type="text"
                                    value="' . htmlspecialchars($object->getId(), ENT_QUOTES) . '"
                                    class="input"
                                    readonly="readonly"
                                >
                            </div>
                        </div>
                    </div>';
        }
    }

    /**
     * @param mixed $object
     * @param \Symfony\Component\Form\FormView $formView
     * @param array $vars
     * @return string
     */
    public function formSave($object, FormView $formView, array $vars = [])
    {
        $template = $this->twigEnvironment->createTemplate('{{ form_widget(form.save, vars) }}');

        if (!array_keys($vars, 'label', true)) {
            if ($object === null) {
                $vars['label'] = t('Create');
            } else {
                $vars['label'] = t('Save changes');
            }
        }

        $parameters['form'] = $formView;
        $parameters['vars'] = $vars;

        return $template->render($parameters);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'shopsys.twig.form_detail_extension';
    }
}
