<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Test\Codeception;

use PHPUnit\Framework\Assert;

/**
 * Representation of a graphical radiobutton that is used in administration
 * Allows to manipulate radiobuttons and read their state
 * (The original input is hidden by JS and replaced by a graphical element, therefore it cannot be manipulated directly)
 */
class AdminRadiobutton
{
    /**
     * @var \Tests\FrameworkBundle\Test\Codeception\ActorInterface
     */
    protected $tester;

    /**
     * @var string
     */
    protected $cssSelector;

    /**
     * @param \Tests\FrameworkBundle\Test\Codeception\ActorInterface $tester
     * @param string $cssSelector
     */
    protected function __construct(ActorInterface $tester, string $cssSelector)
    {
        $this->tester = $tester;
        $this->cssSelector = $cssSelector;
    }

    /**
     * It is important to understand that AdminRadiobutton represents the whole group of radio inputs with the same name
     * You need to use the name or CSS class of the group (and not an ID of a particular radio input) as the $cssSelector
     * Example: AdminRadiobutton::createByCss($actor, '[name="product_form[displayAvailabilityGroup][hidden]"]')
     *
     * @param \Tests\FrameworkBundle\Test\Codeception\ActorInterface $tester
     * @param string $cssSelector
     * @return \Tests\FrameworkBundle\Test\Codeception\AdminRadiobutton
     */
    public static function createByCss(ActorInterface $tester, string $cssSelector): self
    {
        return new static($tester, $cssSelector);
    }

    /**
     * @param string $radioValue
     */
    public function select(string $radioValue): void
    {
        $imageElementClass = $this->getImageElementClassByValue($radioValue);

        $this->tester->clickByCss('.' . $imageElementClass);
    }

    /**
     * @param string $radioValue
     */
    public function assertSelected(string $radioValue): void
    {
        $imageElementClass = $this->getImageElementClassByValue($radioValue);

        $this->tester->canSeeElement(['css' => '.' . $imageElementClass]);

        $script = sprintf('return $(".%s").is("input:checked + *")', $imageElementClass);
        $checked = (bool)$this->tester->executeJS($script);

        $message = sprintf('Admin radiobutton "%s" should have value "%s" checked but it\'s unchecked.', $this->cssSelector, $radioValue);
        Assert::assertTrue($checked, $message);
    }

    /**
     * Method will mark the particular image element with a generated class via JS so it can be targeted by Selenium easily.
     *
     * @param string $radioValue
     * @return string
     */
    protected function getImageElementClassByValue(string $radioValue): string
    {
        $imageElementClass = 'js-radio-image-' . rand();

        $selector = json_encode($this->cssSelector);
        $valueSelector = json_encode(sprintf('input[value="%s"]', $radioValue));
        $script = sprintf('$(%s).filterAllNodes(%s).next(".css-radio__image").addClass("%s")', $selector, $valueSelector, $imageElementClass);
        $this->tester->executeJS($script);

        return $imageElementClass;
    }
}
