<?php

namespace DashboardWidget\WidgetTypes;

use Opis\JsonSchema\Validator;

/**
 * This class represents the content of a DashboardWidget widget and the architecture of such widgets.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
abstract class WidgetType
{
    /**
     * Returns a short string describing this type of widgets.
     *
     * @return string the short string describing this type
     */
    abstract public static function getType(): string;

    /**
     * Returns a short string describing this type of widgets.
     *
     * @return string the short string describing this type
     */
    abstract public static function getScope(): string;

    /**
     * Returns the title of this type of widgets suitable to display it to the user.
     *
     * @return string the title of this type of widgets
     */
    abstract public static function getTitle(): string;

    /**
     * Returns the description of this type of widgets suitable to display it to the user.
     *
     * @return string the description of this type of widgets
     */
    abstract public static function getDescription(): string;

    /**
     * Returns the initial payload of every instance of this type of widget.
     *
     * @return array<mixed> the initial payload of an instance of this type of widget
     */
    abstract public function initialPayload(): array;

    /**
     * Returns the JSON schema which is used to validate the payload of
     * instances of this type of widget.
     *
     * @return string the JSON schema to be used
     */
    abstract public static function getJsonSchema(): string;

    /**
     * Returns all known types of widget types: core types and plugin types as well.
     *
     * @return array<string> a list of all known types of dashboard widgets;
     *                       each one a fully qualified class name
     *                      e.g. ['chat.single' => 'DashboardWidget\\WidgetTypes\\Chat\\ChatSingleWidget']
     */
    public static function getWidgetTypes(): array
    {
        $widgetTypes = [];

        $chatWidgetScopes = Chat\ChatWidgetType::registerScopes();
        $ContactWidgetScopes = Contact\ContactWidgetType::registerScopes();
        $interestGroupWidgetScopes = Group\GroupWidgetType::registerScopes();

        $widgetTypes = array_merge(
            $chatWidgetScopes,
            $ContactWidgetScopes,
            $interestGroupWidgetScopes
        );

        foreach (\PluginEngine::getPlugins(\DashboardWidget\DashboardWidgetPlugin::class) as $plugin) {
            /** @disregard P1013 because the registerWidgetTypes comes from the interface DashboardWidgetPlugin */
            $widgetTypes = $plugin->registerWidgetTypes($widgetTypes);
        }

        return $widgetTypes;
    }

    /**
     * Discover and register all scope variants for this widget type.
     *
     * Scans the directory of the concrete WidgetType subclass (the calling
     * class) for other PHP files that define subclasses of that parent class.
     * For each discovered subclass that implements a `getScope()` method this
     * method will require the file and add an entry to the returned map where
     * the key is formed as `<type>.<scope>` and the value is the fully
     * qualified classname of the subclass.
     *
     * Note: files are loaded with `require_once` and the discovery assumes
     *     * classes live in the same namespace/directory as the parent class.
     *
     * @return array<string,string> Map of identifier to classname, e.g.
     *                              `['chat.single' => 'DashboardWidget\\WidgetTypes\\Chat\\ChatSingleWidget']`
     */
    public static function registerScopes(): array
    {
        $parentClass = static::class;

        $reflection = new \ReflectionClass($parentClass);
        $directory = dirname($reflection->getFileName());
        $namespace = $reflection->getNamespaceName();

        $children = [];

        foreach (scandir($directory) as $file) {
            $key = $parentClass::getType();
            if (!str_ends_with($file, '.php') || $file === $reflection->getShortName() . '.php') {
                continue;
            }

            require_once $directory . '/' . $file;

            $class = $namespace . '\\' . basename($file, '.php');

            if (class_exists($class) && is_subclass_of($class, $parentClass) && method_exists($class, 'getScope')) {
                $key .= '.' . $class::getScope();
                $children[$key] = $class;
            }
        }

        return $children;
    }

    /**
     * @param string $type a short string describing a type of widget.
     * @param string $scope a short string describing the scope of widget type.
     *
     * @return bool true, if the given type of widget is valid; false otherwise
     */
    public static function isOfTypeWithScope(string $type, string $scope): bool
    {
        return null !== self::findWidgetType($type, $scope);
    }

    /**
     * Returns the classname of a widget type whose `type` equals the given one.
     *
     * @param string $type a short string describing a type of widget; see `getType`
     * @param string $scope a short string describing a scope of widget; see `getScope`
     *
     * @return mixed either the classname if the given type was valid; null otherwise
     */
    public static function findWidgetType(string $type, string $scope): ?string
    {
        foreach (self::getWidgetTypes() as $class) {
            if ($class::getType() === $type && $class::getScope() === $scope) {
                return $class;
            }
        }

        return null;
    }

    /**
     * Creates an instance of WidgetType for a given widget.
     *
     * @param \DashboardWidget\Widget $widget the widget whose WidgetType is returned
     *
     * @return WidgetType the WidgetType associated with the given widget
     */
    public static function factory(\DashboardWidget\Widget $widget): WidgetType
    {
        if (!($class = self::findWidgetType($widget['type'], $widget['scope']))) {
            throw new \RuntimeException('There is no WidgetType for the given type.');
        }

        return new $class($widget);
    }

    /**
     * Validates a given payload according to the JSON schema of this type of widget.
     *
     * @param mixed $payload the payload to be validated
     *
     * @return bool true, if the given payload is valid; false otherwise
     */
    public function validatePayload($payload): bool
    {
        $validator = new Validator();
        $result = $validator->validate($payload, static::getJsonSchema());
        return $result->isValid();
    }

    /** @var \DashboardWidget\Widget */
    protected $widget;

    /**
     * @param \DashboardWidget\Widget $widget the widget associated to this type
     */
    public function __construct(\DashboardWidget\Widget $widget)
    {
        $this->widget = $widget;
    }

    /**
     * Returns the decoded payload of the widget associated with this instance.
     *
     * @return mixed the decoded payload
     */
    public function getPayload()
    {
        $decoded = $this->decodePayloadString($this->widget['payload']);

        return $decoded;
    }

    /**
     * Encodes the payload and sets it in the associated widget.
     *
     * @param mixed $payload the payload to be encoded
     */
    public function setPayload($payload): void
    {
        $this->widget['payload'] = null === $payload ? null : json_encode($payload, true);
    }

    /**
     * Decode a given payload.
     *
     * @param string $payload the payload to be decoded
     *
     * @return mixed the decoded payload
     */
    protected function decodePayloadString(string $payload)
    {
        if ('' === $payload) {
            $decoded = $this->initialPayload();
        } else {
            $decoded = json_decode($payload, true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new \RuntimeException('The payload could not be decoded: '.json_last_error_msg());
            }
        }

        return $decoded;
    }
}
