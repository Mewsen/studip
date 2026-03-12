<?php
namespace Studip\Lti\LTI1p3;

use URLHelper;
use Lti\ResourceLink;
use Grading\Definition;
use Studip\Lti\Enum\GradeSynchronization;
use OAT\Library\Lti1p3Core\Util\Collection\Collection;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\AgsClaim;
use OAT\Library\Lti1p3Core\Util\Collection\CollectionInterface;
use OAT\Library\Lti1p3Core\Resource\LtiResourceLink\LtiResourceLinkInterface;

final class ResourceLinkRepository implements LtiResourceLinkInterface
{
    public function __construct(
        protected ResourceLink $resourceLink
    ) {}

    public function getType(): string
    {
        return self::TYPE;
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
        $parameterStr = trim($this->resourceLink->getCustomParameters());
        if ($parameterStr === '') {
            return [];
        }

        $custom = [];
        foreach (explode("\n", $parameterStr) as $line) {
            $line = trim($line);

            if ($line === '' || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2));

            if ($key !== '' && $value !== '') {
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

    public function getAgsClaim(): ?AgsClaim
    {
        $gradeDefinition = Definition::firstOrCreate(
            [
                'tool' => $this->resourceLink->id,
                'item' => ResourceLink::class,
                'course_id' => $this->resourceLink->course_id,
                'category' => 'LTI'
            ],
            [
                'weight' => 1,
                'name' => $this->resourceLink->title
            ]
        );

        $urlParams = [
            'line_item_id' => $gradeDefinition->id,
            'resource_link_id' => $this->resourceLink->id
        ];

        $lineItemURL = URLHelper::getURL('dispatch.php/lti/1p3/ags/line_item', $urlParams, true);
        $lineItemsContainerUrl = URLHelper::getURL('dispatch.php/lti/1p3/ags/line_items', $urlParams, true);

        $gradeSynchronization = (int) $this->resourceLink->getConfigValues()['grade_synchronization'];

        return match ($gradeSynchronization) {
            GradeSynchronization::GradeSyncOnly->value => new AgsClaim(
                [
                    'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
                    'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                    'https://purl.imsglobal.org/spec/lti-ags/scope/score'
                ],
                $lineItemsContainerUrl,
                $lineItemURL
            ),

            GradeSynchronization::GradeManagement->value => new AgsClaim(
                [
                    'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                    'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                    'https://purl.imsglobal.org/spec/lti-ags/scope/score'
                ],
                $lineItemsContainerUrl,
                $lineItemURL
            ),

            default => null
        };
    }
}
