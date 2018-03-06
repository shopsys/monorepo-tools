<?php

namespace Shopsys\FrameworkBundle\Component\Form;

use DateTime;
use Shopsys\FrameworkBundle\Form\TimedFormTypeExtension;
use Symfony\Component\HttpFoundation\Session\Session;

class FormTimeProvider
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param string $name
     * @return \DateTime
     */
    public function generateFormTime($name)
    {
        $startTime = new DateTime();
        $key = $this->getSessionKey($name);
        $this->session->set($key, $startTime);
        return $startTime;
    }

    /**
     * @param string $name
     * @param array $options
     * @return bool
     */
    public function isFormTimeValid($name, array $options)
    {
        $startTime = $this->findFormTime($name);

        if ($startTime === null) {
            return false;
        }

        if ($options[TimedFormTypeExtension::OPTION_MINIMUM_SECONDS] !== null) {
            return new DateTime('-' . $options[TimedFormTypeExtension::OPTION_MINIMUM_SECONDS] . ' second') >= $startTime;
        }

        return true;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasFormTime($name)
    {
        $key = $this->getSessionKey($name);
        return $this->session->has($key);
    }

    /**
     * @param string $name
     * @return \DateTime|null
     */
    public function findFormTime($name)
    {
        $key = $this->getSessionKey($name);
        if ($this->hasFormTime($name)) {
            return $this->session->get($key);
        }
        return null;
    }

    /**
     * @param string $name
     */
    public function removeFormTime($name)
    {
        $key = $this->getSessionKey($name);
        $this->session->remove($key);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getSessionKey($name)
    {
        return 'timedSpam-' . $name;
    }
}
