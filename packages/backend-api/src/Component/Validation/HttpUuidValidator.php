<?php

declare(strict_types=1);

namespace Shopsys\BackendApiBundle\Component\Validation;

use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HttpUuidValidator
{
    /**
     * @param array $uuids
     */
    public static function validateUuids(array $uuids): void
    {
        $invalidUuids = [];

        foreach ($uuids as $uuid) {
            if (!Uuid::isValid($uuid)) {
                $invalidUuids[] = $uuid;
            }
        }

        if (count($invalidUuids) === 1) {
            throw new BadRequestHttpException('This UUID is not valid: ' . reset($invalidUuids));
        } elseif (count($invalidUuids) > 1) {
            throw new BadRequestHttpException('These UUIDS are not valid: ' . implode(', ', $invalidUuids));
        }
    }
}
