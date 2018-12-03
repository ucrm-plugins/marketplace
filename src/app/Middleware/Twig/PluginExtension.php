<?php
declare(strict_types=1);
namespace App\Middleware\Twig;

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

    //protected $container;

    public function __construct(/*Container $container*/)
    {
        //$this->container = $container;
    }



    public function getName(): string
    {
        return "plugin";
    }

    public function getTokenParsers(): array
    {
        return [
            //new SwitchTokenParser(),
        ];
    }

    public function getFilters(): array
    {

        return [
            //new \Twig_SimpleFilter('without', [$this, 'withoutFilter']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction("link", [$this, "link"]),
        ];
    }


    public function link(string $path, bool $relative = true): string
    {
        $link = (!$relative ? self::$globals["hostUrl"].self::$globals["baseUrl"] : "public.php").
            ($path !== "/" ? "?$path" : "");

        return $link;
    }



    /** @var array */
    protected static $globals;



    public function getGlobals(): array
    {
        if(self::$globals === null)
        {
            self::$globals =
                [
                    "env" => Plugin::environment(),

                    "hostUrl" => rtrim(Settings::UCRM_PUBLIC_URL, "/"),
                    "baseUrl" => "/_plugins/" . Settings::PLUGIN_NAME . "/public.php",
                    //"baseUrl" => Settings::PLUGIN_PUBLIC_URL,
                    "homeRoute" => "?/",

                    "locale" => Translator::getCurrentLocale(),

                    "pluginName" => Settings::PLUGIN_NAME,

                ];
        }

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