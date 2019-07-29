<?php

declare(strict_types=1);

namespace Shopsys\BackendApiBundle\Controller\V1\Product;

use DateTime;
use Shopsys\BackendApiBundle\Component\Validation\ValidationRunner;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Symfony\Component\Validator\Constraints;

/**
 * @experimental
 */
class ProductApiDataValidator implements ProductApiDataValidatorInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\BackendApiBundle\Component\Validation\ValidationRunner
     */
    protected $validationRunner;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\BackendApiBundle\Component\Validation\ValidationRunner $validationRunner
     */
    public function __construct(Domain $domain, ValidationRunner $validationRunner)
    {
        $this->domain = $domain;
        $this->validationRunner = $validationRunner;
    }

    /**
     * @param array $productApiData
     * @return string[]
     */
    public function validateCreate(array $productApiData): array
    {
        $constraintCollection = new Constraints\Collection($this->getConstraintDefinition());

        return $this->validationRunner->runValidation($productApiData, $constraintCollection);
    }

    /**
     * @param array $productApiData
     * @return string[]
     */
    public function validateUpdate(array $productApiData): array
    {
        return $this->validateCreate($productApiData);
    }

    /**
     * @return array
     */
    protected function getConstraintDefinition(): array
    {
        $nameFields = $this->createNameConstraintDefinition();
        $descriptionFields = $this->createDescriptionConstraintDefinition();

        return [
            'fields' => [
                'uuid' => new Constraints\Optional(
                    new Constraints\Type([
                        'type' => 'string',
                        'message' => 'The value {{ value }} is not a valid {{ type }}.',
                    ])
                ),
                'name' => new Constraints\Optional(
                    new Constraints\Collection([
                        'fields' => $nameFields,
                        'allowMissingFields' => true,
                        'allowExtraFields' => false,
                    ])
                ),
                'hidden' => new Constraints\Optional([
                    new Constraints\Type([
                        'type' => 'bool',
                        'message' => 'The value {{ value }} is not a valid {{ type }}.',
                    ]),
                    new Constraints\NotNull(),
                ]),
                'sellingDenied' => new Constraints\Optional([
                    new Constraints\Type([
                        'type' => 'bool',
                        'message' => 'The value {{ value }} is not a valid {{ type }}.',
                    ]),
                    new Constraints\NotNull(),
                ]),
                'sellingFrom' => new Constraints\Optional(
                    new Constraints\DateTime([
                        'format' => DateTime::ATOM,
                        'message' => 'The value {{ value }} is not a valid DateTime::ATOM format.',
                    ])
                ),
                'sellingTo' => new Constraints\Optional(
                    new Constraints\DateTime([
                        'format' => DateTime::ATOM,
                        'message' => 'The value {{ value }} is not a valid DateTime::ATOM format.',
                    ])
                ),
                'catnum' => new Constraints\Optional(
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'The value {{ value }} cannot be longer then {{ limit }} characters',
                    ])
                ),
                'ean' => new Constraints\Optional(
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'The value {{ value }} cannot be longer then {{ limit }} characters',
                    ])
                ),
                'partno' => new Constraints\Optional(
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'The value {{ value }} cannot be longer then {{ limit }} characters',
                    ])
                ),
                'shortDescription' => new Constraints\Optional(
                    new Constraints\Collection([
                        'fields' => $descriptionFields,
                        'allowMissingFields' => true,
                        'allowExtraFields' => false,
                    ])
                ),
                'longDescription' => new Constraints\Optional(
                    new Constraints\Collection([
                        'fields' => $descriptionFields,
                        'allowMissingFields' => true,
                        'allowExtraFields' => false,
                    ])
                ),
            ],
            'allowExtraFields' => false,
        ];
    }

    /**
     * @return array
     */
    protected function createNameConstraintDefinition(): array
    {
        $nameFields = [];
        foreach ($this->domain->getAllLocales() as $locale) {
            $nameFields[$locale] = new Constraints\Length([
                'max' => 255,
                'maxMessage' => 'The value {{ value }} cannot be longer then {{ limit }} characters',
            ]);
        }

        return $nameFields;
    }

    /**
     * @return array
     */
    protected function createDescriptionConstraintDefinition(): array
    {
        $descriptionFields = [];
        foreach ($this->domain->getAllIds() as $domainId) {
            $descriptionFields[$domainId] = new Constraints\Type([
                'type' => 'string',
                'message' => 'The value {{ value }} is not a valid {{ type }}.',
            ]);
        }

        return $descriptionFields;
    }
}
