<?php
declare(strict_types=1);

namespace UCRM\Slim\Controllers\Common;

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AssetController
 *
 * Handles routing and provision of static assets.
 *
 * @package UCRM\Slim\Controllers\Common
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class AssetController
{
    /**
     * AssetController constructor.
     *
     * @param App $app The Slim Application for which to configure routing.
     */
    public function __construct(App $app)
    {
        // Get a local reference to the Slim Application's DI Container.
        $container = $app->getContainer();

        $app->get("/{file:.+}.{ext:jpg|png|pdf|txt|css|js|htm|html}",
            function (Request $request, Response $response, array $args) use ($container)
            {
                // Get the file and extension from the matched route.
                $file = $args["file"];
                $ext = $args["ext"];

                // Interpolate the absolute path to the static asset.
                $path = ASSET_PATH."/$file.$ext";

                // IF the static asset file does not exist, THEN return a HTTP 404!
                if(!$path)
                    return $response->withStatus(404, "Asset '$file.$ext' not found!");

                // Specify the Content-Type given the extension...
                switch ($ext)
                {
                    case "jpg":                 $contentType = "image/jpg";                 break;
                    case "png":                 $contentType = "image/png";                 break;
                    case "pdf":                 $contentType = "application/pdf";           break;
                    case "txt":                 $contentType = "text/plain";                break;
                    case "css":                 $contentType = "text/css";                  break;
                    case "js" :                 $contentType = "text/javascript";           break;
                    case "htm": case "html":    $contentType = "text/html";                 break;

                    default   :                 $contentType = "application/octet-stream";  break;
                }

                // Set the response Content-Type header and write the contents of the file to the response body.
                $response = $response
                    ->withHeader("Content-Type", $contentType)
                    ->write(file_get_contents($path));

                // Then return the response!
                return $response;
            }
        )->setName("asset"); // NO Authentication necessary here!
    }

}