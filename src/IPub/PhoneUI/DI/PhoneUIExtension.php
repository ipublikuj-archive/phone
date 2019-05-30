<?php
/**
 * PhoneUIExtension.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:PhoneUI!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           30.05.19
 */

declare(strict_types = 1);

namespace IPub\PhoneUI\DI;

use Nette;
use Nette\Bridges;
use Nette\DI;
use Nette\PhpGenerator as Code;

use IPub\PhoneUI;

use libphonenumber;

/**
 * Phone UI extension container
 *
 * @package        iPublikuj:PhoneUI!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class PhoneUIExtension extends DI\CompilerExtension
{
	/**
	 * @return void
	 */
	public function loadConfiguration()
	{
		// Get container builder
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('phoneUI'))
			->setType(PhoneUI\PhoneUI::class)
			->setInject(FALSE);

		// Register template helpers
		$builder->addDefinition($this->prefix('helpers'))
			->setType(PhoneUI\Templating\Helpers::class)
			->setFactory($this->prefix('@phoneUI') . '::createTemplateHelpers')
			->setInject(FALSE);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile()
	{
		parent::beforeCompile();

		// Get container builder
		$builder = $this->getContainerBuilder();

		// Install extension latte macros
		$latteFactory = $builder->getDefinition($builder->getByType(Bridges\ApplicationLatte\ILatteFactory::class) ?: 'nette.latteFactory');

		$latteFactory
			->addSetup('IPub\PhoneUI\Latte\Macros::install(?->getCompiler())', ['@self'])
			->addSetup('addFilter', ['phone', [$this->prefix('@helpers'), 'phone']])
			->addSetup('addFilter', ['getPhoneNumberService', [$this->prefix('@helpers'), 'getPhoneNumberService']]);
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 *
	 * @return void
	 */
	public static function register(Nette\Configurator $config, string $extensionName = 'phoneUI')
	{
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new PhoneUIExtension);
		};
	}
}
