<?php
namespace Studip;

use Stringable,
    JsonSerializable,
    Studip\Forms\Form,
    \Icon;

/**
 * PHP wrapper class for parts of a wizard, creating the data structure expected by the Vue component StudipWizard.
 *
 * Such a part can either be
 * - a Vue component as returned by VueApp::create()
 * - a StudipForm returned by one of the Forms\Form creation methods (::create() or ::fromSORM())
 *
 * @author Thomas Hackl <hackl@data-quest.de>
 * @since Stud.IP 6.3
 */
final class WizardPart implements Stringable, JsonSerializable
{

    private string $id;
    private string $type;
    private Form|VueApp $content;
    private string $title;
    private string $iconShape;

    /**
     * Creates a vue app with the given relative path to the app component.
     */
    public static function create(string $id, Form|VueApp $content, string $title = '', string $iconShape = ''): WizardPart
    {
        return new static($id, $content, $title, $iconShape);
    }

    public function __construct(string $id, Form|VueApp $content, string $title = '', string $iconShape = '')
    {
        $this->id = $id;
        $this->type = get_class($content);
        $this->content = $content;
        $this->title = $title;
        $this->iconShape = $iconShape;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent() {
        return $this->content;
    }

    public function setId(string $id): WizardPart
    {
        $this->id = $id;
        return $this;
    }

    public function setTitle(string $title): WizardPart
    {
        $this->title = $title;
        return $this;
    }

    public function getIconShape(): string
    {
        return $this->iconShape;
    }

    public function setIconShape(string $shape): WizardPart
    {
        $this->iconShape = $shape;
        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'icon' => $this->iconShape,
            'content' => $this->render()
        ];
    }

    /**
     * Renders the vue app
     */
    public function render(): string
    {
        \NotificationCenter::postNotification('WizardPartWillRender', $this);

        switch ($this->type) {
            case Form::class:
                $content = $this->content->getTemplate()->render();
                break;
            case VueApp::class:
                $content = $this->content->getTemplate(true)->render();
                break;
            default:
                $content = '';
        }

        \NotificationCenter::postNotification('WizardPartDidRender', $this);

        return $content;
    }

    /**
     * Returns a string representation of the vue app by rendering it.
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
