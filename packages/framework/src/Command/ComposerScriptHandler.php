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
    public static function postInstall(Event $event)
    {
        static::executeCommands($event, [
            'shopsys:domains-urls:configure',
            'ckeditor:install --clear=skip --release=full --tag=4.5.11',
            'cache:clear --no-warmup',
        ]);
    }

    /**
     * @param \Composer\Script\Event $event
     */
    public static function postUpdate(Event $event)
    {
        static::executeCommands($event, [
            'shopsys:domains-urls:configure',
            'ckeditor:install --clear=skip --release=full --tag=4.5.11',
            'cache:clear --no-warmup',
        ]);
    }

    /**
     * @param \Composer\Script\Event $event
     * @param string[] $commands
     */
    protected static function executeCommands(Event $event, array $commands)
    {
        $io = $event->getIO();
        $actionName = sprintf('execute Shopsys Framework commands for "%s" Composer event', $event->getName());
        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, $actionName);

        if (null === $consoleDir) {
            $io->writeError(sprintf('Could not locate console dir to %s.', $actionName));
            $io->writeError(sprintf('Commands "%s" not executed.', implode('", "', $commands)));

            return;
        }

        foreach ($commands as $command) {
            $io->write(['', sprintf('> Running "%s" command:', $command)]);

            static::executeCommand($event, $consoleDir, $command, $options['process-timeout']);
        }
    }
}
