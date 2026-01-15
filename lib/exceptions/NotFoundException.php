<?php
declare(strict_types=1);

class NotFoundException extends Exception
{
    private array $details = [];

    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            $message ?: _('Die angeforderte Ressource wurde nicht gefunden.'),
            $code ?: 404,
            $previous
        );
    }

    public function setDetails(array $details): void
    {
        $this->details = $details;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
