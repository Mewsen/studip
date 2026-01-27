<?php
namespace Studip\LTI13a;

use Lti\Publication;
use Lti\PublicationUser;
use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use Semester;
use Studip\Lti\Enum\PublicationStatus;

final class PublicationValidator
{
    protected array $publicationConfigs;
    private ?Semester $semester;

    public function __construct(
        protected Publication $publication
    ) {
        $this->publicationConfigs = $publication->getConfigValues();
        $this->semester = Semester::findOneBySQL(
            "Join semester_courses using(semester_id)
                    WHERE semester_courses.course_id = :course_id",
            [
                'course_id' => $publication->range->id
            ]
        );
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function validateLaunch(): Publication
    {
        $this
            ->validateStatus()
            ->validateStartDate()
            ->validateEndDate();

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
            ->validateEnrollmentDeadline()
            ->validateEnrollmentCapacity()
            ->validateStartDate()
            ->validateEndDate();

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
        $enrollmentDeadline = (int) ($this->publicationConfigs['enrollment_deadline'] ?? 0);

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
        $startDate = (int) ($this->publicationConfigs['start_date'] ?? $this->semester?->beginn);

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
        $endDate = (int) ($this->publicationConfigs['end_date'] ?? $this->semester?->end);

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
