<?php

namespace Shopsys\FrameworkBundle\Component\Translation;

use JMS\TranslationBundle\Translation\Extractor\FileExtractor;
use JMS\TranslationBundle\Translation\ExtractorManager;
use Psr\Log\LoggerInterface;

class NormalizingExtractorManager extends ExtractorManager
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Translation\MessageIdNormalizer
     */
    private $messageIdNormalizer;

    /**
     * @param \JMS\TranslationBundle\Translation\Extractor\FileExtractor $extractor
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Shopsys\FrameworkBundle\Component\Translation\MessageIdNormalizer $messageIdNormalizer
     */
    public function __construct(FileExtractor $extractor, LoggerInterface $logger, MessageIdNormalizer $messageIdNormalizer)
    {
        parent::__construct($extractor, $logger);
        $this->messageIdNormalizer = $messageIdNormalizer;
    }

    /**
     * @inheritdoc
     */
    public function extract()
    {
        return $this->messageIdNormalizer->getNormalizedCatalogue(parent::extract());
    }
}
