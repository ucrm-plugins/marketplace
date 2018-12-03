<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Middleware\Twig\PluginExtension;
use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use UCRM\Common\Log;
use UCRM\Common\Plugin;
use UCRM\Sessions\Session;

class PluginAuthentication
{
    /**
     * The default allowed User Groups.
     */
    protected const DEFAULT_ALLOWED_USER_GROUPS = [ "Admin Group" ];



    /**
     * @var Container A local reference to the Slim Framework DI Container.
     */
    protected $container;

    /**
     * @var array An array of allowed User Groups in the UCRM for which to validate the currently authenticated user.
     */
    protected $allowedUserGroups;



    /**
     * PluginAuthentication constructor.
     *
     * @param Container $container
     * @param array $allowedUserGroups
     */
    public function __construct(Container $container, array $allowedUserGroups = self::DEFAULT_ALLOWED_USER_GROUPS)
    {
        $this->container = $container;
        $this->allowedUserGroups = $allowedUserGroups;
    }


    /**
     * Middleware invokable class
     *
     * @param  Request $request The current PSR-7 Request object.
     * @param  Response $response The current PSR-7 Response object.
     * @param  callable $next The next middleware for which to pass control if this middleware does not fail.
     *
     * @return Response Returns a PSR-7 Response object.
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        // IF this Plugin is in development mode, THEN skip this Middleware!
        if(Plugin::environment() === "dev")
            return $next($request, $response);

        // IF a Session is not already started, THEN start one!
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        // Get the currently authenticated User, while also capturing the actual '/current-user' response!
        $user = Session::getCurrentUser();

        // Display an error if no user is authenticated!
        if(!$user)
            Log::http("No User is currently Authenticated!", 401);

        // Display an error if the authenticated user is NOT part of a valid User Group!
        if(!in_array($user->getUserGroup(), $this->allowedUserGroups))
            Log::http("Currently Authenticated User is not in any allowed User Group!", 401);

        // Set the current session user on the container, for later use in the application.
        $this->container["sessionUser"] = $user;

        PluginExtension::setGlobal("sessionUser", $user);

        // If a valid user is authenticated and
        return $next($request, $response);
    }
}