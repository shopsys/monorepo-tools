<?php

namespace Shopsys\FrameworkBundle\Component\EntityExtension;

class EntityNameResolver
{
    /**
     * @var string[]
     */
    protected $entityExtensionMap;

    /**
     * @param string[] $entityExtensionMap
     */
    public function __construct(array $entityExtensionMap)
    {
        $this->entityExtensionMap = $entityExtensionMap;
    }

    /**
     * @param string $entityName
     * @return string
     */
    public function resolve(string $entityName): string
    {
        return $this->entityExtensionMap[$entityName] ?? $entityName;
    }

    /**
     * @param mixed $subject
     * @return mixed
     */
    public function resolveIn($subject)
    {
        if (is_string($subject)) {
            return $this->resolveInString($subject);
        } elseif (is_array($subject)) {
            return $this->resolveInArray($subject);
        } elseif (is_object($subject)) {
            $this->resolveInObjectProperties($subject);
        }

        return $subject;
    }

    /**
     * Replace every occurrence of the original FQNs with word borders on both sides and not followed by a back-slash
     *
     * @param string $string
     * @return string
     */
    protected function resolveInString(string $string): string
    {
        foreach ($this->entityExtensionMap as $originalEntityName => $extendedEntityName) {
            $pattern = '~\b' . preg_quote($originalEntityName, '~') . '\b(?!\\\\)~u';
            $string = preg_replace($pattern, $extendedEntityName, $string);
        }

        return $string;
    }

    /**
     * @param array $array
     * @return array
     */
    protected function resolveInArray(array $array): array
    {
        return array_map([$this, 'resolveIn'], $array);
    }

    /**
     * Resolve entity names recursively in all properties of the subject (even private ones)
     *
     * @param object $object
     */
    protected function resolveInObjectProperties($object): void
    {
        $reflection = new \ReflectionObject($object);
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            $resolvedValue = $this->resolveIn($value);
            $property->setValue($object, $resolvedValue);
        }
    }
}
