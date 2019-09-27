<?php


namespace MyVendor\Deliverrando\Controller\Helper;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BingMapsRestApiHelper implements SingletonInterface
{
    /**
     * @var mixed $lastResult
     */
    protected $lastResult = null;

    /**
     * @param string $query
     * @param string $apiKey
     * @return string|object
     */
    public function makeApiCall(string $query, string $apiKey)
    {
        $client = GeneralUtility::makeInstance(Client::class);

        try {
            $response = $client->request('GET', 'http://dev.virtualearth.net/REST/v1' . $query . '&key=' . $apiKey);
        } catch (GuzzleException $e) {
            return 'InvalidStatusCode';
        }

        if($response->getStatusCode() !== 200) {
            return 'InvalidStatusCode';
        }
        $header = $response->getHeaders();
        assert($response->getHeaderLine('content-type') === 'application/json; charset=utf-8');
        $json = json_decode($response->getBody());
        assert($json->statusCode == 200);
        $this->lastResult = $json;
        return $this->lastResult;
    }

    /**
     * @return mixed
     */
    public function getLastResult()
    {
        return $this->lastResult;
    }
}