<?php

namespace Shopsys\ShopBundle\Command;

use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TranslationReplaceSourceCommand extends Command
{
    const ARG_TRANSLATIONS_DIR = 'translationsDir';
    const ARG_SOURCE_CODE_DIR = 'sourceCodeDir';
    const ARG_TARGET_LOCALE = 'targetLocale';

    const FILE_NAME_REPLACEMENT_ERRORS = 'replacement_errors.log';

    protected function configure()
    {
        $this
            ->setName('shopsys:translation:replace-source')
            ->setDescription('Replace translation sources to translated texts in target locale. To be used after translation:extract.')
            ->setHelp('Translation messages from whole project should be extracted first as this tool depends on dumped references.')
            ->addArgument(self::ARG_TRANSLATIONS_DIR, InputArgument::REQUIRED, 'Directory of extracted translations in .po format')
            ->addArgument(self::ARG_SOURCE_CODE_DIR, InputArgument::REQUIRED, 'Directory searched for replacements in source code')
            ->addArgument(self::ARG_TARGET_LOCALE, InputArgument::REQUIRED, 'Locale of translations to replace original sources')
            ->addUsage('./src/Shopsys/ShopBundle/Resources/translations ./src/Shopsys/ShopBundle en');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $translationsDirectory = new DirectoryIterator($input->getArgument(self::ARG_TRANSLATIONS_DIR));
        $targetLocale = $input->getArgument(self::ARG_TARGET_LOCALE);

        $allReplacements = $this->getAllReplacements($translationsDirectory, $targetLocale);

        $output->writeln('');

        $replacements = $this->filterReplacementsWithUniqueOldSource($allReplacements, $output);
        $replacements = $this->filterFilledReplacements($replacements, $output);
        $replacements = $this->filterReplacementsWithUniqueNewSource($replacements, $output);
        $replacements = $this->filterNonEqualReplacements($replacements, $output);

        $replacements = $this->sortBySourceLengthDesc($replacements);

        $output->writeln('');

        $allPathNames = $this->getAllPathNames($input->getArgument(self::ARG_SOURCE_CODE_DIR));

        $this->replaceAllInFiles($replacements, $allPathNames, $output);
        $this->replaceSourcesInPoFiles($replacements, $translationsDirectory);
    }

    /**
     * @param \DirectoryIterator $translationsDirectory
     * @param string $targetLocale
     * @return \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[]
     */
    private function getAllReplacements(DirectoryIterator $translationsDirectory, $targetLocale)
    {
        $allReplacements = [];
        foreach ($translationsDirectory as $translationsDirectoryItem) {
            if ($this->isTranslationFileInLocale($translationsDirectoryItem, $targetLocale)) {
                $newReplacements = $this->extractReplacementsFromPoFile($translationsDirectoryItem->getFileInfo());
                $allReplacements = array_merge($allReplacements, $newReplacements);
            }
        }

        return $allReplacements;
    }

    /**
     * @param \DirectoryIterator $directoryIterator
     * @param string $targetLocale
     * @return bool
     */
    private function isTranslationFileInLocale(DirectoryIterator $directoryIterator, $targetLocale)
    {
        $translationFilePattern = '~\.' . preg_quote($targetLocale, '~') . '\.po~';

        return $directoryIterator->isFile() && preg_match($translationFilePattern, $directoryIterator->getFilename());
    }

    /**
     * @see \Symfony\Component\Translation\Loader\PoFileLoader::parse
     * @param \SplFileInfo $file
     * @return \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[]
     */
    private function extractReplacementsFromPoFile(SplFileInfo $file)
    {
        $stream = fopen($file->getPathname(), 'r');

        $defaults = [
            'comments' => [],
            'ids' => [],
            'translated' => null,
            'domain' => explode('.', $file->getFilename())[0],
        ];

        $translationSourceReplacements = [];
        $item = $defaults;

        while ($line = fgets($stream)) {
            $line = trim($line);

            if ($line === '') {
                // Whitespace indicated current item is done
                $this->parsePoFileItem($translationSourceReplacements, $item);
                $item = $defaults;
            } elseif (substr($line, 0, 3) === '#: ') {
                $item['comments'][] = substr($line, 3);
            } elseif (substr($line, 0, 7) === 'msgid "') {
                if (count($item['ids']) > 0) {
                    throw new \Shopsys\ShopBundle\Command\Exception\TranslationReplaceSourceCommandException(
                        sprintf('Parse error: Message ID "%s" must be separated from previous IDs by an empty line.', substr($line, 7, -1))
                    );
                }
                $item['ids']['singular'] = substr($line, 7, -1);
            } elseif (substr($line, 0, 8) === 'msgstr "') {
                $item['translated'] = substr($line, 8, -1);
            } elseif ($line[0] === '"') {
                $continues = isset($item['translated']) ? 'translated' : 'ids';

                if (is_array($item[$continues])) {
                    end($item[$continues]);
                    $item[$continues][key($item[$continues])] .= substr($line, 1, -1);
                } else {
                    $item[$continues] .= substr($line, 1, -1);
                }
            } elseif (substr($line, 0, 14) === 'msgid_plural "') {
                $item['ids']['plural'] = substr($line, 14, -1);
            } elseif (substr($line, 0, 7) === 'msgstr[') {
                $size = strpos($line, ']');
                $item['translated'][(int)substr($line, 7, 1)] = substr($line, $size + 3, -1);
            }
        }
        // save last item
        $this->parsePoFileItem($translationSourceReplacements, $item);

        fclose($stream);

        return $translationSourceReplacements;
    }

    /**
     * @see \Symfony\Component\Translation\Loader\PoFileLoader::addMessage
     * @param \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[] $translationSourceReplacements
     * @param array $item
     */
    private function parsePoFileItem(array &$translationSourceReplacements, array $item)
    {
        $sourceFileReferences = [];
        foreach ($item['comments'] as $comment) {
            $sourceFileReferences = array_merge($sourceFileReferences, explode(' ', $comment));
        }
        $sourceFileReferences = array_filter($sourceFileReferences);
        $sourceFileReferences = str_replace('\\', '/', $sourceFileReferences);

        if (is_array($item['translated'])) {
            $translationSourceReplacements[] = new TranslationSourceReplacement(
                stripcslashes($item['ids']['singular']),
                stripcslashes($item['translated'][0]),
                $item['domain'],
                $sourceFileReferences
            );
            if (isset($item['ids']['plural'])) {
                $plurals = $item['translated'];
                // PO are by definition indexed so sort by index.
                ksort($plurals);
                // Make sure every index is filled.
                end($plurals);
                $count = key($plurals);
                // Fill missing spots with '-'.
                $empties = array_fill(0, $count + 1, '-');
                $plurals += $empties;
                ksort($plurals);
                $translationSourceReplacements[] = new TranslationSourceReplacement(
                    stripcslashes($item['ids']['plural']),
                    stripcslashes(implode('|', $plurals)),
                    $item['domain'],
                    $sourceFileReferences
                );
            }
        } elseif (!empty($item['ids']['singular'])) {
            $translationSourceReplacements[] = new TranslationSourceReplacement(
                stripcslashes($item['ids']['singular']),
                stripcslashes($item['translated']),
                $item['domain'],
                $sourceFileReferences
            );
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[] $replacements
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[]
     */
    private function filterReplacementsWithUniqueOldSource(array $replacements, OutputInterface $output)
    {
        $oldSourceUsageCounts = [];
        foreach ($replacements as $replacement) {
            if (array_key_exists($replacement->getOldSource(), $oldSourceUsageCounts)) {
                $oldSourceUsageCounts[$replacement->getOldSource()]++;
            } else {
                $oldSourceUsageCounts[$replacement->getOldSource()] = 1;
            }
        }

        foreach ($oldSourceUsageCounts as $oldSource => $oldSourceUsageCount) {
            if ($oldSourceUsageCount > 1) {
                $domains = [];
                foreach ($replacements as $index => $replacement) {
                    if ($replacement->getOldSource() === $oldSource) {
                        $domains[] = '"<fg=yellow>' . $replacement->getDomain() . '</fg=yellow>"';

                        unset($replacements[$index]);
                    }
                }

                $output->writeln(sprintf(
                    'Translation for source "<fg=yellow>%s</fg=yellow>" is defined in multiple domains (%s) and will not be replaced',
                    $oldSource,
                    implode(', ', $domains)
                ));
            }
        }

        return $replacements;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[] $replacements
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[]
     */
    private function filterFilledReplacements(array $replacements, OutputInterface $output)
    {
        foreach ($replacements as $index => $replacement) {
            if ($replacement->getNewSource() === '') {
                $output->writeln(sprintf(
                    'Translation for source "<fg=yellow>%s</fg=yellow>" is empty and will not be replaced',
                    $replacement->getOldSource()
                ));

                unset($replacements[$index]);
            }
        }

        return $replacements;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[] $replacements
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[]
     */
    private function filterReplacementsWithUniqueNewSource(array $replacements, OutputInterface $output)
    {
        $newSourceUsageCounts = [];
        foreach ($replacements as $replacement) {
            if (array_key_exists($replacement->getNewSource(), $newSourceUsageCounts)) {
                $newSourceUsageCounts[$replacement->getNewSource()]++;
            } else {
                $newSourceUsageCounts[$replacement->getNewSource()] = 1;
            }
        }

        foreach ($newSourceUsageCounts as $newSource => $newSourceUsageCount) {
            if ($newSourceUsageCount > 1) {
                $oldSources = [];
                foreach ($replacements as $index => $replacement) {
                    if ($replacement->getNewSource() === $newSource) {
                        $oldSources[] = '"<fg=yellow>' . $replacement->getOldSource() . '</fg=yellow>"';

                        unset($replacements[$index]);
                    }
                }

                $output->writeln(sprintf(
                    'There are %d different sources for translation "<fg=yellow>%s</fg=yellow>" (%s) and they will not be replaced.',
                    $newSourceUsageCount,
                    $newSource,
                    implode(', ', $oldSources)
                ));
            }
        }

        return $replacements;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[] $replacements
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[]
     */
    private function filterNonEqualReplacements($replacements, $output)
    {
        foreach ($replacements as $index => $replacement) {
            if ($replacement->getOldSource() === $replacement->getNewSource()) {
                $output->writeln(sprintf(
                    'Translation of source "<fg=yellow>%s</fg=yellow>" is not changed in any way and will not be replaced.',
                    $replacement->getOldSource()
                ));

                unset($replacements[$index]);
            }
        }

        return $replacements;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[] $replacements
     * @returns \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[] $replacements
     */
    private function sortBySourceLengthDesc($replacements)
    {
        usort($replacements, function (TranslationSourceReplacement $replacementLeft, TranslationSourceReplacement $replacementRight) {
            $lengthLeft = strlen($replacementLeft->getOldSource());
            $lengthRight = strlen($replacementRight->getOldSource());

            if ($lengthLeft === $lengthRight) {
                return 0;
            } elseif ($lengthLeft < $lengthRight) {
                return 1;
            } else {
                return -1;
            }
        });

        return $replacements;
    }

    /**
     * @param string $searchedDirectoryPath
     * @return string[]
     */
    private function getAllPathNames($searchedDirectoryPath)
    {
        $recursiveIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($searchedDirectoryPath));
        $pathNames = [];
        foreach ($recursiveIterator as $pathName => $recursiveIteratorItem) {
            if ($recursiveIteratorItem->isFile()) {
                $pathNames[] = str_replace('\\', '/', $pathName);
            }
        }

        return $pathNames;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[] $replacements
     * @param string[] $searchedPathNames
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function replaceAllInFiles(array $replacements, array $searchedPathNames, OutputInterface $output)
    {
        if (file_exists(self::FILE_NAME_REPLACEMENT_ERRORS)) {
            rename(self::FILE_NAME_REPLACEMENT_ERRORS, sprintf('%s.%d.bak', self::FILE_NAME_REPLACEMENT_ERRORS, time()));
        }

        $totalCount = 0;
        $successfulCount = 0;
        foreach ($replacements as $replacement) {
            foreach ($replacement->getSourceFilePaths() as $sourceFilePath) {
                $realCount = $this->makeReplacements($replacement, $searchedPathNames, '/' . $sourceFilePath);
                $expectedCount = $replacement->getExpectedReplacementsCountForSourceFilePath($sourceFilePath);
                $isExpectedCountExact = $replacement->isExpectedReplacementsCountExact($sourceFilePath);

                if ($realCount === $expectedCount || !$isExpectedCountExact && $realCount > $expectedCount) {
                    $successfulCount++;
                } else {
                    $this->logReplacementError($sourceFilePath, $replacement, $realCount, $expectedCount, $isExpectedCountExact, $output);
                }
                $totalCount++;
            }
        }

        if ($totalCount === 0) {
            $output->writeln('<fg=cyan>Nothing to replace.</fg=cyan>');
        } else {
            $output->writeln('');
            $output->writeln(sprintf('Replacement success rate: <fg=cyan>%.2f%%</fg=cyan>', 100 * $successfulCount / $totalCount));
        }

        if ($successfulCount < $totalCount) {
            $output->writeln(sprintf('Error report logged in <fg=cyan>%s</fg=cyan>', self::FILE_NAME_REPLACEMENT_ERRORS));
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement $replacement
     * @param string[] $searchedPathNames
     * @param string $sourceFilePath
     * @return int|null
     */
    private function makeReplacements(TranslationSourceReplacement $replacement, array $searchedPathNames, $sourceFilePath)
    {
        $fileFound = false;
        $totalCount = 0;
        $matchingPathNames = array_filter(
            $searchedPathNames,
            function ($pathName) use ($sourceFilePath) {
                return substr($pathName, -strlen($sourceFilePath)) === $sourceFilePath;
            }
        );

        foreach ($matchingPathNames as $matchingPathName) {
            $fileFound = true;
            $contents = file_get_contents($matchingPathName);

            // Search for original translation sources
            $searchPattern = $replacement->getOldSource();
            // Message sources are normalized (any whitespace => one space), match every whitespace
            $searchPattern = preg_replace('~\s+~', '\s+', preg_quote($searchPattern, '~'));
            // Match only texts surrounded by a non-alphanumeric characters (prevent "abc" => "xyc" while searching for "ab" => "xy")
            $searchPattern = '~(\W)' . $searchPattern . '(\W)~u';

            // Replace with translated texts
            $replacementPattern = $replacement->getNewSource();
            // Escape apostrophes as the messages are mostly used in PHP or Twig string literals
            $replacementPattern = addcslashes($replacementPattern, '\'');
            // Escape back-references in replacement, see http://us1.php.net/manual/en/function.preg-replace.php#103985
            $replacementPattern = preg_replace('~\$(\d)~', '\\\$$1', $replacementPattern);
            // Return the non-alphanumeric character to their original places via back-reference
            $replacementPattern = '${1}' . $replacementPattern . '${2}';

            $newContents = preg_replace($searchPattern, $replacementPattern, $contents, -1, $count);

            file_put_contents($matchingPathName, $newContents);

            $totalCount += $count;
        }

        return $fileFound ? $totalCount : null;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement[] $replacements
     * @param \DirectoryIterator $directory
     */
    private function replaceSourcesInPoFiles(array $replacements, DirectoryIterator $directory)
    {
        foreach ($directory as $item) {
            if ($item->isFile() && $item->getExtension() === 'po') {
                $newContents = '';

                $stream = fopen($item->getPathname(), 'r');

                $match = null;
                $matchContent = '';

                while ($line = fgets($stream)) {
                    $line = trim($line);

                    if (substr($line, 0, 7) === 'msgid "') {
                        $match = 'msgid';
                        $matchContent = stripcslashes(substr($line, 7, -1));
                    } elseif (substr($line, 0, 14) === 'msgid_plural "') {
                        $match = 'msgid_plural';
                        $matchContent = stripcslashes(substr($line, 14, -1));
                    } elseif (substr($line, 0, 1) === '"' && $match !== null) {
                        $matchContent .= stripcslashes(substr($line, 1, -1));
                    } else {
                        if ($match !== null) {
                            foreach ($replacements as $replacement) {
                                if ($matchContent === $replacement->getOldSource()) {
                                    $matchContent = $replacement->getNewSource();

                                    break;
                                }
                            }
                            $newContents .= $match . ' "' . addcslashes($matchContent, "\0..\37\42\134") . '"' . "\n";
                            $match = null;
                        }
                        $newContents .= $line . "\n";
                    }
                }

                fclose($stream);

                file_put_contents($item->getPathname(), $newContents);
            }
        }
    }

    /**
     * @param string $filePath
     * @param \Shopsys\ShopBundle\Component\Translation\TranslationSourceReplacement $replacement
     * @param int|null $realCount
     * @param int $expectedCount
     * @param bool $isExpectedCountExact
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function logReplacementError(
        $filePath,
        TranslationSourceReplacement $replacement,
        $realCount,
        $expectedCount,
        $isExpectedCountExact,
        OutputInterface $output
    ) {
        if ($realCount === null) {
            $output->writeln(
                sprintf('No file "<fg=red>%s</fg=red>" found for source "<fg=red>%s</fg=red>"!', $filePath, $replacement->getOldSource())
            );
        } elseif ($realCount === 0) {
            $message = $isExpectedCountExact
                ? 'Source "<fg=red>%s</fg=red>" not found in "<fg=red>%s</fg=red>", expected %d matches.'
                : 'Source "<fg=red>%s</fg=red>" not found in "<fg=red>%s</fg=red>", expected at least %d matches.';
            $output->writeln(
                sprintf($message, $replacement->getOldSource(), $filePath, $expectedCount)
            );
        } else {
            $message = $isExpectedCountExact
                ? 'Source "<fg=red>%s</fg=red>" was replaced in "<fg=red>%s</fg=red>" %d times, expected %d matches.'
                : 'Source "<fg=red>%s</fg=red>" was replaced in "<fg=red>%s</fg=red>" only %d times, expected at least %d matches.';
            $output->writeln(
                sprintf($message, $replacement->getOldSource(), $filePath, $realCount, $expectedCount)
            );
        }

        $errorReport = [
            'ERROR IN FILE: ' . $filePath,
            'OLD SOURCE:    ' . $replacement->getOldSource(),
            'NEW SOURCE:    ' . $replacement->getNewSource(),
            'DOMAIN:        ' . $replacement->getDomain(),
            'REPLACEMENTS:  ' . $realCount,
            'EXPECTED:      ' . $expectedCount . ($isExpectedCountExact ? '' : ' or more'),
        ];
        file_put_contents(self::FILE_NAME_REPLACEMENT_ERRORS, implode("\n", $errorReport) . "\n\n", FILE_APPEND);
    }
}
