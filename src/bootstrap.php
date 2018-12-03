<?php
declare(strict_types=1);
require_once __DIR__."/vendor/autoload.php";

use MVQN\Localization\Translator;
use MVQN\Localization\Exceptions\TranslatorException;

use MVQN\REST\RestClient;

use UCRM\Common\Log;
use UCRM\Common\Plugin;

use UCRM\Common\Config;

use App\Settings;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

use Slim\Http\Environment;

/**
 * bootstrap.php
 *
 * A common configuration and initialization file.
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */

// =====================================================================================================================
// PLUGIN SETTINGS
// =====================================================================================================================

// Initialize the Plugin libraries using this directory as the plugin root!
Plugin::initialize(__DIR__);

// Regenerate the Settings class, in case anything has changed in the manifest.json file.
Plugin::createSettings("App", "Settings", __DIR__);

// =====================================================================================================================
// REST CLIENT
// =====================================================================================================================

if(file_exists(__DIR__."/../.env"))
{
    (new \Dotenv\Dotenv(__DIR__."/../"))->load();
}

// Generate the REST API URL from either an ENV variable (including from .env file),  or fallback to localhost.
$restUrl = rtrim(getenv("UCRM_REST_URL") ?: Settings::UCRM_LOCAL_URL ?: "https://localhost/", "/")."/api/v1.0";

//echo $restUrl;

// Configure the REST Client...
RestClient::setBaseUrl($restUrl); //Settings::UCRM_PUBLIC_URL . "api/v1.0");
RestClient::setHeaders([
    "Content-Type: application/json",
    "X-Auth-App-Key: " . Settings::PLUGIN_APP_KEY
]);

// =====================================================================================================================
// LOCALIZATION
// =====================================================================================================================

// Set the dictionary directory and "default" locale.
try
{
    Translator::setDictionaryDirectory(__DIR__ . "/translations/");
    Translator::setCurrentLocale(str_replace("_", "-", Config::getLanguage()) ?: "en-US", true);
}
catch (TranslatorException $e)
{
    Log::http("No dictionary could be found!", 500);
}

// =====================================================================================================================
// ROUTING (SLIM)
// =====================================================================================================================

// Create Slim Framework Application, given the provided settings.
$app = new \Slim\App([
    "settings" => [
        "displayErrorDetails" => true,
        "addContentLengthHeader" => false,
        "determineRouteBeforeAppMiddleware" => true,
    ],
]);

// Get a reference to the DI Container included with the Slim Framework.
$container = $app->getContainer();

// =====================================================================================================================
// RENDERING (TWIG)
// =====================================================================================================================

// Configure Twig Renderer
$container["twig"] = function (Container $container)
{
    $twig = new \Slim\Views\Twig(
        [
            //__DIR__ . "/www/",
            __DIR__ . "/app/Views/",

        ],
        [
            //'cache' => 'path/to/cache'
            "debug" => true,
        ]
    );

    // Instantiate and add Slim specific extension
    $router = $container->get("router");
    $uri = \Slim\Http\Uri::createFromEnvironment(new Environment($_SERVER));

    //$query = $uri->getQuery();
    //var_dump($query);

    //$route = \App\Middleware\QueryStringRouter::extractRouteFromQueryString($query);

    //$uri = $uri
    //    ->withPath($route)
    //    ->withQuery($query);

    //var_dump($uri);
    $twig->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    $twig->addExtension(new Twig_Extension_Debug());

    $twig->addExtension(new \MVQN\Twig\Extensions\SwitchExtension());
    $twig->addExtension(new \App\Middleware\Twig\PluginExtension($container));

    return $twig;
};

// ---------------------------------------------------------------------------------------------------------------------

// Override the default 404 Page!
$container['notFoundHandler'] = function (Container $container)
{
    return function(Request $request, Response $response) use ($container): Response
    {
        /** @var \Slim\Router $router */
        $router = $container->get("router");

        $data = [
            "vRoute" => $request->getAttribute("vRoute"),
            "router" => $router,
        ];

        return $container->twig->render($response,"404.html.twig", $data);
    };
};

// =====================================================================================================================
// LOGGING (MONOLOG)
// =====================================================================================================================

// Configure MonoLog
$container['logger'] = function (\Slim\Container $container)
{
    $logger = new Monolog\Logger("template-plugin");
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler(
        PHP_SAPI === "cli-server" ? "php://stdout" : __DIR__ . "/logs/www.log",
        \Monolog\Logger::DEBUG
    ));
    return $logger;
};



// Applied in Ascending order, bottom up!
//$www->add(new \UCRM\Routing\Middleware\PluginAuthentication());
$app->add(new \App\Middleware\QueryStringRouter());

