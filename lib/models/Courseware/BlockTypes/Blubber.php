<?php

namespace Courseware\BlockTypes;

use BlubberThread;
use Course;

/**
 * This class represents the content of a Courseware blubber block.
 *
 * @author  Ron Lucke <lucke@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 5.0
 */
class Blubber extends BlockType
{
    public static function getType(): string
    {
        return 'blubber';
    }

    public static function getTitle(): string
    {
        return _('Blubber');
    }

    public static function getDescription(): string
    {
        return _('Lehrende können eine Konversation starten oder eine bestehende Konversation einbinden.');
    }

    public function initialPayload(): array
    {
        return [
            'thread_id' => '',
        ];
    }

    public static function getJsonSchema(): string
    {
        $schemaFile = __DIR__.'/Blubber.json';
        return file_get_contents($schemaFile);
    }

    public static function getCategories(): array
    {
        return ['interaction'];
    }

    public static function getContentTypes(): array
    {
        return ['text'];
    }

    public static function getFileTypes(): array
    {
        return [];
    }

    public function copyPayload(string $rangeId = ''): array
    {
        $payload = $this->getPayload();
        $threadId = $payload['thread_id'];

        $course = Course::find($rangeId);

        if ( $threadId === '' || $rangeId === '' || !$course) {
            return $this->initialPayload();
        }

        $remoteBlubberThread = \BlubberThread::find($threadId);

        $threadTitle = $remoteBlubberThread['content'];

        $presentBlubberThread = \BlubberThread::findOneBySQL('content = ? AND context_id = ?', array($threadTitle, $rangeId));

        if ($presentBlubberThread !== null) {
            $payload['thread_id'] = $presentBlubberThread['thread_id'];
        } else {
            $user = \User::findCurrent();
            $newBlubberThread = \BlubberThread::create(
                [
                    'context_type' => \BlubberThread::CTX_TYPE_COURSE,
                    'context_id' => $rangeId,
                    'user_id' => $user->id,
                    'external_contact' => 0,
                    'display_class' => null,
                    'visible_in_stream' => 1,
                    'commentable' => 1,
                    'content' => $threadTitle,
                ]
            );
            $payload['thread_id'] = $newBlubberThread['thread_id'];
        }

        return $payload;
    }
}
