<?php

namespace StpBoard\Elasticsearch\Service;

use DateTime;

class ElasticsearchService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public function fetchMetricForGraph($config)
    {
        $data = $this->client->getJSON(
            $config['apiUrl'],
            [
                'since' => $config['since'],
                'action' => $config['action'],
                'index' => $config['index'],
            ]
        );

        $result = [];
        foreach ($data as $singleStat) {
            $result[] = [
                'x' => 1000 * (new DateTime($singleStat['measured_at']))->getTimestamp(),
                'y' => $singleStat['value'],
            ];
        }

        return $result;
    }
}
