<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Test\Codeception;

use PHPUnit\Framework\Assert;

/**
 * Representation of a graphical checkbox that is used in administration
 * Allows to manipulate checkboxes and read their state
 * (The original input is hidden by JS and replaced by a graphical element, therefore it cannot be manipulated directly)
 */
class AdminCheckbox
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
     * @param \Tests\FrameworkBundle\Test\Codeception\ActorInterface $tester
     * @param string $cssSelector
     * @return \Tests\FrameworkBundle\Test\Codeception\AdminCheckbox
     */
    public static function createByCss(ActorInterface $tester, string $cssSelector): self
    {
        return new static($tester, $cssSelector);
    }

    public function toggle(): void
    {
        $imageElementClass = $this->getImageElementClass();

        $this->tester->clickByCss('.' . $imageElementClass);
    }

    public function check(): void
    {
        $this->isChecked() ? $this->assertVisible() : $this->toggle();
    }

    public function uncheck(): void
    {
        $this->isChecked() ? $this->toggle() : $this->assertVisible();
    }

    public function assertChecked(): void
    {
        $this->assertVisible();

        $message = sprintf('Admin checkbox "%s" should be checked but it\'s unchecked.', $this->cssSelector);
        Assert::assertTrue($this->isChecked(), $message);
    }

    public function assertUnchecked(): void
    {
        $this->assertVisible();

        $message = sprintf('Admin checkbox "%s" should be unchecked but it\'s checked.', $this->cssSelector);
        Assert::assertFalse($this->isChecked(), $message);
    }

    /**
     * Method will mark the particular image element with a generated class via JS so it can be targeted by Selenium easily.
     *
     * @return string
     */
    protected function getImageElementClass(): string
    {
        $imageElementClass = 'js-checkbox-image-' . rand();

        $script = sprintf('$("%s").next(".css-checkbox__image").addClass("%s")', $this->cssSelector, $imageElementClass);
        $this->tester->executeJS($script);

        return $imageElementClass;
    }

    protected function assertVisible(): void
    {
        $imageElementClass = $this->getImageElementClass();

        $this->tester->canSeeElement(['css' => '.' . $imageElementClass]);
    }

    /**
     * @return bool
     */
    protected function isChecked(): bool
    {
        $script = sprintf('return $("%s").is(":checked")', $this->cssSelector);

        return (bool)$this->tester->executeJS($script);
    }
}
