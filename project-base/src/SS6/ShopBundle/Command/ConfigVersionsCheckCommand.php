<?php

namespace SS6\ShopBundle\Command;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigVersionsCheckCommand {

	const VERSION_LABEL_IN_CONFIG = 'config_version';
	const ROOT_SETTING_VALUE = null;

	/**
	 * @var array
	 */
	private $containerParametersConfigsFilePaths = [
			'parameters.yml' => 'parameters',
			'parameters_test.yml' => 'parameters',
		];

	/**
	 * @var array
	 */
	private $errors;

	/**
	 * @var \Symfony\Component\Console\Output\OutputInterface
	 */
	private $output;

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	public function __construct(OutputInterface $output) {
		$this->output = $output;
	}

	/**
	 * @return int|null
	 */
	public function check() {
		$this->output->writeln('Start checking configs.');
		$this->errors = [];

		$this->processConfigVersion('domains.yml', self::ROOT_SETTING_VALUE);

		foreach ($this->containerParametersConfigsFilePaths as $configFilename => $rootParameter) {
			$this->processConfigVersion($configFilename, $rootParameter);
		}

		if (count($this->errors) > 0) {
			$this->output->writeln('<fg=red>' . implode(PHP_EOL, $this->errors) . '</fg=red>');
			return 1;
		} else {
			$this->output->writeln('<fg=green>All configs are actual.</fg=green>');
		}
	}

	/**
	 * @param string $configFilename
	 * @param string $rootParameter
	 */
	private function processConfigVersion($configFilename, $rootParameter) {
		$configFilepath = __DIR__ . '/../../../../app/config/' . $configFilename;
		$distConfigVersion = $this->getConfigVersion($configFilepath. '.dist', $rootParameter);
		$configVersion = $this->getConfigVersion($configFilepath, $rootParameter);
		if ($configVersion != $distConfigVersion) {
			$this->errors[] = 'Your config ' . $configFilename . ' has wrong version, please check it.';
		}
	}

	/**
	 * @param string $filename
	 * @param string $parent
	 * @return int
	 */
	private function getConfigVersion($filename, $parent) {
		$yamlParser = new Parser();
		$version = 0;
		$filename = realpath($filename);

		if (!file_exists($filename)) {
			$this->errors[] = 'File ' . $filename . ' does not exist';
			return $version;
		}

		$parsedConfig = $yamlParser->parse(file_get_contents($filename));
		$root = null;
		if ($parent === self::ROOT_SETTING_VALUE) {
			$root = $parsedConfig;
		} elseif (array_key_exists($parent, $parsedConfig)) {
			$root = $parsedConfig[$parent];
		}

		if ($root !== null && array_key_exists(self::VERSION_LABEL_IN_CONFIG, $root)) {
			$version = $root[self::VERSION_LABEL_IN_CONFIG];
		} else {
			$this->errors[] = 'In config ' . $filename . ' isn\'t set config version.';
		}
		return $version;
	}
}
