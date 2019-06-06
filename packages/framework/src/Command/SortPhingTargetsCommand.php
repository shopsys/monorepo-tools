<?php

namespace Shopsys\FrameworkBundle\Command;

use Exception;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SortPhingTargetsCommand extends Command
{
    protected const ARG_XML_PATH = 'xml';
    protected const OPTION_ONLY_CHECK = 'check';

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:phing-targets:sort';

    protected function configure()
    {
        $this
            ->setDescription('Sort Phing targets alphabetically')
            ->addArgument(static::ARG_XML_PATH, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path(-s) to the Phing XML configuration')
            ->addOption(static::OPTION_ONLY_CHECK, null, InputOption::VALUE_NONE, 'Will not modify the XML, only fail if the output would be different');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $paths = $input->getArgument(static::ARG_XML_PATH);
        foreach ($paths as $path) {
            $content = file_get_contents($path);

            $sortedContent = $this->sortTargetBlocks($content);

            if (!$input->getOption(static::OPTION_ONLY_CHECK)) {
                file_put_contents($path, $sortedContent);
            } elseif ($content !== $sortedContent) {
                throw new Exception('The targets are not alphabetically sorted. Re-run the command without the "' . static::OPTION_ONLY_CHECK . '" option to fix it.');
            }
        }
    }

    /**
     * @param string $content
     * @return string
     */
    protected function sortTargetBlocks(string $content): string
    {
        $targetBlocks = $this->extractTargetBlocksIndexedByName($content);
        $content = $this->replaceTargetsByPlaceholders($content, $targetBlocks);
        $content = $this->normalizeWhitespaceBetweenPlaceholders($content);

        ksort($targetBlocks);

        $content = $this->replacePlaceholdersByTargets($content, $targetBlocks);

        return $content;
    }

    /**
     * @param string $content
     * @return string[]
     */
    protected function extractTargetBlocksIndexedByName(string $content): array
    {
        $xml = new SimpleXMLElement($content);

        $targetBlocks = [];
        foreach ($xml as $tagName => $item) {
            if ($tagName === 'target') {
                $targetBlocks[(string)$item['name']] = $item->asXML();
            }
        }

        return $targetBlocks;
    }

    /**
     * @param int $position
     * @return string
     */
    protected function getTargetPlaceholder(int $position): string
    {
        return '<!--- TARGET ' . $position . ' -->';
    }

    /**
     * @param string $content
     * @return string
     */
    protected function normalizeWhitespaceBetweenPlaceholders(string $content): string
    {
        return preg_replace('~(<!--- TARGET \d+ -->)(?: *\n)*(?= *<!--- TARGET \d+ -->)~mu', "$1\n\n", $content);
    }

    /**
     * @param string $content
     * @param string[] $targetBlocks
     * @return string
     */
    protected function replaceTargetsByPlaceholders(string $content, array $targetBlocks): string
    {
        $position = 1;
        foreach ($targetBlocks as $targetBlock) {
            $targetPlaceholder = $this->getTargetPlaceholder($position++);

            $replacedContent = str_replace($targetBlock, $targetPlaceholder, $content);

            if ($content === $replacedContent) {
                throw new Exception("Target block was not found in the original XML, probably because of unexpected formatting:\n\n" . $targetBlock);
            }

            $content = $replacedContent;
        }

        return $content;
    }

    /**
     * @param string $content
     * @param string[] $targetBlocks
     * @return string
     */
    protected function replacePlaceholdersByTargets(string $content, array $targetBlocks): string
    {
        $position = 1;
        foreach ($targetBlocks as $targetBlock) {
            $targetPlaceholder = $this->getTargetPlaceholder($position++);

            $content = str_replace($targetPlaceholder, $targetBlock, $content);
        }
        return $content;
    }
}
