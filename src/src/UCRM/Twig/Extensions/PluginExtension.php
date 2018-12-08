<?php
declare(strict_types=1);
namespace UCRM\Twig\Extensions;

use MVQN\Common\Arrays;
use MVQN\Common\Strings;
use MVQN\Localization\Translator;
use Slim\App;
use Slim\Container;
use Slim\Router;
use UCRM\Common\Plugin;
use App\Settings;
use UCRM\Common\SettingsBase;

/**
 * Class Extension
 *
 * @package MVQN\Twig
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class PluginExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /** @var string */
    private $settings;

    public function __construct(string $settings)
    {
        $this->settings = $settings;
    }


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
            new \Twig_SimpleFilter("uncached", [$this, "uncached"]),
        ];
    }

    public function uncached(string $path)
    {
        $uncachedPath = "";

        if(Strings::contains($path, "?"))
        {
            $parts = explode("?", $path);

            parse_str($parts[1], $query);

            var_dump($query);

            $query["v"] = (new \DateTime())->getTimestamp();
            $queryParts = [];

            foreach($query as $key => $value)
                $queryParts[] = "$key=$value";

            $uncachedPath = $parts[0]."?".implode("&", $queryParts);
        }
        else
        {
            $uncachedPath = $path."?v=".(new \DateTime())->getTimestamp();
        }

        return $uncachedPath;
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

        if(!class_exists($this->settings) || !is_subclass_of($this->settings, SettingsBase::class, true))
            throw new \Exception("A valid Settings class was not found; was Plugin::createSettings() called first?");

        self::$globals["env"] = Plugin::environment();
        self::$globals["hostUrl"] = rtrim(constant("{$this->settings}::UCRM_PUBLIC_URL"), "/");
        self::$globals["baseUrl"] = "/_plugins/" . constant("{$this->settings}::PLUGIN_NAME") . "/public.php";
        self::$globals["homeRoute"] = "?/";
        self::$globals["locale"] = Translator::getCurrentLocale();
        self::$globals["pluginName"] = constant("{$this->settings}::PLUGIN_NAME");

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