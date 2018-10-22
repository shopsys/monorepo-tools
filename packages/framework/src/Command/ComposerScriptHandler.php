<?php

namespace Shopsys\FrameworkBundle\Command;

use Composer\Script\Event;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;

/**
 * ScriptHandler enables to execute Symfony commands as Composer scripts (eg. after each run of "composer install")
 */
class ComposerScriptHandler extends ScriptHandler
{
    /**
     * @param \Composer\Script\Event $event
     */
    public static function configureDomainsUrls(Event $event)
    {
        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'configure domains URLs');

        if (null === $consoleDir) {
            $event->getIO()->writeError('Could not locate console dir to configure domains URLs.');

            return;
        }

        static::executeCommand($event, $consoleDir, 'shopsys:domains-urls:configure', $options['process-timeout']);
    }
}
