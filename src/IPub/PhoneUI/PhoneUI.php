<?php
/**
 * Phone.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:PhoneUI!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           12.12.15
 */

declare(strict_types = 1);

namespace IPub\PhoneUI;

use Nette;

use IPub\Phone;

/**
 * Phone number UI helpers
 *
 * @package        iPublikuj:PhoneUI!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class PhoneUI
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var Phone\Phone
	 */
	private $phone;

	/**
	 * @param Phone\Phone $phone
	 */
	public function __construct(Phone\Phone $phone)
	{
		$this->phone = $phone;
	}

	/**
	 * @return Templating\Helpers
	 */
	public function createTemplateHelpers()
	{
		return new Templating\Helpers($this->phone);
	}
}
