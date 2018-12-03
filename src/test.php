<?php
declare(strict_types=1);
require __DIR__.'/vendor/autoload.php';

use UCRM\Common\Plugin;
use MVQN\SFTP\SftpClient;

define("PLUGIN_NAME", baseName(realpath(__DIR__."/../")));

if(file_exists(__DIR__."/../.env"))
{
    define("PLUGIN_MODE", "DEV");
    (new \Dotenv\Dotenv(__DIR__ . "/../"))->load();
}

Plugin::initialize(__DIR__);

$host = getenv("SFTP_HOST");
$port = intval(getenv("SFTP_PORT"));
$user = getenv("SFTP_USER");
$pass = getenv("SFTP_PASS");

$sftp = new SftpClient($host, $port);
$sftp->login($user, $pass);
$sftp->setRemoteBasePath("/home/ucrm/data/ucrm/ucrm/data/plugins/".PLUGIN_NAME."/");
$sftp->setLocalBasePath(__DIR__);

foreach([ "/ucrm.json", "/data/config.json" ] as $file)
{
    $data = $sftp->download($file);
}