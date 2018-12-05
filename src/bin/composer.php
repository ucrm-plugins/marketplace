<?php
declare(strict_types=1);
require __DIR__.'/../vendor/autoload.php';

use UCRM\Common\Plugin;

/**
 * composer.php
 *
 * A shared script that handles composer script execution from the command line.
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */

if($argc === 1)
{
    $usage = "\n".
        "Usage:\n".
        "    composer.php [create|bundle|sync]\n";

    die($usage);
}

define("PLUGIN_NAME", baseName(realpath(__DIR__ . "/../../")));

if(file_exists(__DIR__ . "/../../.env"))
{
    define("PLUGIN_MODE", "DEV");
    (new \Dotenv\Dotenv(__DIR__ . "/../../"))->load();
}



// Handle the different command line arguments...
switch ($argv[1])
{
    // Perform initialization of the Plugin libraries and create the auto-generated Settings class.
    case "create":
        Plugin::initialize(__DIR__."/../");
        Plugin::createSettings("App", "Settings");
        break;

    // Bundle the 'zip/' directory into a package ready for Plugin installation on the UCRM server.
    case "bundle":
        Plugin::bundle(__DIR__."/../", PLUGIN_NAME, ".zipignore", __DIR__."/../../");
        break;

    case "sync":
        $host = getenv("SFTP_HOST");
        $port = intval(getenv("SFTP_PORT"));
        $user = getenv("SFTP_USER");
        $pass = getenv("SFTP_PASS");

        $sftp = new \UCRM\SFTP\SftpClient($host, $port);
        $sftp->login($user, $pass);
        $sftp->setRemoteBasePath("/home/ucrm/data/ucrm/ucrm/data/plugins/".PLUGIN_NAME."/");
        $sftp->setLocalBasePath(__DIR__."/../");

        foreach([ "/ucrm.json", "/data/config.json" ] as $file)
        {
            $sftp->download($file);
        }

        break;

    // TODO: More commands to come!

    default:
        break;
}
