<?php

class RunningProcess
{
    public readonly string $id;
    public function __construct(
        public readonly string $context_id,
        public readonly Icon $icon,
        public readonly string $type,
        public readonly string $url,
        public readonly int $begin,
        public readonly int $end,
        public readonly bool $dialog = false,
        public readonly string $title = '',
        public readonly string $additionalShortInfo = '',
        public readonly string $additionalInfoTitleTag = ''
    ) {
        $this->id = uniqid();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'context_id' => $this->context_id,
            'icon' => $this->icon->asImagePath(),
            'type' => $this->type,
            'url' => $this->url,
            'begin' => $this->begin,
            'end' => $this->end,
            'dialog' => $this->dialog,
            'title' => $this->title,
            'additionalShortInfo' => $this->additionalShortInfo,
            'additionalInfoTitleTag' => $this->additionalInfoTitleTag
        ];
    }
}
