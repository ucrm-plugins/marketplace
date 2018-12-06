<?php
declare(strict_types=1);

namespace App\Controllers\Common;

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

final class AssetController
{

    public function __construct(App $app)
    {
        $container = $app->getContainer();

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

                $path = ASSET_PATH."/$file.$ext";

                if(!$path)
                    return $response->withStatus(404, "Asset '$file.$ext' not found!");

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