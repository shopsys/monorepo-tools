<?php

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Article\Article;
use Shopsys\FrameworkBundle\Model\Article\ArticleData;
use Shopsys\FrameworkBundle\Model\Article\ArticleDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Article\ArticleFacade;

class ArticleDataFixture extends AbstractReferenceFixture
{
    const ARTICLE_TERMS_AND_CONDITIONS_2 = 'article_terms_and_conditions_2';
    const ARTICLE_PRIVACY_POLICY_2 = 'article_privacy_policy_2';
    const ARTICLE_COOKIES_2 = 'article_cookies_2';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Article\ArticleFacade
     */
    private $articleFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Article\ArticleDataFactoryInterface
     */
    private $articleDataFactory;

    public function __construct(ArticleFacade $articleFacade, ArticleDataFactoryInterface $articleDataFactory)
    {
        $this->articleFacade = $articleFacade;
        $this->articleDataFactory = $articleDataFactory;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $articleData = $this->articleDataFactory->create();

        $articleData->domainId = 2;
        $articleData->name = 'Novinky';
        $articleData->text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus felis nisi, tincidunt sollicitudin augue eu, laoreet blandit sem. Donec rutrum augue a elit imperdiet, eu vehicula tortor porta. Vivamus pulvinar sem non auctor dictum. Morbi eleifend semper enim, eu faucibus tortor posuere vitae. Donec tincidunt ipsum ullamcorper nisi accumsan tincidunt. Aenean sed velit massa. Nullam interdum eget est ut convallis. Vestibulum et mauris condimentum, rutrum sem congue, suscipit arcu.\nSed tristique vehicula ipsum, ut vulputate tortor feugiat eu. Vivamus convallis quam vulputate faucibus facilisis. Curabitur tincidunt pulvinar leo, eu dapibus augue lacinia a. Fusce sed tincidunt nunc. Morbi a nisi a odio pharetra laoreet nec eget quam. In in nisl tortor. Ut fringilla vitae lectus eu venenatis. Nullam interdum sed odio a posuere. Fusce pellentesque dui vel tortor blandit, a dictum nunc congue.';
        $articleData->placement = Article::PLACEMENT_TOP_MENU;
        $this->createArticle($articleData);

        $articleData->name = 'Jak nakupovat';
        $articleData->text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus felis nisi, tincidunt sollicitudin augue eu, laoreet blandit sem. Donec rutrum augue a elit imperdiet, eu vehicula tortor porta. Vivamus pulvinar sem non auctor dictum. Morbi eleifend semper enim, eu faucibus tortor posuere vitae. Donec tincidunt ipsum ullamcorper nisi accumsan tincidunt. Aenean sed velit massa. Nullam interdum eget est ut convallis. Vestibulum et mauris condimentum, rutrum sem congue, suscipit arcu.\nSed tristique vehicula ipsum, ut vulputate tortor feugiat eu. Vivamus convallis quam vulputate faucibus facilisis. Curabitur tincidunt pulvinar leo, eu dapibus augue lacinia a. Fusce sed tincidunt nunc. Morbi a nisi a odio pharetra laoreet nec eget quam. In in nisl tortor. Ut fringilla vitae lectus eu venenatis. Nullam interdum sed odio a posuere. Fusce pellentesque dui vel tortor blandit, a dictum nunc congue.';
        $articleData->placement = Article::PLACEMENT_TOP_MENU;
        $this->createArticle($articleData);

        $articleData->name = 'Obchodní podmínky';
        $articleData->text = 'Morbi posuere mauris dolor, quis accumsan dolor ullamcorper eget. Phasellus at elementum magna, et pretium neque. Praesent tristique lorem mi, eget varius quam aliquam eget. Vivamus ultrices interdum nisi, sed placerat lectus fermentum non. Phasellus ac quam vitae nisi aliquam vestibulum. Sed rhoncus tortor a arcu sagittis placerat. Nulla lectus nunc, ultrices ac faucibus sed, accumsan nec diam. Nam auctor neque quis tincidunt tempus. Nunc eget risus tristique, lobortis metus vitae, pellentesque leo. Vivamus placerat turpis ac dolor vehicula tincidunt. Sed venenatis, ante id ultrices convallis, lacus elit porttitor dolor, non porta risus ipsum ac justo. Integer id pretium quam, id placerat nulla.';
        $articleData->placement = Article::PLACEMENT_FOOTER;
        $this->createArticle($articleData, self::ARTICLE_TERMS_AND_CONDITIONS_2);

        $articleData->name = 'Zásady ochrany osobních údajů';
        $articleData->text = 'Morbi posuere mauris dolor, quis accumsan dolor ullamcorper eget. Phasellus at elementum magna, et pretium neque. Praesent tristique lorem mi, eget varius quam aliquam eget. Vivamus ultrices interdum nisi, sed placerat lectus fermentum non. Phasellus ac quam vitae nisi aliquam vestibulum. Sed rhoncus tortor a arcu sagittis placerat. Nulla lectus nunc, ultrices ac faucibus sed, accumsan nec diam. Nam auctor neque quis tincidunt tempus. Nunc eget risus tristique, lobortis metus vitae, pellentesque leo. Vivamus placerat turpis ac dolor vehicula tincidunt. Sed venenatis, ante id ultrices convallis, lacus elit porttitor dolor, non porta risus ipsum ac justo. Integer id pretium quam, id placerat nulla.';
        $articleData->placement = Article::PLACEMENT_NONE;
        $this->createArticle($articleData, self::ARTICLE_PRIVACY_POLICY_2);

        $articleData->name = 'Informace o cookies';
        $articleData->text = 'Morbi posuere mauris dolor, quis accumsan dolor ullamcorper eget. Phasellus at elementum magna, et pretium neque. Praesent tristique lorem mi, eget varius quam aliquam eget. Vivamus ultrices interdum nisi, sed placerat lectus fermentum non. Phasellus ac quam vitae nisi aliquam vestibulum. Sed rhoncus tortor a arcu sagittis placerat. Nulla lectus nunc, ultrices ac faucibus sed, accumsan nec diam. Nam auctor neque quis tincidunt tempus. Nunc eget risus tristique, lobortis metus vitae, pellentesque leo. Vivamus placerat turpis ac dolor vehicula tincidunt. Sed venenatis, ante id ultrices convallis, lacus elit porttitor dolor, non porta risus ipsum ac justo. Integer id pretium quam, id placerat nulla.';
        $articleData->placement = Article::PLACEMENT_FOOTER;
        $this->createArticle($articleData, self::ARTICLE_COOKIES_2);

        $articleData->name = 'Kontakty';
        $articleData->text = 'Donec at dolor mi. Nullam ornare, massa in cursus imperdiet, felis nisl auctor ante, vel aliquet tortor lacus sit amet ipsum. Proin ultrices euismod elementum. Integer sodales hendrerit tortor, vel semper turpis interdum eu. Phasellus quam tortor, feugiat vel condimentum vel, tristique et ipsum. Duis blandit lectus in odio cursus rutrum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aliquam pulvinar massa at imperdiet venenatis. Maecenas convallis lobortis quam in fringilla. Mauris gravida turpis eget sapien imperdiet pulvinar. Nunc velit urna, fringilla nec est sit amet, accumsan varius nunc. Morbi sed tincidunt diam, sit amet laoreet nisl. Nulla tempus id lectus non lacinia.\n\nVestibulum interdum adipiscing iaculis. Nunc posuere pharetra velit. Nunc ac ante non massa scelerisque blandit sit amet vel velit. Integer in massa sed augue pulvinar malesuada. Pellentesque laoreet orci augue, in fermentum nisl feugiat ut. Nunc congue et nisi a interdum. Aenean mauris mi, interdum vel lacus et, placerat gravida augue. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed sagittis ipsum et consequat euismod. Praesent a ipsum dapibus, aliquet justo a, consectetur magna. Phasellus imperdiet tempor laoreet. Sed a accumsan lacus, accumsan faucibus dolor. Praesent euismod justo quis ipsum aliquam suscipit. Sed quis blandit urna.';
        $articleData->placement = Article::PLACEMENT_FOOTER;
        $this->createArticle($articleData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Article\ArticleData $articleData
     * @param string|null $referenceName
     */
    private function createArticle(ArticleData $articleData, $referenceName = null)
    {
        $article = $this->articleFacade->create($articleData);
        if ($referenceName !== null) {
            $this->addReference($referenceName, $article);
        }
    }
}
