<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Command;

use RuntimeException;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PhingConfigFixerCommand extends Command
{
    protected const RETURN_CODE_OK = 0;
    protected const RETURN_CODE_ERROR = 1;
    protected const ARG_XML_PATH = 'xml';
    protected const OPTION_ONLY_CHECK = 'check';

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:phing-config:fix';

    protected function configure(): void
    {
        $this
            ->setDescription('Fixes syntax of Phing configuration automatically (sorts targets alphabetically and normalizes whitespace).')
            ->addArgument(static::ARG_XML_PATH, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path(-s) to the Phing XML configuration')
            ->addOption(static::OPTION_ONLY_CHECK, null, InputOption::VALUE_NONE, 'Will not modify the XML, only fails if the output would be different');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $returnCode = static::RETURN_CODE_OK;
        $io = new SymfonyStyle($input, $output);

        $checkOnly = $input->getOption(static::OPTION_ONLY_CHECK);
        $paths = $input->getArgument(static::ARG_XML_PATH);
        foreach ($paths as $path) {
            $content = file_get_contents($path);

            $sortedContent = $this->fixConfiguration($content);

            $isContentChanged = $content !== $sortedContent;
            if ($checkOnly && $isContentChanged) {
                $returnCode = static::RETURN_CODE_ERROR;

                $io->error(sprintf('The Phing configuration in "%s" in not OK.', $path));
            } elseif ($isContentChanged) {
                file_put_contents($path, $sortedContent);

                $io->success(sprintf('The Phing configuration in "%s" was fixed.', $path));
            }
        }

        if ($returnCode === static::RETURN_CODE_OK) {
            $io->success('All Phing configuration files are OK.');
        } elseif ($returnCode === static::RETURN_CODE_ERROR) {
            $io->error('Some Phing configuration files are not OK.');

            $io->comment(sprintf('Re-run the command without the "%s" option to fix it automatically.', static::OPTION_ONLY_CHECK));
        }

        return $returnCode;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function fixConfiguration(string $content): string
    {
        $content = $this->normalizeXml($content);
        $targetBlocks = $this->extractTargetBlocksIndexedByName($content);
        $content = $this->replaceTargetsByPlaceholders($content, $targetBlocks);
        $content = $this->normalizeWhitespaceBetweenPlaceholders($content);

        ksort($targetBlocks);

        $content = $this->replacePlaceholdersByTargets($content, $targetBlocks);

        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function normalizeXml(string $content): string
    {
        $xml = new SimpleXMLElement($content);

        return $xml->asXML();
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
                throw new RuntimeException("This block was not found in the XML content and could not be replaced:\n\n" . $targetBlock);
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

            $replacedContent = str_replace($targetPlaceholder, $targetBlock, $content);

            if ($content === $replacedContent) {
                throw new RuntimeException(sprintf('The placeholder for target #%d was not found in the XML content and could not be replaced.', $position));
            }

            $content = $replacedContent;
        }

        return $content;
    }
}
