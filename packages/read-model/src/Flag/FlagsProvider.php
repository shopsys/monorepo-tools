<?php

declare(strict_types=1);

namespace Shopsys\ReadModelBundle\Flag;

use Shopsys\FrameworkBundle\Model\Product\Flag\FlagFacade;

class FlagsProvider
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Flag\FlagFacade
     */
    protected $flagFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    protected $allFlags;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagFacade $flagFacade
     */
    public function __construct(FlagFacade $flagFacade)
    {
        $this->flagFacade = $flagFacade;
    }

    /**
     * @param int[] $flagIds
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    public function getFlagsByIds(array $flagIds): array
    {
        if ($this->allFlags === null) {
            $this->allFlags = $this->flagFacade->getAll();
        }

        $flags = [];
        foreach ($this->allFlags as $flag) {
            if (in_array($flag->getId(), $flagIds, true)) {
                $flags[] = $flag;
            }
        }

        return $flags;
    }
}
