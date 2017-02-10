<?php

namespace Shopsys\ShopBundle\Model\Customer;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Customer\CustomerPasswordService;
use Shopsys\ShopBundle\Model\Customer\Mail\ResetPasswordMailFacade;
use Shopsys\ShopBundle\Model\Customer\UserRepository;

class CustomerPasswordFacade {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\UserRepository
     */
    private $userRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\Mail\ResetPasswordMailFacade
     */
    private $resetPasswordMailFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CustomerPasswordService
     */
    private $customerPasswordService;

    public function __construct(
        EntityManager $em,
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
    public function resetPassword($email, $domainId) {
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
    public function isResetPasswordHashValid($email, $domainId, $hash) {
        $user = $this->userRepository->getUserByEmailAndDomain($email, $domainId);

        return $this->customerPasswordService->isResetPasswordHashValid($user, $hash);
    }

    /**
     * @param string $email
     * @param int $domainId
     * @param string|null $hash
     * @param string $newPassword
     * @return \Shopsys\ShopBundle\Model\Customer\User
     */
    public function setNewPassword($email, $domainId, $hash, $newPassword) {
        $user = $this->userRepository->getUserByEmailAndDomain($email, $domainId);

        $this->customerPasswordService->setNewPassword($user, $hash, $newPassword);

        return $user;
    }

}
