<?php
declare(strict_types=1);
require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/bootstrap.php";

use App\Controllers;

/**
 * Use an immediately invoked function here, to avoid global namespace pollution...
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 */
(function() use ($app)
{
    define("ASSET_PATH", realpath(__DIR__."/public/")); // TODO: Move to public/
    define("VIEWS_PATH", realpath(__DIR__."/src/App/Views/"));


    // -----------------------------------------------------------------------------------------------------------------
    // CUSTOM ROUTES
    // -----------------------------------------------------------------------------------------------------------------

    new Controllers\ExampleController($app);

    // TODO: Add additional custom routes here...

    // -----------------------------------------------------------------------------------------------------------------
    // BUILD-IN ROUTES
    // -----------------------------------------------------------------------------------------------------------------

    new Controllers\Common\AssetController($app);
    new Controllers\Common\TemplateController($app);
    new Controllers\Common\ScriptController($app);

    // Run the Slim Framework Application!
    $app->run();

})();

