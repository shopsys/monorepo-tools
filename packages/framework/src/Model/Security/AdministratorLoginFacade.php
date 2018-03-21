<?php

namespace Shopsys\FrameworkBundle\Model\Security;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\String\HashGenerator;
use Shopsys\FrameworkBundle\Model\Administrator\Administrator;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class AdministratorLoginFacade
{
    const MULTIDOMAIN_LOGIN_TOKEN_LENGTH = 50;
    const MULTIDOMAIN_LOGIN_TOKEN_VALID_SECONDS = 10;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorRepository
     */
    private $administratorRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\String\HashGenerator
     */
    private $hashGenerator;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
        AdministratorRepository $administratorRepository,
        HashGenerator $hashGenerator,
        EntityManagerInterface $em
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->administratorRepository = $administratorRepository;
        $this->hashGenerator = $hashGenerator;
        $this->em = $em;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator $administrator
     * @return string
     */
    public function generateMultidomainLoginTokenWithExpiration(Administrator $administrator)
    {
        $multidomainLoginToken = $this->hashGenerator->generateHash(self::MULTIDOMAIN_LOGIN_TOKEN_LENGTH);
        $multidomainLoginTokenExpirationDateTime = new DateTime('+' . self::MULTIDOMAIN_LOGIN_TOKEN_VALID_SECONDS . 'seconds');
        $administrator->setMultidomainLoginTokenWithExpiration($multidomainLoginToken, $multidomainLoginTokenExpirationDateTime);
        $this->em->flush();

        return $multidomainLoginToken;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $multidomainLoginToken
     */
    public function loginByMultidomainToken(Request $request, $multidomainLoginToken)
    {
        $administrator = $this->administratorRepository->getByValidMultidomainLoginToken($multidomainLoginToken);
        $administrator->setMultidomainLogin(true);
        $password = '';
        $firewallName = 'administration';
        $token = new UsernamePasswordToken($administrator, $password, $firewallName, $administrator->getRoles());
        $this->tokenStorage->setToken($token);

        $event = new InteractiveLoginEvent($request, $token);
        $this->eventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $event);
    }

    public function invalidateCurrentAdministratorLoginToken()
    {
        $token = $this->tokenStorage->getToken();
        if ($token !== null) {
            $currentAdministrator = $token->getUser();
            $currentAdministrator->setLoginToken('');

            $this->em->flush($currentAdministrator);
        }
    }
}
