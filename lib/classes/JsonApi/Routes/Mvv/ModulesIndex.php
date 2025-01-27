<?php

namespace JsonApi\Routes\Mvv;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\Schemas\Module;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class ModulesIndex extends JsonApiController
{
    protected $allowedFilteringParameters = ['q', 'institute', 'semester', 'section'];

    protected $allowedPagingParameters = ['offset', 'limit'];

    protected $allowedIncludePaths = [
        Module::REL_MODULE_COMPONENTS,
        Module::REL_END_SEMESTER,
        Module::REL_START_SEMESTER,
        Module::REL_RESPONSIBLE_DEPARTMENT,
        Module::REL_DEPARTMENTS,
        Module::REL_SOURCE_MODULE,
        Module::REL_VARIANT_MODULE,
    ];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        if (!Authority::canIndexModules($user = $this->getUser($request))) {
            throw new AuthorizationFailedException();
        }

        $filtering = $this->getQueryParameters()->getFilteringParameters() ?: [];
        $error = $this->validateFilters($filtering);
        if ($error) {
            throw new BadRequestException($error);
        }

        [$offset, $limit] = $this->getOffsetAndLimit();
        $modules = $this->getModules($filtering, $offset, $limit);

        return $this->getPaginatedContentResponse(
            $modules,
            count($modules)
        );
    }

    private function validateFilters($filtering)
    {
        // keyword aka q
        if (isset($filtering['q']) && mb_strlen($filtering['q']) < 3) {
            return 'Search term too short.';
        }

        // institute
        if (isset($filtering['institute']) && !\Institute::exists($filtering['institute'])) {
            return 'Filter `institute` must be a valid id.';
        }

        // section
        if (isset($filtering['section']) && !\StgteilAbschnitt::exists($filtering['section'])) {
            return 'Filter `section` must be a valid id';
        }

        // semester
        if (isset($filtering['semester']) && !\Semester::exists($filtering['semester'])) {
            return 'Filter `semester` must be a valid id.';
        }
    }

    private function getModules($filtering, $offset, $limit): array
    {
        $join = '';
        $where = ' 1 ';
        $filtering['offset'] = $offset;
        $filtering['limit'] = $limit;
        if (isset($filtering['institute'])) {
            $where .= ' AND `institut_id` = :institute ';
        }
        if (isset($filtering['section'])) {
            $join .= 'LEFT JOIN `mvv_stgteilabschnitt_modul` USING(`modul_id`) ';
            $where .= ' AND `mvv_stgteilabschnitt_modul`.`abschnitt_id` = :section';
        }
        if (isset($filtering['semester'])) {
            $semester = \Semester::find($filtering['semester']);
            unset($filtering['semester']);
            $filtering['semester_start'] = $semester->beginn;
            $filtering['semester_end'] = $semester->ende;
            $join .= 'LEFT JOIN `semester_data` AS `start_sem`
                        ON (`mvv_modul`.`start` = `start_sem`.`semester_id`)
                    LEFT JOIN `semester_data` AS `end_sem`
                        ON (`mvv_modul`.`end` = `end_sem`.`semester_id`) ';
            $where .= ' AND (`start_sem`.`beginn` <= :semester_end OR ISNULL(`start_sem`.`beginn`))
                        AND (`end_sem`.`ende` >= :semester_start OR ISNULL(`end_sem`.`ende`))';
        }
        $join .= 'LEFT JOIN `mvv_modul_deskriptor` USING(`modul_id`) ';
        if (isset($filtering['q'])) {
            $where .= " AND (`mvv_modul_deskriptor`.`bezeichnung` LIKE CONCAT('%', :q, '%') OR `mvv_modul`.`code` LIKE CONCAT('%', :q, '%')) ";
        }
        $where .= ' ORDER BY `mvv_modul`.`code` ASC, `mvv_modul_deskriptor`.`bezeichnung` ASC
                    LIMIT :limit OFFSET :offset';
        return \Modul::findBySQL(
            ($join ? $join . ' WHERE ' : '') . $where,
            $filtering
        );
    }
}
