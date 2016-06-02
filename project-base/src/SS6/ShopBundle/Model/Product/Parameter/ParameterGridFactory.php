<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\GridFactoryInterface;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Localization\Localization;

class ParameterGridFactory implements GridFactoryInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		Localization $localization
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->localization = $localization;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Grid\Grid
	 */
	public function create() {
		$locales = $this->localization->getAllLocales();
		$defaultLocale = $this->localization->getDefaultLocale();
		$grid = $this->gridFactory->create('parameterList', $this->getParametersDataSource());
		$grid->setDefaultOrder('pt.name');

		$grid->addColumn(
			'name',
			'pt.name',
			t('Název %locale%', ['%locale%' => $this->localization->getLanguageName($defaultLocale)]),
			true
		);
		foreach ($locales as $locale) {
			if ($locale !== $defaultLocale) {
				$grid->addColumn(
					'name_' . $locale,
					'pt_' . $locale . '.name',
					t('Název %locale%', ['%locale%' => $this->localization->getLanguageName($locale)]),
					true
				);
			}
		}
		$grid->addColumn('visible', 'p.visible', t('Filtrovat podle'), true);

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addDeleteActionColumn('admin_parameter_delete', ['id' => 'p.id'])
			->setConfirmMessage(t('Opravdu chcete odstranit tento parametr? '
				. 'Smazáním parametru dojde k odstranění tohoto parametru u zboží, kde je parametr přiřazen. '
				. 'Tento krok je nevratný!'));

		$grid->setTheme('@SS6Shop/Admin/Content/Parameter/listGrid.html.twig');

		return $grid;
	}

	/**
	 * @param array $locales
	 * @return QueryBuilderDataSource
	 */
	private function getParametersDataSource() {
		$locales = $this->localization->getAllLocales();
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('p, pt')
			->from(Parameter::class, 'p')
			->join('p.translations', 'pt', Join::WITH, 'pt.locale = :locale')
			->setParameter('locale', $this->localization->getDefaultLocale());

		foreach ($locales as $locale) {
			if ($locale !== $this->localization->getDefaultLocale()) {
				$queryBuilder
					->addSelect('pt_' . $locale)
					->leftJoin('p.translations', 'pt_' . $locale, Join::WITH, 'pt_' . $locale . '.locale = :locale_' . $locale)
					->setParameter('locale_' . $locale, $locale);
			}
		}

		return new QueryBuilderDataSource($queryBuilder, 'p.id');
	}
}
