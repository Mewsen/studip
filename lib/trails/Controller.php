<?php
namespace Trails;

use Flexi\Factory;
use Flexi\Template;
use Flexi\TemplateNotFoundException;
use Trails\Exceptions\DoubleRenderError;
use Trails\Exceptions\UnknownAction;

/**
 * A Controller is responsible for matching the unconsumed part of an URI
 * to an action using the left over words as arguments for that action. The
 * action is then mapped to method of the controller instance which is called
 * with the just mentioned arguments. That method can send the #render_action,
 * #render_template, #render_text, #render_nothing or #redirect method.
 * Otherwise the #render_action is called with the current action as argument.
 * If the action method sets instance variables during performing, they will be
 * be used as attributes for the flexi-template opened by #render_action or
 * #render_template. A controller's response's body is populated with the output
 * of the #render_* methods. The action methods can add additional headers or
 * change the status of that response.
 *
 * @package       trails
 *
 * @author        mlunzena
 * @copyright (c) Authors
 * @version       $Id: trails.php 7001 2008-04-04 11:20:27Z mlunzena $
 */
class Controller
{
    protected Dispatcher $dispatcher;
    protected Response $response;
    protected bool $performed = false;
    protected Template|string|null $layout = null;
    protected string $format = 'html';

    /**
     * @param Dispatcher $dispatcher the dispatcher who creates this instance
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->erase_response();
    }

    /**
     * Resets the response of the controller
     *
     * @return void
     */
    public function erase_response()
    {
        $this->performed = false;
        $this->response = new Response();
    }

    /**
     * Return this controller's response
     *
     * @return Response the controller's response
     */
    public function get_response()
    {
        return $this->response;
    }

    /**
     * This method extracts an action string and further arguments from it's
     * parameter. The action string is mapped to a method being called afterwards
     * using the said arguments. That method is called and a response object is
     * generated, populated and sent back to the dispatcher.
     *
     * @param string $unconsumed
     *
     * @return Response
     * @throws UnknownAction
     */
    public function perform($unconsumed)
    {
        [$action, $args, $format] = $this->extract_action_and_args($unconsumed);

        $this->format = $format ?? 'html';

        $before_filter_result = $this->before_filter($action, $args);

        # send action to controller
        # TODO (mlunzena) shouldn't the after filter be triggered too?
        if (!($before_filter_result === false || $this->performed)) {

            $callable = $this->map_action($action);

            if (is_callable($callable)) {
                $callable(...$args);
            } else {
                $this->does_not_understand($action, $args);
            }

            if (!$this->performed) {
                $this->render_action($action);
            }

            $this->after_filter($action, $args);
        }

        return $this->response;
    }

    /**
     * Extracts action and args from a string.
     *
     * @param string $string the processed string
     * @return array        an array with two elements - a string containing the
     *                      action and an array of strings representing the args
     */
    public function extract_action_and_args($string)
    {
        if ('' === $string) {
            return $this->default_action_and_args();
        }

        // find optional file extension
        $format = null;
        if (preg_match('/^(.*[^\/.])\.(\w+)$/', $string, $matches)) {
            [, $string, $format] = $matches;
        }

        // TODO this should possibly remove empty tokens
        $args = explode('/', $string);
        $action = array_shift($args);
        return [$action, $args, $format];
    }

    /**
     * Return the default action and arguments
     *
     * @return array containing the action, an array of args and the format
     */
    public function default_action_and_args()
    {
        return ['index', [], null];
    }

    /**
     * Maps the action to an actual method name.
     *
     * @param string $action
     * @return array  the mapped method name
     */
    public function map_action($action)
    {
        return [&$this, $action . '_action'];
    }

    /**
     * Callback function being called before an action is executed. If this
     * function does not return FALSE, the action will be called, otherwise
     * an error will be generated and processing will be aborted. If this function
     * already #rendered or #redirected, further processing of the action is
     * withheld.
     *
     * @param string $action Name of the action to perform.
     * @param array  $args   An array of arguments to the action.
     * @return bool|void
     */
    public function before_filter(&$action, &$args)
    {
    }

    /**
     * Callback function being called after an action is executed.
     *
     * @param string $action Name of the action to perform.
     * @param array  $args   An array of arguments to the action.
     * @return void
     */
    public function after_filter($action, $args)
    {
    }

    /**
     * @param string $action
     * @param array  $args
     * @return void
     * @throws UnknownAction
     */
    public function does_not_understand($action, $args)
    {
        throw new Exceptions\UnknownAction("No action responded to '$action'.");
    }

    /**
     * @param string $to
     *
     * @return void
     * @throws DoubleRenderError
     */
    public function redirect($to)
    {
        if ($this->performed) {
            throw new Exceptions\DoubleRenderError();
        }

        $this->performed = true;

        # get uri; keep absolute URIs
        $url = preg_match('#^(/|\w+://)#', $to)
            ? $to
            : $this->url_for($to);

        $this->response->add_header('Location', $url)->set_status(302);
    }

    /**
     * Renders the given text as the body of the response.
     *
     * @param string $text the text to be rendered
     * @return void
     * @throws DoubleRenderError
     */
    public function render_text($text = ' ')
    {
        if ($this->performed) {
            throw new Exceptions\DoubleRenderError();
        }

        $this->performed = true;

        $this->response->set_body($text);
    }

    /**
     * Renders the empty string as the response's body.
     *
     * @return void
     * @throws DoubleRenderError
     */
    public function render_nothing()
    {
        $this->render_text('');
    }

    /**
     * Renders the template of the given action as the response's body.
     *
     * @param string $action the action
     * @return void
     */
    public function render_action($action)
    {
        $this->render_template(
            $this->get_default_template($action),
            $this->layout
        );
    }

    public function get_default_template($action)
    {
        $controller_name = Inflector::underscore(
            substr(static::class, 0, -10)
        );
        return $controller_name . '/' . $action;
    }

    /**
     * Renders a template using an optional layout template.
     *
     * @param Template|string      $template_name a flexi template
     * @param Template|string|null $layout        a flexi template which is used as layout
     *
     * @return void
     * @throws DoubleRenderError
     * @throws TemplateNotFoundException
     */
    public function render_template($template_name, $layout = null)
    {
        $factory = $this->get_template_factory();
        $template = $factory->open($template_name);

        $template->set_attributes($this->get_assigned_variables());

        if (isset($layout)) {
            $template->set_layout($layout);
        }

        $this->render_text($template->render());
    }

    /**
     * Create and return a template factory for this controller.
     *
     * @return Factory
     */
    public function get_template_factory()
    {
        return new Factory($this->dispatcher->trails_root . '/views/');
    }

    /**
     * This method returns all the set instance variables to be used as attributes
     * for a template. This controller is returned too as value for
     * key 'controller'.
     *
     * @return array  an associative array of variables for the template
     */
    public function get_assigned_variables()
    {
        $assigns = [];
        $protected = get_class_vars(static::class);

        foreach (get_object_vars($this) as $var => $value) {
            if (!array_key_exists($var, $protected)) {
                $assigns[$var] =& $this->$var;
            }
        }

        $assigns['controller'] = $this;

        return $assigns;
    }

    /**
     * Sets the layout to be used by this controller per default.
     *
     * @param Template|string|null $layout a flexi template to be used as layout
     * @return void
     */
    public function set_layout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Returns a URL to a specified route to your Trails application.
     *
     * Example:
     * Your Trails application is located at 'http://example.com/dispatch.php'.
     * So your dispatcher's trails_uri is set to 'http://example.com/dispatch.php'
     * If you want the URL to your 'wiki' controller with action 'show' and
     * parameter 'page' you should send:
     *
     *   $url = $controller->url_for('wiki/show', 'page');
     *
     * $url should then contain 'http://example.com/dispatch.php/wiki/show/page'.
     *
     * The first parameter is a string containing the controller and optionally an
     * action:
     *
     *   - "{controller}/{action}"
     *   - "path/to/controller/action"
     *   - "controller"
     *
     * This "controller/action" string is not url encoded. You may provide
     * additional parameter which will be urlencoded and concatenated with
     * slashes:
     *
     *     $controller->url_for('wiki/show', 'page');
     *     -> 'wiki/show/page'
     *
     *     $controller->url_for('wiki/show', 'page', 'one and a half');
     *     -> 'wiki/show/page/one+and+a+half'
     *
     * @param string $to a string containing a controller and optionally an action
     * @return string  a URL to this route
     */
    public function url_for($to/*, ...*/)
    {
        # urlencode all but the first argument
        $args = func_get_args();
        $args = array_map('urlencode', $args);
        $args[0] = $to;

        return $this->dispatcher->trails_uri . '/' . implode('/', $args);
    }

    /**
     * @param int $status
     * @return void
     */
    public function set_status($status, $reason_phrase = null)
    {
        $this->response->set_status($status, $reason_phrase);
    }

    /**
     * Sets the content type of the controller's response.
     *
     * @param string $type the content type
     * @return void
     */
    public function set_content_type($type)
    {
        $this->response->add_header('Content-Type', $type);
    }

    /**
     * Exception handler called when the performance of an action raises an
     * exception.
     *
     * @param \Throwable $exception the thrown exception
     * @return Response  a response object
     */
    public function rescue($exception)
    {
        return $this->dispatcher->trails_error($exception);
    }

    public function respond_to($ext)
    {
        return $this->format === $ext;
    }
}
