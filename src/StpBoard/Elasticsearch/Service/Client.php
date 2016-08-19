<?php

namespace StpBoard\Elasticsearch\Service;

use StpBoard\Elasticsearch\Exception\ElasticsearchException;

class Client
{
    /**
     * @param string $url
     * @param array $parameters
     *
     * @return array
     * @throws ElasticsearchException
     */
    public function getJSON($url, $parameters)
    {
        return $this->parseJSON($this->request($url, $parameters));
    }

    /**
     * @param string $url
     * @param array $parameters
     *
     * @return string
     */
    protected function request($url, $parameters)
    {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, sprintf('%s?%s', $url, http_build_query($parameters)));
        $data = curl_exec($curlHandle);

        curl_close($curlHandle);

        return $data;
    }

    /**
     * @param string $data
     *
     * @return array
     * @throws ElasticsearchException
     */
    protected function parseJSON($data)
    {
        if ($data === false) {
            throw new ElasticsearchException('Can not get data from Elasticsearch monitor');
        }

        $data = json_decode($data, true);
        if ($data === null) {
            throw new ElasticsearchException('Can not parse response from Elasticsearch monitor');
        }

        return $data;
    }
}
