<?php
declare(strict_types=1);

namespace App\Controllers\Common;

use App\Middleware\PluginAuthentication;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

final class TemplateController
{

    public function __construct(App $app)
    {
        $container = $app->getContainer();

        $app->get("/{file:.+}.{ext:htm|html|twig}",
            function (Request $request, Response $response, array $args) use ($container)
            {
                $file = $args["file"] ?? "index";
                $ext = $args["ext"] ?? "html";

                //$assets = __DIR__ . "/../../../../www/$file.$ext";
                $assets = ASSET_PATH."/$file.$ext";

                //$templates = __DIR__ . "/../../Views/$file.$ext";
                $templates = VIEWS_PATH."/$file.$ext";

                /** @var \Slim\Router $router */
                $router = $container->get("router");

                $data = [
                    //"request" => $request,
                    "route" => $request->getAttribute("vRoute"),
                    "query" => $request->getAttribute("vQuery"),
                    //"router" => $router,
                ];

                $twig = $container->get("twig");

                if ((file_exists($assets) && !is_dir($assets)) || (file_exists($templates) && !is_dir($templates)))
                    return $twig->render($response, "$file.$ext", $data);
                elseif(file_exists($templates.".twig") && !is_dir($templates.".twig"))
                    return $twig->render($response, "$file.$ext.twig", $data);
                else
                    return $container->get("notFoundHandler")($request, $response, $data);
            }
        )->add(new PluginAuthentication($container))->setName("template");
    }


}