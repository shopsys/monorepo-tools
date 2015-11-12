<?php

namespace SS6\ShopBundle\Command;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Component\Setting\SettingValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig_Environment;

class GenerateGruntfileCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('ss6:generate:gruntfile')
			->setDescription('Generate Gruntfile.js by domain settings');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */
		$twig = $this->getContainer()->get(Twig_Environment::class);
		/* @var $twig \Twig_Environment */
		$setting = $this->getContainer()->get(Setting::class);
		/* @var $setting \SS6\ShopBundle\Component\Setting\Setting */

		$cssVersion = time();
		$setting->set(Setting::CSS_VERSION, $cssVersion, SettingValue::DOMAIN_ID_COMMON);

		$output->writeln('Start of generating Gruntfile.js.');
		$gruntfileContents = $twig->render('@SS6Shop/Grunt/gruntfile.js.twig', [
			'domains' => $domain->getAll(),
			'rootStylesDirectory' => $this->getContainer()->getParameter('ss6.styles_dir'),
			'cssVersion' => $cssVersion,
		]);
		$path = $this->getContainer()->getParameter('ss6.root_dir');
		file_put_contents($path . '/Gruntfile.js', $gruntfileContents);
		$output->writeln('<fg=green>Gruntfile.js was successfully generated.</fg=green>');
	}

}
