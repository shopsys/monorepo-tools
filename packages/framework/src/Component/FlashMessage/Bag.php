<?php

namespace Shopsys\FrameworkBundle\Component\FlashMessage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Bag
{
    /** @access protected */
    const MAIN_KEY = 'messages';

    /** @access protected */
    const KEY_ERROR = 'error';
    /** @access protected */
    const KEY_INFO = 'info';
    /** @access protected */
    const KEY_SUCCESS = 'success';

    /**
     * @var string
     */
    protected $bagName;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * @param string $bagName
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    public function __construct($bagName, SessionInterface $session)
    {
        if (!is_string($bagName) || empty($bagName)) {
            $message = 'Bag name for messages must be non-empty string.';
            throw new \Shopsys\FrameworkBundle\Component\FlashMessage\Exception\BagNameIsNotValidException($message);
        }

        $this->session = $session;
        $this->bagName = $bagName;
    }

    /**
     * @param string|array $message
     * @param bool $escape
     */
    public function addError($message, $escape = true)
    {
        $this->addMessage($message, $escape, static::KEY_ERROR);
    }

    /**
     * @param string|array $message
     * @param bool $escape
     */
    public function addInfo($message, $escape = true)
    {
        $this->addMessage($message, $escape, static::KEY_INFO);
    }

    /**
     * @param string|array $message
     * @param bool $escape
     */
    public function addSuccess($message, $escape = true)
    {
        $this->addMessage($message, $escape, static::KEY_SUCCESS);
    }

    /**
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->getMessages(static::KEY_ERROR);
    }

    /**
     * @return array
     */
    public function getInfoMessages()
    {
        return $this->getMessages(static::KEY_INFO);
    }

    /**
     * @return array
     */
    public function getSuccessMessages()
    {
        return $this->getMessages(static::KEY_SUCCESS);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
        $session = $this->session;
        $flashBag = $session->getFlashBag();

        return !$flashBag->has($this->getFullbagName(static::KEY_ERROR))
            && !$flashBag->has($this->getFullbagName(static::KEY_INFO))
            && !$flashBag->has($this->getFullbagName(static::KEY_SUCCESS));
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getFullbagName($key)
    {
        return static::MAIN_KEY . '__' . $this->bagName . '__' . $key;
    }

    /**
     * @param string $key
     * @return array
     */
    protected function getMessages($key)
    {
        /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
        $session = $this->session;
        $flashBag = $session->getFlashBag();
        $messages = $flashBag->get($this->getFullbagName($key));
        return array_unique($messages);
    }

    /**
     * @param string|array $message
     * @param bool $escape
     * @param string $key
     */
    protected function addMessage($message, $escape, $key)
    {
        if (is_array($message)) {
            foreach ($message as $item) {
                $this->addMessage($item, $escape, $key);
            }
        } else {
            if ($escape) {
                $message = htmlspecialchars($message);
            }

            /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
            $session = $this->session;
            $session->getFlashBag()->add($this->getFullbagName($key), $message);
        }
    }
}
