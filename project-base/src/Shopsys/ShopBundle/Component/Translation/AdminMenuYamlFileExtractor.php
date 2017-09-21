<?php

namespace Shopsys\ShopBundle\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use SplFileInfo;
use Symfony\Component\Yaml\Parser;
use Twig_Node;

class AdminMenuYamlFileExtractor implements FileVisitorInterface
{
    const ADMIN_MENU_ITEM_LABEL_DOMAIN = 'messages';

    /**
     * @var string
     */
    private $adminMenuRealPath;

    /**
     * @param string $adminMenuFilePath
     */
    public function __construct($adminMenuFilePath)
    {
        $this->adminMenuRealPath = realpath($adminMenuFilePath);
    }

    /**
     * @param \SplFileInfo $file
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     */
    public function visitFile(SplFileInfo $file, MessageCatalogue $catalogue)
    {
        if ($file->getRealPath() === realpath($this->adminMenuRealPath)) {
            $contents = file_get_contents($file->getPathname());
            $yamlParser = new Parser();
            $items = $yamlParser->parse($contents);

            $this->collectAdminMenuItems($items, $file, $catalogue);
        }
    }

    /**
     * @param \SplFileInfo $file
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     * @param array $ast
     */
    public function visitPhpFile(SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
    }

    /**
     * @param \SplFileInfo $file
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     * @param \Twig_Node $node
     */
    public function visitTwigFile(SplFileInfo $file, MessageCatalogue $catalogue, Twig_Node $node)
    {
    }

    /**
     * @param array[] $items
     * @param \SplFileInfo $file
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     */
    private function collectAdminMenuItems(array $items, SplFileInfo $file, MessageCatalogue $catalogue)
    {
        foreach ($items as $item) {
            $message = new Message($item['label'], self::ADMIN_MENU_ITEM_LABEL_DOMAIN);
            $message->addSource(new FileSource($file->getPathname()));

            $catalogue->add($message);

            if (array_key_exists('items', $item)) {
                $this->collectAdminMenuItems($item['items'], $file, $catalogue);
            }
        }
    }
}
