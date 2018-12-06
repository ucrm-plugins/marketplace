<?php
declare(strict_types=1);
require_once __DIR__ . "/../vendor/autoload.php";

use UCRM\Sessions\SessionUser;

/** @noinspection PhpUnusedLocalVariableInspection */
/** @var SessionUser $user */


echo "Home Page<br/>";

//var_dump($user);

//if(session_status() === PHP_SESSION_NONE)
//    session_start();

//if(isset($_SESSION))
//    var_dump($_SESSION);

//if(isset($_COOKIE))
//    var_dump($_COOKIE);

if(isset($_GET))
    var_dump($_GET);

//var_dump($_SERVER);
echo "<br/>";
echo "<a href='public/user.html'>User</a><br/>";

echo "<a href='public.php?route=/index.php'>Index</a><br/>";
echo "<a href='public.php?route=/client/add.html.twig'>Client - Add</a><br/>";

echo "<a href='public.php?/example'>Example</a><br/>";

echo "

<script type='application/javascript'>

    //console.clear();
    //console.log('testing');
    //console.log(window.location.href)

</script>

";

echo "

<form method='POST' action='public.php?/submit.php&testing=123'>
    <input type='hidden' name='username' value='rspaeth' />
    <input type='hidden' name='password' value='password' />
    <button type='submit'>Submit</button>
</form>

";



//echo "<pre>";
//var_dump($_SERVER);
//echo "</pre>";

