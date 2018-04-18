<?php
/**
 * Created by PhpStorm.
 * User: mmalawski
 * Date: 18/04/2018
 * Time: 08:44
 */

namespace Tests\Pact;


use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;

class MyConfig extends VerifierConfig
{
    public function getProviderVersion()
    {
        $value  = parent::getProviderVersion();
        return $value ?? 'latest';
    }

}