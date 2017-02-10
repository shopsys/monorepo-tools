<?php

namespace Shopsys\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture as DemoCategoryDataFixture;
use Shopsys\ShopBundle\Model\Category\CategoryData;
use Shopsys\ShopBundle\Model\Category\CategoryDataFactory;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;

class CategoryDataFixture extends AbstractReferenceFixture {

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function load(ObjectManager $manager) {
        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::ELECTRONICS,
            'Electronics',
            'Our electronics include devices used for entertainment (flat screen TVs, DVD players, DVD movies, iPods, '
            . 'video games, remote control cars, etc.), communications (telephones, cell phones, e-mail-capable laptops, etc.) '
            . 'and home office activities (e.g., desktop computers, printers, paper shredders, etc.).'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::TV,
            'TV, audio',
            'Television or TV is a telecommunication medium used for transmitting sound with moving images in monochrome '
            . '(black-and-white), or in color, and in two or three dimensions'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::PHOTO,
            'Cameras & Photo',
            'A camera is an optical instrument for recording or capturing images, which may be stored locally, '
            . 'transmitted to another location, or both.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::PRINTERS,
            null,
            'A printer is a peripheral which makes a persistent human readable representation of graphics or text on paper '
            . 'or similar physical media.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::PC,
            null,
            'A personal computer (PC) is a general-purpose computer whose size, capabilities, and original sale price '
            . 'make it useful for individuals, and is intended to be operated directly by an end-user with no intervening computer '
            . 'time-sharing models that allowed larger, more expensive minicomputer and mainframe systems to be used by many people, '
            . 'usually at the same time.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::PHONES,
            null,
            'A telephone is a telecommunications device that permits two or more users to conduct a conversation when they are '
            . 'too far apart to be heard directly. A telephone converts sound, typically and most efficiently the human voice, '
            . 'into electronic signals suitable for transmission via cables or other transmission media over long distances, '
            . 'and replays such signals simultaneously in audible form to its user.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::COFFEE,
            null,
            'Coffeemakers or coffee machines are cooking appliances used to brew coffee. While there are many different types '
            . 'of coffeemakers using a number of different brewing principles, in the most common devices, coffee grounds '
            . 'are placed in a paper or metal filter inside a funnel, which is set over a glass or ceramic coffee pot, '
            . 'a cooking pot in the kettle family. Cold water is poured into a separate chamber, which is then heated up to the '
            . 'boiling point, and directed into the funnel.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::BOOKS,
            'Books',
            'A book is a set of written, printed, illustrated, or blank sheets, made of ink, paper, parchment, or other '
            . 'materials, fastened together to hinge at one side. A single sheet within a book is a leaf, and each side of a leaf '
            . 'is a page. A set of text-filled or illustrated pages produced in electronic format is known as an electronic book, '
            . 'or e-book.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::TOYS,
            null,
            'A toy is an item that can be used for play. Toys are generally played with by children and pets. '
            . 'Playing with toys is an enjoyable means of training young children for life in society. Different materials are '
            . 'used to make toys enjoyable to all ages. '
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::GARDEN_TOOLS,
            'Garden tools',
            'A garden tool is any one of many tools made for gardens and gardening and overlaps with the range of tools '
            . 'made for agriculture and horticulture. Garden tools can also be hand tools and power tools.'
        );

        $this->editCategoryOnDomain2(
            DemoCategoryDataFixture::FOOD,
            'Food',
            'Food is any substance consumed to provide nutritional support for the body. It is usually of plant or '
            . 'animal origin, and contains essential nutrients, such as fats, proteins, vitamins, or minerals. The substance '
            . 'is ingested by an organism and assimilated by the organism\'s cells to provide energy, maintain life, '
            . 'or stimulate growth.'
        );
    }

    /**
     * @param string $referenceName
     * @param string|null $nameEn
     * @param string|null $descriptionDomain2
     */
    private function editCategoryOnDomain2($referenceName, $nameEn, $descriptionDomain2) {
        $categoryFacade = $this->get(CategoryFacade::class);
        /* @var $categoryFacade \Shopsys\ShopBundle\Model\Category\CategoryFacade */
        $categoryDataFactory = $this->get(CategoryDataFactory::class);
        /* @var $categoryDataFactory \Shopsys\ShopBundle\Model\Category\CategoryDataFactory */

        $category = $this->getReference(DemoCategoryDataFixture::PREFIX . $referenceName);
        /* @var $category \Shopsys\ShopBundle\Model\Category\Category */
        $categoryData = $categoryDataFactory->createFromCategory($category);
        $categoryData->name['en'] = $nameEn;
        $domainId = 2;
        $categoryData->descriptions[$domainId] = $descriptionDomain2;
        $categoryFacade->edit($category->getId(), $categoryData);
    }

}
