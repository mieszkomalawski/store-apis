<?php
/**
 * Created by PhpStorm.
 * User: mmalawski
 * Date: 01/04/2018
 * Time: 17:43
 */

namespace Tests\Pact;


use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

class VerifyPactTest extends TestCase
{
    public function testVerifyAllPact()
    {
        $config = Yaml::parse(file_get_contents(__DIR__ . '/../../app/config/parameters.yml'));
        $brokerUri = $config['paremeters']['broker_uri'];
        $providerUri = $config['paremeters']['provider_uri'];
        $config = new VerifierConfig();
        $config
            ->setProviderName('SomeProvider') // Providers name to fetch.
            ->setProviderVersion('1.0.0') // Providers version.
           // ->setProviderBaseUrl(new Uri('http://192.168.99.100:82')) // URL of the Provider.
           // ->setBrokerUri(new Uri('http://192.168.99.100:81')) // URL of the Pact Broker to publish results.
                ->setProviderBaseUrl(new Uri($providerUri))
                ->setBrokerUri(new Uri($brokerUri))
                ->setVerbose(true)
            ->setPublishResults(true); // Flag the verifier service to publish the results to the Pact Broker.

        // Verify that all consumers of 'SomeProvider' are valid.
        $verifier = new Verifier($config);
        $verifier->verifyAll();

        // This will not be reached if the PACT verifier throws an error, otherwise it was successful.
        $this->assertTrue(true, 'Pact Verification has failed.');
    }
}