<?php

namespace Routmoute\Bundle\RoutmouteSireneBundle\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

use Routmoute\Bundle\RoutmouteSireneBundle\Exception\SireneAuthFailedException;
use Routmoute\Bundle\RoutmouteSireneBundle\Exception\SireneUnitClosedException;
use Routmoute\Bundle\RoutmouteSireneBundle\Exception\SireneMisformattedParametersException;
use Routmoute\Bundle\RoutmouteSireneBundle\Exception\SireneInvalidTokenException;
use Routmoute\Bundle\RoutmouteSireneBundle\Exception\SireneInvalidPermissionsException;
use Routmoute\Bundle\RoutmouteSireneBundle\Exception\SireneNotExistException;
use Routmoute\Bundle\RoutmouteSireneBundle\Exception\SireneMaxQuotaException;
use Routmoute\Bundle\RoutmouteSireneBundle\Exception\SireneServerErrorException;
use Routmoute\Bundle\RoutmouteSireneBundle\Exception\SireneServiceUnavailableException;
use Routmoute\Bundle\RoutmouteSireneBundle\Exception\SireneUnknownException;
use Routmoute\Bundle\RoutmouteSireneBundle\Exception\SireneEmptyParamsException;

class RoutmouteSireneApiService
{
    private const AUTH_URL = "https://api.insee.fr/token";
    private const API_URL = "https://api.insee.fr/entreprises/sirene/V3.11";

    private string $consumer_key;
    private string $consumer_secret;

    private HttpClientInterface $httpClient;
    private FilesystemAdapter $cache;

    public function __construct(string $consumer_key, string $consumer_secret, HttpClientInterface $httpClient)
    {
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->httpClient = $httpClient;
        $this->cache = new FilesystemAdapter;
    }

    private function getAccessToken(): string
    {
        return $this->cache->get('routmoute-sirene-token', function(ItemInterface $item) {
            $response = $this->httpClient->request("POST", self::AUTH_URL, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->consumer_key . ':' . $this->consumer_secret)
                ],
                'body' => [
                    'grant_type' => 'client_credentials'
                ]
            ]);
    
            if ($response->getStatusCode() != 200) {
                throw new SireneAuthFailedException('Authentication failed.', 403);
            }
    
            $datas = $response->toArray();

            $item->expiresAfter($datas['expires_in']);

            return $datas['token_type'] . ' ' . $datas['access_token'];
        });
    }

    private function checkStatusCode($statusCode)
    {
        if ($statusCode != 200) {
            switch ($statusCode) {
                case 301:
                    throw new SireneUnitClosedException('Legal unit closed due to duplication.', $statusCode);
                case 400:
                    throw new SireneMisformattedParametersException('Incorrect number of parameters or parameters are incorrectly formatted.', $statusCode);
                case 401:
                    throw new SireneInvalidTokenException('Invalid access token.', $statusCode);
                case 403:
                    throw new SireneInvalidPermissionsException('Insufficient rights to view data from this unit.', $statusCode);
                case 404:
                    throw new SireneNotExistException('Company not found in the Sirene database.', $statusCode);
                case 429:
                    throw new SireneMaxQuotaException('API query quota exceeded.', $statusCode);
                case 500:
                    throw new SireneServerErrorException('Sirene API Internal Server Error.', $statusCode);
                case 503:
                    throw new SireneServiceUnavailableException('Service unavailable.', $statusCode);
                default:
                    throw new SireneUnknownException('Unknown error.', $statusCode);
            }
        }
    }

    public function siret(string $siret): array
    {
        if (strlen($siret) != 14) {
            throw new SireneMisformattedParametersException('Incorrect number of parameters or parameters are incorrectly formatted.', 400);
        }

        $response = $this->httpClient->request("GET", self::API_URL . '/siret/' . $siret, [
            'headers' => [
                'Authorization' => $this->getAccessToken(),
                'Accept' => 'application/json'
            ]
        ]);

        $this->checkStatusCode($response->getStatusCode());

        return $response->toArray()['etablissement'];
    }

    public function siren(string $siren): array
    {
        if (strlen($siren) != 9) {
            throw new SireneMisformattedParametersException('Incorrect number of parameters or parameters are incorrectly formatted.', 400);
        }

        $response = $this->httpClient->request("GET", self::API_URL . '/siren/' . $siren, [
            'headers' => [
                'Authorization' => $this->getAccessToken(),
                'Accept' => 'application/json'
            ]
        ]);

        $this->checkStatusCode($response->getStatusCode());

        return $response->toArray()['uniteLegale'];
    }

    public function searchEtablissement(array $params, string $tri = "siren", int $page = 1, int $nombre = 20): array
    {
        $list = [
            "city" => "libelleCommuneEtablissement",
            "cp" => "codePostalEtablissement",
            "company" => "denominationUniteLegale",
            "sigle" => "sigleUniteLegale",
            "ape" => "activitePrincipaleUniteLegale",
            "cj" => "categorieJuridiqueUniteLegale"
        ];

        if (empty($params)) {
            throw new SireneEmptyParamsException('Array of params is empty.', 400);
        }

        $data = "";
        foreach ($params as $k => $v) {
            if (array_key_exists($k, $list)) {
                $data .= $list[$k].":".$v." AND ";
                unset($params[$k]);
            }
        }
        $data = urlencode(substr($data, 0, -5));
        $paramsTri = "&debut=".$page."&nombre=".$nombre."&tri=".$tri;

        $response = $this->httpClient->request("GET", self::API_URL . '/siret/?q=' . $data . $paramsTri, [
            'headers' => [
                'Authorization' => $this->getAccessToken(),
                'Accept' => 'application/json'
            ]
        ]);

        $this->checkStatusCode($response->getStatusCode());

        return $response->toArray()['etablissements'];
    }
}
