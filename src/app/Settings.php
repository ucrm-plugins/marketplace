<?php /** @noinspection SpellCheckingInspection */
declare(strict_types=1);

namespace App;

use UCRM\Common\SettingsBase;

/**
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 * @method static string getUsername()
 * @method static string getPassword()
 */
final class Settings extends SettingsBase
{
	/** @const string The name of this Project, based on the root folder name. */
	public const PROJECT_NAME = 'marketplace';

	/** @const string The absolute path to this Project's root folder. */
	public const PROJECT_ROOT_PATH = 'C:\Users\rspaeth\Documents\PhpStorm\Projects\ucrm-plugins\marketplace';

	/** @const string The name of this Project, based on the root folder name. */
	public const PLUGIN_NAME = 'marketplace';

	/** @const string The absolute path to the root path of this project. */
	public const PLUGIN_ROOT_PATH = 'C:\Users\rspaeth\Documents\PhpStorm\Projects\ucrm-plugins\marketplace\src';

	/** @const string The absolute path to the data path of this project. */
	public const PLUGIN_DATA_PATH = 'C:\Users\rspaeth\Documents\PhpStorm\Projects\ucrm-plugins\marketplace\src\data';

	/** @const string The absolute path to the source path of this project. */
	public const PLUGIN_SOURCE_PATH = 'C:\Users\rspaeth\Documents\PhpStorm\Projects\ucrm-plugins\marketplace\src\src';

	/** @const string The publicly accessible URL of this UCRM, null if not configured in UCRM. */
	public const UCRM_PUBLIC_URL = 'https://ucrm.dev.mvqn.net/';

	/** @const string The locally accessible URL of this UCRM, null if not configured in UCRM. */
	public const UCRM_LOCAL_URL = 'https://localhost/';

	/** @const string The publicly accessible URL assigned to this Plugin by the UCRM. */
	public const PLUGIN_PUBLIC_URL = 'https://ucrm.dev.mvqn.net/_plugins/marketplace/public.php';

	/** @const string An automatically generated UCRM API 'App Key' with read/write access. */
	public const PLUGIN_APP_KEY = 'OLtYRUl4wjdhSCOf2kKrAeKJUb2nTAGRK+gj+6HIfkgSDcljImaPL4NYkd+yypGK';

	/**
	 * Marketplace Username
	 * @var string The username you used when registering for the Marketplace.
	 */
	protected static $username;

	/**
	 * Marketplace Password
	 * @var string The password you used when registering for the Marketplace.
	 */
	protected static $password;
}
