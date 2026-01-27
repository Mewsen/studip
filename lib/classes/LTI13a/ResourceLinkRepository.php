<?php
namespace Studip\LTI13a;

use Lti\ResourceLink;
use OAT\Library\Lti1p3Core\Resource\LtiResourceLink\LtiResourceLinkInterface;
use OAT\Library\Lti1p3Core\Util\Collection\Collection;
use OAT\Library\Lti1p3Core\Util\Collection\CollectionInterface;

final class ResourceLinkRepository implements LtiResourceLinkInterface
{
    public function __construct(
        protected ResourceLink $resourceLink
    ) {}

    public function getType(): string
    {
        return 'ltiResourceLink';
    }

    public function getUrl(): ?string
    {
        return $this->resourceLink->getLaunchURL();
    }

    public function getIcon(): ?array
    {
        if ($this->resourceLink->course) {
            return [
                $this->resourceLink->course->getItemAvatarURL()
            ];
        }

        return null;
    }

    public function getThumbnail(): ?array
    {
        return null;
    }

    public function getIframe(): ?array
    {
        return null;
    }

    public function getCustom(): array
    {
        $parameterStr = $this->resourceLink->getCustomParameters();
        if (trim($parameterStr) === '') {
            return [];
        }

        $custom = [];
        foreach (explode("\n", $parameterStr) as $line) {
            [$key, $value] = array_map('trim', explode('=', $line, 2) + [null, null]);

            if ($key !== null && $value !== null) {
                $custom[$key] = $value;
            }
        }

        return $custom;
    }

    public function getLineItem(): ?array
    {
        return null;
    }

    public function getAvailability(): ?array
    {
        return null;
    }

    public function getSubmission(): ?array
    {
        return null;
    }

    public function getIdentifier(): string
    {
        return strval($this->resourceLink->id);
    }

    public function getTitle(): ?string
    {
        return $this->resourceLink->title ?? $this->resourceLink->deployment->registration->name;
    }

    public function getText(): ?string
    {
        return $this->resourceLink->description;
    }

    public function getProperties(): CollectionInterface
    {
        $collection = new Collection();
        $collection->add([
            'url' => $this->getUrl(),
            'title' => $this->getTitle(),
            'text' => $this->getText(),
            'icon' => $this->getIcon(),
            'thumbnail' => $this->getThumbnail(),
            'submission' => $this->getSubmission(),
            'lineItem' => $this->getLineItem(),
            'iframe' => $this->getIframe(),
            'custom' => $this->getCustom(),
        ]);

        return $collection;
    }

    public function normalize(): array
    {
        return array_filter([
            ...$this->getProperties()->all(),
            'type' => $this->getType()
        ]);
    }
}
