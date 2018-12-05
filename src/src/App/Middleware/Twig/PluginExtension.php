<?php
declare(strict_types=1);
namespace App\Middleware\Twig;

use MVQN\Common\Arrays;
use MVQN\Localization\Translator;
use Slim\App;
use Slim\Container;
use Slim\Router;
use UCRM\Common\Plugin;
use App\Settings;

/**
 * Class Extension
 *
 * @package MVQN\Twig
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class PluginExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return "plugin";
    }

    /**
     * @return array
     */
    public function getTokenParsers(): array
    {
        return [
            //new SwitchTokenParser(),
        ];
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {

        return [
            //new \Twig_SimpleFilter('without', [$this, 'withoutFilter']),
        ];
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction("link", [$this, "link"]),
        ];
    }

    /**
     * @param string $path
     * @param array $query
     * @param bool $relative
     * @return string
     */
    public function link(string $path, array $query = [], bool $relative = true): string
    {
        if(!$relative && self::$globals === null)
            self::getGlobals();

        //var_dump(self::$globals);

        $link = (!$relative ? self::$globals["hostUrl"].self::$globals["baseUrl"] : "public.php").
            ($path !== "/" ? "?$path" : "");

        if ($query !== null && $query !== [])
        {
            $queryString = "";

            if (Arrays::is_assoc($query))
                $queryString = http_build_query($query);
            else
                $queryString = implode("&", $query);

            $queryString = htmlspecialchars($queryString);
            $link .= "&$queryString";
        }

        return $link;
    }



    /** @var array */
    protected static $globals;

    public function getGlobals(): array
    {
        self::$globals["env"] = Plugin::environment();
        self::$globals["hostUrl"] = rtrim(Settings::UCRM_PUBLIC_URL, "/");
        self::$globals["baseUrl"] = "/_plugins/" . Settings::PLUGIN_NAME . "/public.php";
        self::$globals["homeRoute"] = "?/";
        self::$globals["locale"] = Translator::getCurrentLocale();
        self::$globals["pluginName"] = Settings::PLUGIN_NAME;

        return [
            "app" => self::$globals,
        ];
    }

    public static function setGlobal(string $name, $value)
    {
        if(self::$globals === null)
            self::$globals = [ $name => $value ];

        self::$globals[$name] = $value;
    }

}