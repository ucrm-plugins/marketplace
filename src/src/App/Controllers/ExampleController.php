<?php
declare(strict_types=1);

namespace App\Controllers;

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

final class ExampleController
{

    public function __construct(App $app)
    {
        $container = $app->getContainer();

        $app->get("/example",
            function (Request $request, Response $response, array $args) use ($container)
            {
                return $response->write("This is an example route!");
            }
        );


    }


}