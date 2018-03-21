<?php

namespace Shopsys\FrameworkBundle\Component\FlashMessage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Bag
{
    const MAIN_KEY = 'messages';

    const KEY_ERROR = 'error';
    const KEY_INFO = 'info';
    const KEY_SUCCESS = 'success';

    /**
     * @var string
     */
    protected $bagName;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    private $session;

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
        $this->addMessage($message, $escape, self::KEY_ERROR);
    }

    /**
     * @param string|array $message
     * @param bool $escape
     */
    public function addInfo($message, $escape = true)
    {
        $this->addMessage($message, $escape, self::KEY_INFO);
    }

    /**
     * @param string|array $message
     * @param bool $escape
     */
    public function addSuccess($message, $escape = true)
    {
        $this->addMessage($message, $escape, self::KEY_SUCCESS);
    }

    /**
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->getMessages(self::KEY_ERROR);
    }

    /**
     * @return array
     */
    public function getInfoMessages()
    {
        return $this->getMessages(self::KEY_INFO);
    }

    /**
     * @return array
     */
    public function getSuccessMessages()
    {
        return $this->getMessages(self::KEY_SUCCESS);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        $flashBag = $this->session->getFlashBag();

        return !$flashBag->has($this->getFullbagName(self::KEY_ERROR))
            && !$flashBag->has($this->getFullbagName(self::KEY_INFO))
            && !$flashBag->has($this->getFullbagName(self::KEY_SUCCESS));
    }

    /**
     * @param string $key
     * @return string
     */
    private function getFullbagName($key)
    {
        return self::MAIN_KEY . '__' . $this->bagName . '__' . $key;
    }

    /**
     * @param string $key
     * @return array
     */
    private function getMessages($key)
    {
        $flashBag = $this->session->getFlashBag();
        $messages = $flashBag->get($this->getFullbagName($key));
        return array_unique($messages);
    }

    /**
     * @param string|array $message
     * @param bool $escape
     * @param string $key
     */
    private function addMessage($message, $escape, $key)
    {
        if (is_array($message)) {
            foreach ($message as $item) {
                $this->addMessage($item, $escape, $key);
            }
        } else {
            if ($escape) {
                $message = htmlspecialchars($message);
            }

            $this->session->getFlashBag()->add($this->getFullbagName($key), $message);
        }
    }
}
