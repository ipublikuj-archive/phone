<?php
/**
 * Macros.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:PhoneUI!
 * @subpackage     Latte
 * @since          1.0.0
 *
 * @date           12.12.15
 */

declare(strict_types = 1);

namespace IPub\PhoneUI\Latte;

use Nette;

use Latte;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\PhpWriter;
use Latte\Macros\MacroSet;

use IPub\Phone;

/**
 * Phone UI number Latte macros
 *
 * @package        iPublikuj:PhoneUI!
 * @subpackage     Latte
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class Macros extends MacroSet
{
	/**
	 * Register latte macros
	 *
	 * @param Compiler $compiler
	 *
	 * @return static
	 */
	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);

		/**
		 * {phone $phoneNumber[, $country, $format]}
		 */
		$me->addMacro('phone', [$me, 'macroPhone']);

		return $me;
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 *
	 * @throws Latte\CompileException
	 */
	public function macroPhone(MacroNode $node, PhpWriter $writer)
	{
		$arguments = self::prepareMacroArguments($node->args);

		if ($arguments['phone'] === NULL) {
			throw new Latte\CompileException('Please provide phone number.');
		}

		return $writer->write('echo %escape(property_exists($this, "filters") ? call_user_func($this->filters->phone, "' . $arguments['phone'] . '", "' . $arguments['country'] . '", ' . $arguments['format'] . ') : $template->getPhoneNumberService()->format("' . $arguments['phone'] . '", "' . $arguments['country'] . '", ' . $arguments['format'] . '));');
	}

	/**
	 * @param string $macro
	 *
	 * @return array
	 */
	public static function prepareMacroArguments(string $macro) : array
	{
		$arguments = array_map(function ($value) {
			return trim(trim($value), '\'"');
		}, explode(",", $macro));

		$phone = $arguments[0];
		$country = (isset($arguments[1]) && !empty($arguments[1])) ? strtoupper($arguments[1]) : NULL;
		$format = (isset($arguments[2]) && !empty($arguments[2])) ? $arguments[2] : Phone\Phone::FORMAT_INTERNATIONAL;

		if (!self::isPhoneCountry($country)) {
			$format = (int) $country;
			$country = 'AUTO';
		}

		if ($country === NULL) {
			$country = 'AUTO';
		}

		return [
			'phone'   => (string) $phone,
			'country' => (string) $country,
			'format'  => (int) $format,
		];
	}

	/**
	 * Checks if the supplied string is a valid country code using some arbitrary country validation
	 *
	 * @param string $country
	 *
	 * @return bool
	 */
	protected static function isPhoneCountry(string $country) : bool
	{
		return (strlen($country) === 2 && ctype_alpha($country) && ctype_upper($country));
	}
}
