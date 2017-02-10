<?php

namespace Shopsys\ShopBundle\Component\Doctrine;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class GroupedScalarHydrator extends AbstractHydrator
{
    const HYDRATION_MODE = 'GroupedScalarHydrator';

    /**
     * {@inheritdoc}
     */
    protected function hydrateAllData() {
        $result = [];

        while ($data = $this->_stmt->fetch(\PDO::FETCH_ASSOC)) {
            $this->hydrateRowData($data, $result);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateRowData(array $data, array &$result) {
        $rowData = $this->gatherGroupedScalarRowData($data);
        $result[] = $rowData;
    }

    /**
     * Copies implementation of gatherScalarRowData(), but groups non-scalar columns
     * as array of columns.
     *
     * @param array $data
     * @return array
     */
    private function gatherGroupedScalarRowData(&$data) {
        $rowData = [];

        foreach ($data as $key => $value) {
            $cacheKeyInfo = $this->hydrateColumnInfo($key);
            if ($cacheKeyInfo === null) {
                continue;
            }

            $fieldName = $cacheKeyInfo['fieldName'];

            if (isset($cacheKeyInfo['isScalar'])) {
                $rowData[$fieldName] = $value;
            } else {
                $dqlAlias = $cacheKeyInfo['dqlAlias'];
                $type = $cacheKeyInfo['type'];
                $value = $type ? $type->convertToPHPValue($value, $this->_platform) : $value;

                $rowData[$dqlAlias][$fieldName] = $value;
            }
        }

        return $rowData;
    }
}
