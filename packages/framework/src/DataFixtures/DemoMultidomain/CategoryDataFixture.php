<?php

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\CategoryDataFixture as DemoCategoryDataFixture;
use Shopsys\FrameworkBundle\Model\Category\CategoryDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;

class CategoryDataFixture extends AbstractReferenceFixture
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryDataFactoryInterface
     */
    private $categoryDataFacade;

    public function __construct(
        CategoryFacade $categoryFacade,
        CategoryDataFactoryInterface $categoryDataFacade
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->categoryDataFacade = $categoryDataFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::CATEGORY_ELECTRONICS,
            'Spotřební elektronika zahrnuje elektronická zařízení každodenního (nebo alespoň častého)'
            . ' použití pro komunikaci, v kanceláři i doma.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::CATEGORY_TV,
            'Televize (z řeckého tele – daleko a latinského vize – vidět) je široce používané'
            . ' jednosměrné dálkové plošné vysílání (tzv. broadcasting) a individuální přijímání televizního vysílání'
            . ' – obrazu a zvuku do televizoru. Výrazně přispívá k celkové socializaci lidí takřka po celém světě.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::CATEGORY_PHOTO,
            'Fotoaparát je zařízení sloužící k pořizování a zaznamenání fotografií. Každý fotoaparát'
            . ' je v principu světlotěsně uzavřená komora s malým otvorem (nebo nějakou složitější optickou soustavou –'
            . ' objektivem), jímž dovnitř vstupuje světlo, a nějakým druhem světlocitlivé záznamové vrstvy na druhé straně'
            . ', na níž dopadající světlo kreslí obraz.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::CATEGORY_PRINTERS,
            'Tiskárna je periferní výstupní zařízení, které slouží k přenosu dat uložených v'
            . ' elektronické podobě na papír nebo jiné médium (fotopapír, kompaktní disk apod.). Tiskárnu připojujeme'
            . ' k počítači, ale může fungovat i samostatně.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::CATEGORY_PC,
            'Počítač je zařízení a výpočetní technika, která zpracovává data pomocí předem'
            . ' vytvořeného programu. Současný počítač je elektronický a skládá se z hardwaru, který představuje fyzické'
            . ' části počítače (mikroprocesor, klávesnice, monitor atd.) a ze softwaru (operační systém a programy). Počítač'
            . ' je zpravidla ovládán uživatelem, který poskytuje počítači data ke zpracování prostřednictvím jeho vstupních'
            . ' zařízení a počítač výsledky prezentuje pomocí výstupních zařízení. V současnosti jsou počítače využívány'
            . ' téměř ve všech oborech lidské činnosti.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::CATEGORY_PHONES,
            'Mobilní telefony umožňují nejen komunikaci v rámci mobilní sítě, ale i spojení s pevnou'
            . ' telefonní sítí přímo volbou telefonního čísla na vestavěné klávesnici a poskytují širokou škálu dalších'
            . ' telekomunikačních služeb, jako jsou SMS, MMS, WAP a připojení na Internet. Protože patří k nejrozšířenějším'
            . ' elektronickým zařízením, výrobci je vybavují také dalšími funkcemi.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::CATEGORY_COFFEE,
            'Kávovar je stroj určený pro výrobu kávy takovým způsobem, aby se voda nemusela vařit'
            . ' v oddělené nádobě. Existuje obrovské množství kávovarů, nicméně princip přípravy kávy je vždy stejný: do'
            . ' kovového či papírového filtru se vloží rozemletá káva. Filtr s kávou se vloží do kávovaru, kde se přes něj'
            . ' (většinou pod tlakem) nechá přetéct horká voda vytékající do připravené nádoby na kávu (hrnek, sklenici apod.).'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::CATEGORY_BOOKS,
            'Kniha je sešitý nebo slepený svazek listů nebo skládaný arch papíru, kartonu, pergamenu'
            . ' nebo jiného materiálu, popsaný, potištěný nebo prázdný s vazbou a opatřený přebalem.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::CATEGORY_TOYS,
            'Hračka je předmět používaný ke hře dětí, ale někdy i dospělých. Slouží k upoutání'
            . ' pozornosti dítěte, jeho zabavení, ale také k rozvíjení jeho motorických a psychických schopností. Hračky'
            . ' existují již po tisíce let. Panenky, zvířátka, vojáčci a miniatury nástrojů dospělých jsou nacházeny'
            . ' v archeologických vykopávkách od nepaměti.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::CATEGORY_GARDEN_TOOLS,
            'Oddělení zahradního náčiní je jedno z největších oddělení v naší nabídce. Za pozornost'
            . ' stojí zejména naše filtry různých druhů nečistot, avšak doporučujeme popatřit zrakem i na naše boční držáky'
            . ' plechů.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::CATEGORY_FOOD,
            'Potravina je výrobek nebo látka určená pro výživu lidí a konzumovaná ústy v nezměněném'
            . ' nebo upraveném stavu. Potraviny se dělí na poživatiny a pochutiny. Potraviny mohou být rostlinného, živočišného'
            . ' nebo jiného původu.'
        );
    }

    /**
     * @param string $referenceName
     * @param string|null $descriptionDomain2
     */
    private function editCategoryOnDomain2($referenceName, $descriptionDomain2)
    {
        $category = $this->getReference($referenceName);
        /* @var $category \Shopsys\FrameworkBundle\Model\Category\Category */
        $categoryData = $this->categoryDataFacade->createFromCategory($category);
        $domainId = 2;
        $categoryData->descriptions[$domainId] = $descriptionDomain2;
        $this->categoryFacade->edit($category->getId(), $categoryData);
    }
}
