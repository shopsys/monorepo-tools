<?php

namespace Shopsys;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Shopsys\FrameworkBundle\Component\Environment\EnvironmentType;

class Environment
{
    const FILE_DEVELOPMENT = 'DEVELOPMENT';
    const FILE_PRODUCTION = 'PRODUCTION';
    const FILE_TEST = 'TEST';

    /**
     * @param \Composer\Script\Event $event
     */
    public static function checkEnvironment(Event $event)
    {
        $io = $event->getIO();
        /* @var $io \Composer\IO\IOInterface */
        if ($io->isInteractive() && self::getEnvironmentSetting(false) === null) {
            if ($io->askConfirmation('Build in production environment? (Y/n): ', true)) {
                self::createFile(self::getRootDir() . '/' . self::FILE_PRODUCTION);
            } else {
                self::createFile(self::getRootDir() . '/' . self::FILE_DEVELOPMENT);
            }
        }
        self::printEnvironmentInfo($io);
    }

    /**
     * @param bool $console
     * @return string
     */
    public static function getEnvironment($console)
    {
        $environmentSetting = self::getEnvironmentSetting($console);
        return $environmentSetting ?: EnvironmentType::PRODUCTION;
    }


    /**
     * @param \Composer\IO\IOInterface $io
     */
    public static function printEnvironmentInfo(IOInterface $io)
    {
        $io->write("\nEnvironment is <info>" . self::getEnvironment(false) . "</info>\n");
    }

    /**
     * @param string $filepath
     */
    private static function createFile($filepath)
    {
        $file = fopen($filepath, 'w');
        fclose($file);
    }

    /**
     * @return string
     */
    private static function getRootDir()
    {
        return __DIR__ . '/..';
    }

    /**
     * @param bool $ignoreTestFile
     * @return string|null
     */
    private static function getEnvironmentSetting($ignoreTestFile)
    {
        if (!$ignoreTestFile && is_file(self::getRootDir() . '/' . self::FILE_TEST)) {
            return EnvironmentType::TEST;
        } elseif (is_file(self::getRootDir() . '/' . self::FILE_DEVELOPMENT)) {
            return EnvironmentType::DEVELOPMENT;
        } elseif (is_file(self::getRootDir() . '/' . self::FILE_PRODUCTION)) {
            return EnvironmentType::PRODUCTION;
        }
        return null;
    }
}
