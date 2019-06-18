<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Customer;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\String\HashGenerator;
use Shopsys\FrameworkBundle\Model\Customer\Mail\ResetPasswordMailFacade;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class CustomerPasswordFacade
{
    public const RESET_PASSWORD_HASH_LENGTH = 50;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserRepository
     */
    protected $userRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\Mail\ResetPasswordMailFacade
     */
    protected $resetPasswordMailFacade;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\String\HashGenerator
     */
    protected $hashGenerator;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserRepository $userRepository
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\Mail\ResetPasswordMailFacade $resetPasswordMailFacade
     * @param \Shopsys\FrameworkBundle\Component\String\HashGenerator $hashGenerator
     */
    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        EncoderFactoryInterface $encoderFactory,
        ResetPasswordMailFacade $resetPasswordMailFacade,
        HashGenerator $hashGenerator
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->encoderFactory = $encoderFactory;
        $this->resetPasswordMailFacade = $resetPasswordMailFacade;
        $this->hashGenerator = $hashGenerator;
    }

    /**
     * @param string $email
     * @param int $domainId
     */
    public function resetPassword($email, $domainId)
    {
        $user = $this->userRepository->getUserByEmailAndDomain($email, $domainId);

        $resetPasswordHash = $this->hashGenerator->generateHash(static::RESET_PASSWORD_HASH_LENGTH);
        $user->setResetPasswordHash($resetPasswordHash);

        $this->em->flush($user);
        $this->resetPasswordMailFacade->sendMail($user);
    }

    /**
     * @param string $email
     * @param int $domainId
     * @param string|null $hash
     * @return bool
     */
    public function isResetPasswordHashValid($email, $domainId, $hash)
    {
        $user = $this->userRepository->getUserByEmailAndDomain($email, $domainId);

        return $user->isResetPasswordHashValid($hash);
    }

    /**
     * @param string $email
     * @param int $domainId
     * @param string|null $resetPasswordHash
     * @param string $newPassword
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function setNewPassword(string $email, int $domainId, ?string $resetPasswordHash, string $newPassword): User
    {
        $user = $this->userRepository->getUserByEmailAndDomain($email, $domainId);

        if (!$user->isResetPasswordHashValid($resetPasswordHash)) {
            throw new \Shopsys\FrameworkBundle\Model\Customer\Exception\InvalidResetPasswordHashException();
        }

        $this->changePassword($user, $newPassword);

        return $user;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param string $password
     */
    public function changePassword(User $user, string $password): void
    {
        $encoder = $this->encoderFactory->getEncoder($user);
        $passwordHash = $encoder->encodePassword($password, null);
        $user->setPasswordHash($passwordHash);
    }
}
