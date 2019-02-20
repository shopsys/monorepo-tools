<?php

declare(strict_types=1);

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Category\CategoryDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture as DemoCategoryDataFixture;

class MultidomainCategoryDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade
     */
    protected $categoryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryDataFactoryInterface
     */
    protected $categoryDataFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFacade $categoryFacade
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryDataFactoryInterface $categoryDataFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        CategoryFacade $categoryFacade,
        CategoryDataFactoryInterface $categoryDataFacade,
        Domain $domain
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->categoryDataFacade = $categoryDataFacade;
        $this->domain = $domain;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->domain->getAllIdsExcludingFirstDomain() as $domainId) {
            $this->loadForDomain($domainId);
        }
    }

    /**
     * @param int $domainId
     */
    protected function loadForDomain(int $domainId)
    {
        $this->editCategoryOnDomain(
            DemoCategoryDataFixture::CATEGORY_ELECTRONICS,
            $domainId,
            'Spotřební elektronika zahrnuje elektronická zařízení každodenního (nebo alespoň častého)'
            . ' použití pro komunikaci, v kanceláři i doma.'
        );

        $this->editCategoryOnDomain(
            DemoCategoryDataFixture::CATEGORY_TV,
            $domainId,
            'Televize (z řeckého tele – daleko a latinského vize – vidět) je široce používané'
            . ' jednosměrné dálkové plošné vysílání (tzv. broadcasting) a individuální přijímání televizního vysílání'
            . ' – obrazu a zvuku do televizoru. Výrazně přispívá k celkové socializaci lidí takřka po celém světě.'
        );

        $this->editCategoryOnDomain(
            DemoCategoryDataFixture::CATEGORY_PHOTO,
            $domainId,
            'Fotoaparát je zařízení sloužící k pořizování a zaznamenání fotografií. Každý fotoaparát'
            . ' je v principu světlotěsně uzavřená komora s malým otvorem (nebo nějakou složitější optickou soustavou –'
            . ' objektivem), jímž dovnitř vstupuje světlo, a nějakým druhem světlocitlivé záznamové vrstvy na druhé straně'
            . ', na níž dopadající světlo kreslí obraz.'
        );

        $this->editCategoryOnDomain(
            DemoCategoryDataFixture::CATEGORY_PRINTERS,
            $domainId,
            'Tiskárna je periferní výstupní zařízení, které slouží k přenosu dat uložených v'
            . ' elektronické podobě na papír nebo jiné médium (fotopapír, kompaktní disk apod.). Tiskárnu připojujeme'
            . ' k počítači, ale může fungovat i samostatně.'
        );

        $this->editCategoryOnDomain(
            DemoCategoryDataFixture::CATEGORY_PC,
            $domainId,
            'Počítač je zařízení a výpočetní technika, která zpracovává data pomocí předem'
            . ' vytvořeného programu. Současný počítač je elektronický a skládá se z hardwaru, který představuje fyzické'
            . ' části počítače (mikroprocesor, klávesnice, monitor atd.) a ze softwaru (operační systém a programy). Počítač'
            . ' je zpravidla ovládán uživatelem, který poskytuje počítači data ke zpracování prostřednictvím jeho vstupních'
            . ' zařízení a počítač výsledky prezentuje pomocí výstupních zařízení. V současnosti jsou počítače využívány'
            . ' téměř ve všech oborech lidské činnosti.'
        );

        $this->editCategoryOnDomain(
            DemoCategoryDataFixture::CATEGORY_PHONES,
            $domainId,
            'Mobilní telefony umožňují nejen komunikaci v rámci mobilní sítě, ale i spojení s pevnou'
            . ' telefonní sítí přímo volbou telefonního čísla na vestavěné klávesnici a poskytují širokou škálu dalších'
            . ' telekomunikačních služeb, jako jsou SMS, MMS, WAP a připojení na Internet. Protože patří k nejrozšířenějším'
            . ' elektronickým zařízením, výrobci je vybavují také dalšími funkcemi.'
        );

        $this->editCategoryOnDomain(
            DemoCategoryDataFixture::CATEGORY_COFFEE,
            $domainId,
            'Kávovar je stroj určený pro výrobu kávy takovým způsobem, aby se voda nemusela vařit'
            . ' v oddělené nádobě. Existuje obrovské množství kávovarů, nicméně princip přípravy kávy je vždy stejný: do'
            . ' kovového či papírového filtru se vloží rozemletá káva. Filtr s kávou se vloží do kávovaru, kde se přes něj'
            . ' (většinou pod tlakem) nechá přetéct horká voda vytékající do připravené nádoby na kávu (hrnek, sklenici apod.).'
        );

        $this->editCategoryOnDomain(
            DemoCategoryDataFixture::CATEGORY_BOOKS,
            $domainId,
            'Kniha je sešitý nebo slepený svazek listů nebo skládaný arch papíru, kartonu, pergamenu'
            . ' nebo jiného materiálu, popsaný, potištěný nebo prázdný s vazbou a opatřený přebalem.'
        );

        $this->editCategoryOnDomain(
            DemoCategoryDataFixture::CATEGORY_TOYS,
            $domainId,
            'Hračka je předmět používaný ke hře dětí, ale někdy i dospělých. Slouží k upoutání'
            . ' pozornosti dítěte, jeho zabavení, ale také k rozvíjení jeho motorických a psychických schopností. Hračky'
            . ' existují již po tisíce let. Panenky, zvířátka, vojáčci a miniatury nástrojů dospělých jsou nacházeny'
            . ' v archeologických vykopávkách od nepaměti.'
        );

        $this->editCategoryOnDomain(
            DemoCategoryDataFixture::CATEGORY_GARDEN_TOOLS,
            $domainId,
            'Oddělení zahradního náčiní je jedno z největších oddělení v naší nabídce. Za pozornost'
            . ' stojí zejména naše filtry různých druhů nečistot, avšak doporučujeme popatřit zrakem i na naše boční držáky'
            . ' plechů.'
        );

        $this->editCategoryOnDomain(
            DemoCategoryDataFixture::CATEGORY_FOOD,
            $domainId,
            'Potravina je výrobek nebo látka určená pro výživu lidí a konzumovaná ústy v nezměněném'
            . ' nebo upraveném stavu. Potraviny se dělí na poživatiny a pochutiny. Potraviny mohou být rostlinného, živočišného'
            . ' nebo jiného původu.'
        );
    }

    /**
     * @param string $referenceName
     * @param int $domainId
     * @param string $description
     */
    protected function editCategoryOnDomain(string $referenceName, int $domainId, string $description)
    {
        $category = $this->getReference($referenceName);
        /* @var $category \Shopsys\FrameworkBundle\Model\Category\Category */
        $categoryData = $this->categoryDataFacade->createFromCategory($category);
        $categoryData->descriptions[$domainId] = $description;
        $this->categoryFacade->edit($category->getId(), $categoryData);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            CategoryDataFixture::class,
        ];
    }
}
