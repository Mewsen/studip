<?php
namespace Trails;

use Trails\Exceptions\MissingFile;
use Trails\Exceptions\RoutingError;
use Trails\Exceptions\UnknownController;

/**
 * The Dispatcher is used to map an incoming HTTP request to a Controller
 * producing a response which is then rendered. To initialize an instance of
 * class Dispatcher you have to give three configuration settings:
 *
 *          trails_root - the absolute file path to a directory containing the
 *                        applications controllers, views etc.
 *           trails_uri - the URI to which routes to mapped Controller/Actions
 *                        are appended
 *   default_controller - the route to a controller, that is used if no
 *                        controller is given, that is the route is equal to '/'
 *
 * After instantiation of a dispatcher you have to call method #dispatch with
 * the request uri to be mapped to a controller/action pair.
 *
 * @package       trails
 *
 * @author        mlunzena
 * @copyright (c) Authors
 * @version       $Id: trails.php 7001 2008-04-04 11:20:27Z mlunzena $
 */
class Dispatcher
{
    # TODO (mlunzena) Konfiguration muss anders geschehen

    /**
     * This is the absolute file path to the trails application directory.
     */
    public string $trails_root;

    /**
     * This is the URI to which routes to controller/actions are appended.
     */
    public string $trails_uri;

    /**
     * This variable contains the route to the default controller.
     */
    public string $default_controller;

    /**
     * @param string $trails_root        absolute file path to a directory containing the
     *                 applications controllers, views etc.
     * @param string $trails_uri         the URI to which routes to mapped Controller/Actions
     *                 are appended
     * @param string $default_controller the route to a controller, that is used if no
     *                 controller is given, that is the route is equal to '/'
     */
    public function __construct(
        string $trails_root,
        string $trails_uri,
        string $default_controller
    ) {
        $this->trails_root = $trails_root;
        $this->trails_uri = $trails_uri;
        $this->default_controller = $default_controller;
    }

    /**
     * Maps a string to a response which is then rendered.
     *
     * @param string $uri The requested URI.
     */
    public function dispatch($uri)
    {
        # E_USER_ERROR|E_USER_WARNING|E_USER_NOTICE|E_RECOVERABLE_ERROR = 5888
        $old_handler = set_error_handler([$this, 'error_handler'], 5888);

        ob_start();
        $level = ob_get_level();

        $this->map_uri_to_response($this->clean_request_uri((string) $uri))->output();

        while (ob_get_level() >= $level) {
            ob_end_flush();
        }

        if (isset($old_handler)) {
            set_error_handler($old_handler);
        }
    }

    /**
     * Maps an URI to a response by figuring out first what controller to
     * instantiate, then delegating the unconsumed part of the URI to the
     * controller who returns an appropriate response object or throws an
     * Exception.
     *
     * @param string $uri the URI string
     * @return Response a response object
     */
    public function map_uri_to_response($uri)
    {
        try {
            [$controller_path, $unconsumed] = '' === $uri ? $this->default_route() : $this->parse($uri);

            $controller = $this->load_controller($controller_path);

            $response = $controller->perform($unconsumed);
        } catch (Exception $e) {
            $response = isset($controller) ? $controller->rescue($e) : $this->trails_error($e);
        }

        return $response;
    }

    /**
     * @return array  an array containing the default controller and an
     *                empty unconsumed route
     * @throws MissingFile
     */
    public function default_route()
    {
        if (!$this->file_exists($this->default_controller . '.php')) {
            throw new Exceptions\MissingFile(
                "Default controller '{$this->default_controller}' not found'"
            );
        }
        return [$this->default_controller, ''];
    }

    public function trails_error($exception)
    {
        ob_clean();

        # show details for local requests
        $detailed = @$_SERVER['REMOTE_ADDR'] === '127.0.0.1';

        $body = sprintf('<html><head><title>Trails Error</title></head>' .
            '<body><h1>%s</h1><pre>%s</pre></body></html>',
            htmlentities($exception->__toString()),
            $detailed
                ? htmlentities($exception->getTraceAsString())
                : '');

        if ($exception instanceof Exception) {
            $response = new Response(
                $body,
                $exception->getHeaders(),
                $exception->getCode(),
                $exception->getMessage()
            );
        } else {
            $response = new Response(
                $body,
                [],
                500,
                $exception->getMessage()
            );
        }

        return $response;
    }

    /**
     * Clean up URI string by removing the query part and leading slashes.
     *
     * @param string $uri an URI string
     * @return string  the cleaned string
     */
    public function clean_request_uri($uri)
    {
        $pos = strpos($uri, '?');
        if ($pos !== false) {
            $uri = substr($uri, 0, $pos);
        }
        return ltrim($uri, '/');
    }

    /**
     * @param string $unconsumed
     * @param string $controller
     * @return array
     * @throws RoutingError
     */
    public function parse($unconsumed, $controller = null)
    {
        [$head, $tail] = $this->split_on_first_slash($unconsumed);

        if (!preg_match('/^\w+$/', $head)) {
            throw new RoutingError("No route matches '$head'");
        }

        $controller = (isset($controller) ? $controller . '/' : '') . $head;

        if ($this->file_exists($controller . '.php')) {
            return [$controller, $tail];
        }

        if ($this->file_exists($controller)) {
            return $this->parse($tail, $controller);
        }

        throw new RoutingError("No route matches '$head'");
    }

    /**
     * @param string $str
     * @return array
     */
    public function split_on_first_slash($str)
    {
        preg_match(":([^/]*)(/+)?(.*):", $str, $matches);
        return [$matches[1], $matches[3]];
    }

    /**
     * @param string $path
     * @return bool
     */
    public function file_exists($path)
    {
        return file_exists("{$this->trails_root}/controllers/$path");
    }

    /**
     * Loads the controller file for a given controller path and return an
     * instance of that controller. If an error occures, an exception will be
     * thrown.
     *
     * @param string $controller the relative controller path
     * @return Controller  an instance of that controller
     * @throws UnknownController
     */
    public function load_controller($controller)
    {
        require_once "{$this->trails_root}/controllers/{$controller}.php";
        $class = Inflector::camelize($controller) . 'Controller';
        if (!class_exists($class)) {
            throw new UnknownController("Controller missing: '$class'");
        }
        return new $class($this);
    }

    /**
     * This method transforms E_USER_* and E_RECOVERABLE_ERROR to
     * Exceptions.
     *
     * @param integer $errno  the level of the error raised
     * @param string  $string the error message
     * @param string  $file   the filename that the error was raised in
     * @param integer $line   the line number the error was raised at
     *
     * @return bool
     * @throws Exception
     *
     */
    public function error_handler($errno, $string, $file, $line)
    {
        if (!(5888 & $errno)) {
            return false;
        }
        throw new Exception(500, $string);
    }
}
