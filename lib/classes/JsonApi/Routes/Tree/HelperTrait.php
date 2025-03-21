<?php
namespace JsonApi\Routes\Tree;

trait HelperTrait
{
    protected function validateSemesterFilter(array $filters): ?string
    {
        if (
            isset($filters['semester'])
            && $filters['semester'] !== 'all'
            && !\Semester::exists($filters['semester'])
        ) {
            return 'Invalid "semester".';
        }

        return null;
    }

    protected function validateSemClassFilter(array $filters): ?string
    {
        if (
            isset($filters['semester'])
            && $filters['semester'] !== 'all'
            && !\Semester::exists($filters['semester'])
        ) {
            return 'Invalid filter parameter "semester".';
        }

        if (
            isset($filters['semclass'])
            && $filters['semclass'] !== '0'
            && !\SeminarCategories::Get($filters['semclass'])
        ) {
            return 'Invalid filter parameter "semclass".';
        }

        return null;
    }
}
