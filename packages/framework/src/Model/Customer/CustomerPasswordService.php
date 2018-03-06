<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use DateTime;
use Shopsys\FrameworkBundle\Component\String\HashGenerator;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class CustomerPasswordService
{
    const RESET_PASSWORD_HASH_LENGTH = 50;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
     */
    private $encoderFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\String\HashGenerator
     */
    private $hashGenerator;

    public function __construct(
        EncoderFactory $encoderFactory,
        HashGenerator $hashGenerator
    ) {
        $this->encoderFactory = $encoderFactory;
        $this->hashGenerator = $hashGenerator;
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
     */
    public function resetPassword(User $user)
    {
        $hash = $this->hashGenerator->generateHash(self::RESET_PASSWORD_HASH_LENGTH);
        $user->setResetPasswordHash($hash);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param string|null $hash
     * @return bool
     */
    public function isResetPasswordHashValid(User $user, $hash)
    {
        if ($hash === null || $user->getResetPasswordHash() !== $hash) {
            return false;
        }

        $now = new DateTime();
        if ($user->getResetPasswordHashValidThrough() === null || $user->getResetPasswordHashValidThrough() < $now) {
            return false;
        }

        return true;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param string|null $hash
     * @param string $newPassword
     */
    public function setNewPassword(User $user, $hash, $newPassword)
    {
        if (!$this->isResetPasswordHashValid($user, $hash)) {
            throw new \Shopsys\FrameworkBundle\Model\Customer\Exception\InvalidResetPasswordHashException();
        }

        $this->changePassword($user, $newPassword);
    }
}
