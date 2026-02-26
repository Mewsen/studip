<?php
namespace JsonApi;

/**
 * @template T of SORM
 */
trait SORMCrudCallbacksTrait
{
    /**
     * @param SORM $sorm
     * @return SORM
     */
    protected function beforeStore(SORM $sorm, array $data): SORM
    {
        return $sorm;
    }

    /**
     * @param SORM $sorm
     * @return SORM
     */
    protected function afterStore(SORM $sorm, array $data): SORM
    {
        return $sorm;
    }
}
