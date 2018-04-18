<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Customer\Mail\ResetPasswordMailFacade;

class CustomerPasswordFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserRepository
     */
    private $userRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\Mail\ResetPasswordMailFacade
     */
    private $resetPasswordMailFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerPasswordService
     */
    private $customerPasswordService;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        CustomerPasswordService $customerPasswordService,
        ResetPasswordMailFacade $resetPasswordMailFacade
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->customerPasswordService = $customerPasswordService;
        $this->resetPasswordMailFacade = $resetPasswordMailFacade;
    }

    /**
     * @param string $email
     * @param int $domainId
     */
    public function resetPassword($email, $domainId)
    {
        $user = $this->userRepository->getUserByEmailAndDomain($email, $domainId);

        $this->customerPasswordService->resetPassword($user);
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

        return $this->customerPasswordService->isResetPasswordHashValid($user, $hash);
    }

    /**
     * @param string $email
     * @param int $domainId
     * @param string|null $hash
     * @param string $newPassword
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function setNewPassword($email, $domainId, $hash, $newPassword)
    {
        $user = $this->userRepository->getUserByEmailAndDomain($email, $domainId);

        $this->customerPasswordService->setNewPassword($user, $hash, $newPassword);

        return $user;
    }
}
