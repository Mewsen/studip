<?php
namespace Studip\LTI13a;

use Lti\Publication;
use Lti\PublicationUser;
use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use Studip\Lti\Enum\PublicationStatus;

class PublicationValidator
{
    protected array $publicationConfigs;

    public function __construct(
        protected Publication $publication
    ) {
        $this->publicationConfigs = $publication->getConfigValues();
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function validateLaunch(): Publication
    {
        $this
            ->validateStatus()
            ->validateEndDate()
            ->validateStartDate();

        return $this->publication;
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function validateEnrollment(): Publication
    {
        $this
            ->validateStatus()
            ->validateEnrollmentCapacity()
            ->validateEnrollmentDeadline();

        return $this->publication;
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function validateAll(): Publication
    {
        $this
            ->validateStatus()
            ->validateEndDate()
            ->validateStartDate()
            ->validateEndDate()
            ->validateStartDate();

        return $this->publication;
    }

    /**
     * @throws LtiExceptionInterface
     */
    private function validateStatus(): self
    {
        if ($this->publication->status !== PublicationStatus::Active->value) {
            throw new LtiException('This content is currently inactive and cannot be accessed.');
        }

        return $this;
    }

    /**
     * @throws LtiExceptionInterface
     */
    private function validateEnrollmentDeadline(): self
    {
        $enrollmentDeadline = (int) $this->publicationConfigs['enrollment_deadline'];

        if (!$enrollmentDeadline) {
            return $this;
        }

        if ($enrollmentDeadline <= time()) {
            throw new LtiException('The enrollment deadline has already passed.');
        }

        return $this;
    }

    /**
     * @throws LtiExceptionInterface
     */
    private function validateStartDate(): self
    {
        $startDate = (int) $this->publicationConfigs['start_date'];

        if (!$startDate) {
            return $this;
        }

        if ($startDate > time()) {
            throw new LtiException(
                'This content is not available yet. It will be accessible starting on: ' . date('c', $startDate)
            );
        }

        return $this;
    }

    /**
     * @throws LtiExceptionInterface
     */
    private function validateEndDate(): self
    {
        $endDate = (int) $this->publicationConfigs['end_date'];

        if (!$endDate) {
            return $this;
        }

        if ($endDate < time()) {
            throw new LtiException(
                'This content is no longer available. Access ended on: ' . date('c', $endDate)
            );
        }

        return $this;
    }

    /**
     * @throws LtiExceptionInterface
     */
    private function validateEnrollmentCapacity(): self
    {
        $maximumEnrolledUsers = (int) ($this->publicationConfigs['maximum_enrolled_users'] ?? 0);

        if ($maximumEnrolledUsers <= 0) {
            return $this;
        }

        $currentEnrolledUsers = PublicationUser::countBySql("publication_id = ?", [$this->publication->id]);

        if ($currentEnrolledUsers >= $maximumEnrolledUsers) {
            throw new LtiException(
                'Enrollment is currently full. No additional users can be enrolled at this time.'
            );
        }

        return $this;
    }
}
