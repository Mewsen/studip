<?php
namespace Studip\LTI13a;

use Lti\ResourceLink;
use OAT\Library\Lti1p3Core\Resource\LtiResourceLink\LtiResourceLinkInterface;
use OAT\Library\Lti1p3Core\Util\Collection\Collection;
use OAT\Library\Lti1p3Core\Util\Collection\CollectionInterface;

class ResourceLinkRepository implements LtiResourceLinkInterface
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
        // TODO: return icon URL
        return [
            $this->resourceLink->icon
        ];
    }

    public function getThumbnail(): ?array
    {
        if ($this->resourceLink->course) {
            return [
                $this->resourceLink->course->getItemAvatarURL()
            ];
        }

        return null;
    }

    public function getIframe(): ?array
    {
        //Not supported.
        return null;
    }

    public function getCustom(): ?array
    {
        //Not supported.
        return null;
    }

    public function getLineItem(): ?array
    {
        // TODO: Implement getLineItem() method.
        return null;
    }

    public function getAvailability(): ?array
    {
        // TODO: Implement getAvailability() method.
        return null;
    }

    public function getSubmission(): ?array
    {
        // TODO: Implement getSubmission() method.
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
            'title' => $this->getTitle()
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

    public function getCustomLtiParameters(): array
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

        return [
            'https://purl.imsglobal.org/spec/lti/claim/custom' => $custom
        ];
    }
}
