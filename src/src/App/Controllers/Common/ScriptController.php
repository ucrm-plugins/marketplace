<?php
declare(strict_types=1);

namespace App\Controllers\Common;

use App\Middleware\PluginAuthentication;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;


final class ScriptController
{

    public function __construct(App $app)
    {
        $container = $app->getContainer();

        $app->map([ "GET", "POST" ], "/{file:.+}.{ext:php}",
            function (Request $request, Response $response, array $args) use ($container)
            {
                $file = $args["file"] ?? "index";
                $ext = $args["ext"] ?? "php";

                $path = ASSET_PATH."/$file.$ext";

                $data = [
                    "route" => $request->getAttribute("vRoute"),
                    "query" => $request->getAttribute("vQuery"),
                    "user"  => $request->getAttribute("user"),
                ];

                if(!file_exists($path))
                    return $container->get("notFoundHandler")($request, $response, $data);

                /** @noinspection PhpIncludeInspection */

                // Pass execution to the specified PHP file.
                include $path;

                // In this case, 'index.php' should handle everything and since there is no Response to return, die()!
                die();
            }
        )->add(new PluginAuthentication($container))->setName("script");
    }

}