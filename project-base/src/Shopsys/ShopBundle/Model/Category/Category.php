<?php

namespace Shopsys\ShopBundle\Model\Category;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Shopsys\ShopBundle\Model\Localization\AbstractTranslatableEntity;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="categories")
 * @ORM\Entity
 */
class Category extends AbstractTranslatableEntity {

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryTranslation[]
     *
     * @Prezent\Translations(targetEntity="Shopsys\ShopBundle\Model\Category\CategoryTranslation")
     */
    protected $translations;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\Category|null
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Category\Category", inversedBy="children")
     * @ORM\JoinColumn(nullable=true, name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\Category[]
     *
     * @ORM\OneToMany(targetEntity="Shopsys\ShopBundle\Model\Category\Category", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @var int
     *
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    private $level;

    /**
     * @var int
     *
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    private $lft;

    /**
     * @var int
     *
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    private $rgt;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryDomain[]|\Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Shopsys\ShopBundle\Model\Category\CategoryDomain", mappedBy="category", fetch="EXTRA_LAZY")
     */
    private $domains;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\Category\FeedCategory|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Feed\Category\FeedCategory")
     * @ORM\JoinColumn(nullable=true)
     */
    private $heurekaCzFeedCategory;

    /**
     * @param \Shopsys\ShopBundle\Model\Category\CategoryData $categoryData
     */
    public function __construct(CategoryData $categoryData) {
        $this->setParent($categoryData->parent);
        $this->translations = new ArrayCollection();
        $this->setTranslations($categoryData);
        $this->heurekaCzFeedCategory = $categoryData->heurekaCzFeedCategory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\CategoryData $categoryData
     */
    public function edit(CategoryData $categoryData) {
        $this->setParent($categoryData->parent);
        $this->setTranslations($categoryData);
        $this->heurekaCzFeedCategory = $categoryData->heurekaCzFeedCategory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category|null $parent
     */
    public function setParent(Category $parent = null) {
        $this->parent = $parent;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string|null $locale
     * @return string
     */
    public function getName($locale = null) {
        return $this->translation($locale)->getName();
    }

    /**
     * @return string[locale]
     */
    public function getNames() {
        $names = [];
        foreach ($this->translations as $translation) {
            $names[$translation->getLocale()] = $translation->getName();
        }

        return $names;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Category\Category|null
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @return int
     */
    public function getLevel() {
        return $this->level;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * @return int
     */
    public function getLft() {
        return $this->lft;
    }

    /**
     * @return int
     */
    public function getRgt() {
        return $this->rgt;
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Category\CategoryDomain
     */
    public function getCategoryDomain($domainId) {
        foreach ($this->domains as $categoryDomain) {
            if ($categoryDomain->getDomainId() === $domainId) {
                return $categoryDomain;
            }
        }

        throw new \Shopsys\ShopBundle\Model\Category\Exception\CategoryDomainNotFoundException($this->id, $domainId);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Feed\Category\FeedCategory|null
     */
    public function getHeurekaCzFeedCategory() {
        return $this->heurekaCzFeedCategory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\CategoryData $categoryData
     */
    private function setTranslations(CategoryData $categoryData) {
        foreach ($categoryData->name as $locale => $name) {
            $this->translation($locale)->setName($name);
        }
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Category\CategoryTranslation
     */
    protected function createTranslation() {
        return new CategoryTranslation();
    }

}
