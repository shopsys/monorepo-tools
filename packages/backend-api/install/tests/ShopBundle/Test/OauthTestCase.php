<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Test;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Shopsys\FrameworkBundle\Component\Router\CurrentDomainRouter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OauthTestCase extends FunctionalTestCase
{
    private const OAUTH_IDENTIFIER = 'test';
    private const OAUTH_SECRET = 'xxx';

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    protected function setUp()
    {
        parent::setUp();
        $this->setUpDomain();
        $this->connection = $this->getContainer()->get('doctrine.orm.entity_manager')->getConnection();
        $this->createOauthClientInDatabase();
    }

    private function createOauthClientInDatabase(): void
    {
        try {
            $statement = $this->connection->prepare('INSERT INTO "oauth2_client" ("identifier", "secret", "grants", "active") VALUES (:identifier, :secret, \'client_credentials password\', \'1\')');
            $statement->bindValue(':identifier', self::OAUTH_IDENTIFIER);
            $statement->bindValue(':secret', self::OAUTH_SECRET);
            $statement->execute();
        } catch (UniqueConstraintViolationException $e) {
            // ok, client is there probably from last unsuccessful run
        }
    }

    protected function tearDown()
    {
        $this->removeOauthClientFromDatabase();
    }

    private function removeOauthClientFromDatabase(): void
    {
        $statement = $this->connection->prepare('DELETE FROM "oauth2_access_token" WHERE "client" = :identifier');
        $statement->bindValue(':identifier', self::OAUTH_IDENTIFIER);
        $statement->execute();

        $statement = $this->connection->prepare('DELETE FROM "oauth2_client" WHERE "identifier" = :identifier');
        $statement->bindValue(':identifier', self::OAUTH_IDENTIFIER);
        $statement->execute();
    }

    /**
     * @return string
     */
    protected function createOauthToken(): string
    {
        /** @var \Shopsys\FrameworkBundle\Component\Router\CurrentDomainRouter $router */
        $router = $this->getContainer()->get(CurrentDomainRouter::class);
        $tokenUrl = $router->generate('oauth2_token', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $oauthParameters = [
            'grant_type' => 'client_credentials',
            'client_id' => self::OAUTH_IDENTIFIER,
            'client_secret' => self::OAUTH_SECRET,
        ];

        $client = $this->getClient();
        $client->request('POST', $tokenUrl, $oauthParameters);

        $response = $client->getResponse();
        $jsonResponse = json_decode($response->getContent(), true);
        return $jsonResponse['access_token'];
    }

    /**
     * @param string $method
     * @param string $uri
     * @param mixed|null $content
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function runOauthRequest(string $method, string $uri, $content = null): Response
    {
        $client = $this->getClient();
        $headers = [
            'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $this->createOauthToken()),
            'HTTP_ACCEPT' => '*/*',
        ];
        $client->request($method, $uri, [], [], $headers, $content);
        return $client->getResponse();
    }
}
