<?php
declare(strict_types=1);

namespace App\Controllers;

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ExampleController
 *
 * An example controller.
 *
 * @package App\Controllers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class ExampleController
{
    /**
     * ExampleController constructor.
     *
     * @param App $app The Slim Application for which to configure routing.
     */
    public function __construct(App $app)
    {
        // Get a local reference to the Slim Application's DI Container.
        $container = $app->getContainer();

        $app->get("/example",

            function (Request $request, Response $response, array $args) use ($container)
            {
                return $response->write("This is an example route!");
            }
        );
    }

}