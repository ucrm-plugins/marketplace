<?php
declare(strict_types=1);
require_once __DIR__ . "/../vendor/autoload.php";



echo "Home Page<br/>";
echo "<br/>";

// =====================================================================================================================
// Directly from public/ folder using UCRM functionality.
// NOTE: These requests are cached, due to being inside an iframe!
// =====================================================================================================================

// Request a static asset...
echo "<a href='public/css/iframe.css'>UCRM: CSS</a><br/>";

// Request a static page...
echo "<a href='public/user.html'>UCRM: User</a><br/>";

echo "<br/>";

// =====================================================================================================================
// Routed using the QueryStringRouter
// NOTE: These requests are cached, due to being inside an iframe!
// =====================================================================================================================

// Request a static asset...
echo "<a href='public.php?/css/iframe.css'>QSR: CSS</a><br/>";

// Request a static page...
echo "<a href='public.php?/user.html'>QSR: User</a><br/>";

echo "<a href='public.php?route=/index.php'>Index</a><br/>";
echo "<a href='public.php?route=/client/add.html.twig'>Client - Add</a><br/>";

echo "<a href='public.php?/example'>Example</a><br/>";
echo "<a href='public.php?/user.html.twig'>Uncached</a><br/>";


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

