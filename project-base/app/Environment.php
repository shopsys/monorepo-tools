<?php

namespace Shopsys;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Shopsys\FrameworkBundle\Component\Environment\EnvironmentFileSetting;
use Shopsys\FrameworkBundle\Component\Environment\EnvironmentType;

class Environment
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Environment\EnvironmentFileSetting|null
     */
    private static $environmentFileSetting;

    /**
     * @param \Composer\Script\Event $event
     */
    public static function checkEnvironment(Event $event)
    {
        $io = $event->getIO();
        /* @var $io \Composer\IO\IOInterface */
        $environmentFileSetting = self::getEnvironmentFileSetting();
        if ($io->isInteractive() && !$environmentFileSetting->isAnyEnvironmentSet()) {
            if ($io->askConfirmation('Build in production environment? (Y/n): ', true)) {
                $environmentFileSetting->createFileForEnvironment(EnvironmentType::PRODUCTION);
            } else {
                $environmentFileSetting->createFileForEnvironment(EnvironmentType::DEVELOPMENT);
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
        return self::getEnvironmentFileSetting()->getEnvironment($console);
    }

    /**
     * @param \Composer\IO\IOInterface $io
     */
    public static function printEnvironmentInfo(IOInterface $io)
    {
        $io->write("\nEnvironment is <info>" . self::getEnvironment(false) . "</info>\n");
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Environment\EnvironmentFileSetting
     */
    private static function getEnvironmentFileSetting()
    {
        if (self::$environmentFileSetting === null) {
            self::$environmentFileSetting = new EnvironmentFileSetting(__DIR__ . '/..');
        }
        return self::$environmentFileSetting;
    }
}
