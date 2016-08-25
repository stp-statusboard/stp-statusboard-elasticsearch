<?php

namespace StpBoard\Elasticsearch\Service;

use DateTime;
use DateTimeZone;

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
                'metric' => $config['metric'],
                'index' => $config['index'],
            ]
        );

        $currentDate = new DateTime(null, new DateTimeZone('Europe/Warsaw'));

        $result = [];
        foreach ($data as $singleStat) {
            $measuredAt = new DateTime($singleStat['measured_at']);

            $result[] = [
                'x' => 1000 * ($measuredAt->getTimestamp() + $currentDate->getOffset()),
                'y' => $singleStat['value'],
            ];
        }

        return $result;
    }
}
