<?php
/**
 * Created by PhpStorm.
 * User: mmalawski
 * Date: 20/04/2018
 * Time: 08:09
 */

namespace Tests\Pact;


use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;
use PhpPact\Standalone\ProviderVerifier\Verifier;

class MyVerifier extends Verifier
{


    /**
     * MyVerifier constructor.
     */
    public function __construct(VerifierConfigInterface $config)
    {
        parent::__construct($config);
    }

    public function getArguments(): array
    {
        $parameters =  parent::getArguments();
        $parameters[] = "--provider-app-version=1.0.0";
        return $parameters;
    }

}