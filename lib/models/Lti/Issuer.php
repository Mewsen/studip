<?php
namespace Lti;

class Issuer extends \SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_issuers';

        parent::configure($config);
    }
}
