<?php
declare(strict_types=1);
require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/bootstrap.php";

use UCRM\Sessions\SessionUser;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Middleware\PluginAuthentication;

/**
 * Use an immediately invoked function here, to avoid global namespace pollution...
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 */
(function() use ($app, $container)
{



    // -----------------------------------------------------------------------------------------------------------------
    // ADD CUSTOM ROUTES HERE...
    // -----------------------------------------------------------------------------------------------------------------

    $app->get("/example",
        function (Request $request, Response $response, array $args) use ($container)
        {
            return $response->write("This is an example route!");
        }
    );

    // ...




    // -----------------------------------------------------------------------------------------------------------------
    // HTTP GET: ASSET
    // -----------------------------------------------------------------------------------------------------------------

    $app->get("/{file:.+}.{ext:jpg|png|pdf|txt|css|js}",
        function (Request $request, Response $response, array $args) use ($container)
        {
            $file = $args["file"];
            $ext = $args["ext"];

            // Match the Content-Type given the following extension...
            switch ($ext)
            {
                case "jpg":         $contentType = "image/jpg";                 break;
                case "png":         $contentType = "image/png";                 break;
                case "pdf":         $contentType = "application/pdf";           break;
                case "txt":         $contentType = "text/plain";                break;
                case "css":         $contentType = "text/css";                  break;
                case "js" :         $contentType = "text/javascript";           break;
                default   :         $contentType = "application/octet-stream";  break; // Excluded by URL RegEx!
            }

            $path = realpath(__DIR__ . "/www/" . "$file.$ext");

            if(!$path)
                return $response->withStatus(404, "Asset not found!");

            // Set the response Content-Type header and write the contents of the file to the response body.
            $response = $response
                ->withHeader("Content-Type", $contentType)
                ->write(file_get_contents($path));

            // Then return the response!
            return $response;
        }
    )->setName("asset"); // NO Authentication necessary here!

    // -----------------------------------------------------------------------------------------------------------------
    // HTTP GET: TEMPLATE
    // -----------------------------------------------------------------------------------------------------------------

    $app->get("/{file:.+}.{ext:htm|html|twig}",
        function (Request $request, Response $response, array $args) use ($container)
        {
            $file = $args["file"] ?? "index";
            $ext = $args["ext"] ?? "html";

            $assets = __DIR__ . "/www/$file.$ext";
            $templates = __DIR__."/app/Views/$file.$ext";

            /** @var \Slim\Router $router */
            $router = $container->get("router");

            $data = [
                "request" => $request,
                "vRoute" => $request->getAttribute("vRoute"),
                "router" => $router,
            ];

            if ((file_exists($assets) && !is_dir($assets)) || (file_exists($templates) && !is_dir($templates)))
                return $this->twig->render($response, "$file.$ext", $data);
            elseif(file_exists($templates.".twig") && !is_dir($templates.".twig"))
                return $this->twig->render($response, "$file.$ext.twig", $data);
            else
                return $container->get("notFoundHandler")($request, $response, $data);
        }
    )->add(new PluginAuthentication($container))->setName("template");;

    // -----------------------------------------------------------------------------------------------------------------
    // HTTP GET/POST: SCRIPT
    // -----------------------------------------------------------------------------------------------------------------

    $app->map([ "GET", "POST" ], "/{file:.+}.{ext:php}",
        function (Request $request, Response $response, array $args) use ($container)
        {
            $file = $args["file"] ?? "index";
            $ext = $args["ext"] ?? "php";

            $path = __DIR__ . "/www/$file.$ext";

            /** @var \Slim\Router $router */
            $router = $container->get("router");

            $data = [
                "request" => $request,
                "vRoute" => $request->getAttribute("vRoute"),
                "router" => $router,
            ];

            if(!file_exists($path))
                return $container->get("notFoundHandler")($request, $response, $data);

            /** @noinspection PhpUnusedLocalVariableInspection */
            /** @var SessionUser $user */
            $user = $request->getAttribute("user");

            // Pass execution to the specified PHP file.
            include $path;

            // In this case, 'index.php' should handle everything and since there is no Response to return, die()!
            die();

        }
    )->add(new PluginAuthentication($container))->setName("script");

    // Run the Slim Framework Application!
    $app->run();

})();

