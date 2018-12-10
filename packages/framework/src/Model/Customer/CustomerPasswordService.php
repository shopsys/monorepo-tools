<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class CustomerPasswordService
{
    const RESET_PASSWORD_HASH_LENGTH = 50;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param string $password
     */
    public function changePassword(User $user, $password)
    {
        $encoder = $this->encoderFactory->getEncoder($user);
        $passwordHash = $encoder->encodePassword($password, $user->getSalt());
        $user->changePassword($passwordHash);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param string|null $hash
     * @param string $newPassword
     */
    public function setNewPassword(User $user, $hash, $newPassword)
    {
        if (!$user->isResetPasswordHashValid($hash)) {
            throw new \Shopsys\FrameworkBundle\Model\Customer\Exception\InvalidResetPasswordHashException();
        }

        $this->changePassword($user, $newPassword);
    }
}
