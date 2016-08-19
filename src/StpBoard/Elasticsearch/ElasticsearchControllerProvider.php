<?php

namespace StpBoard\Elasticsearch;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use StpBoard\Base\BoardProviderInterface;
use StpBoard\Base\TwigTrait;
use StpBoard\Elasticsearch\Exception\ElasticsearchException;
use StpBoard\Elasticsearch\Service\Client;
use StpBoard\Elasticsearch\Service\ElasticsearchService;
use Symfony\Component\HttpFoundation\Request;

class ElasticsearchControllerProvider implements ControllerProviderInterface, BoardProviderInterface
{
    use TwigTrait;

    /**
     * @var ElasticsearchService
     */
    protected $elasticsearchService;

    /**
     * @var array
     */
    protected $allowedActions = [
        'search_average_time',
        'index_average_time',
        'storage_size',
        'document_count',
    ];

    /**
     * Returns route prefix, starting with "/"
     *
     * @return string
     */
    public static function getRoutePrefix()
    {
        return '/elasticsearch';
    }

    /**
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $this->elasticsearchService = new ElasticsearchService(new Client());

        $this->initTwig(__DIR__ . '/views');
        $controllers = $app['controllers_factory'];

        $controllers->get(
            '/',
            function (Application $app) {
                /** @var Request $request */
                $request = $app['request'];

                try {
                    $config = $this->getConfig($request);

                    $result = $this->elasticsearchService->fetchMetricForGraph($config);

                    return $this->twig->render(
                        $config['template'],
                        [
                            'name' => $config['name'],
                            'data' => $result,
                        ]
                    );
                } catch (ElasticsearchException $e) {
                    return $this->twig->render('error.html.twig', [
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        );

        return $controllers;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws ElasticsearchException
     */
    protected function getConfig(Request $request)
    {
        $name = $request->get('name');
        if (empty($name)) {
            throw new ElasticsearchException('Empty chart name');
        }

        $apiUrl = $request->get('apiUrl');
        if (empty($apiUrl)) {
            throw new ElasticsearchException('Empty apiUrl');
        }

        $index = $request->get('index');
        if (empty($index)) {
            throw new ElasticsearchException('Empty index');
        }

        $action = $request->get('action');
        if (!in_array($action, $this->allowedActions)) {
            throw new ElasticsearchException('Not allowed action');
        }

        $since = $request->get('since', 'now-3h/h');

        return [
            'name' => $name,
            'apiUrl' => $apiUrl,
            'index' => $index,
            'action' => $action,
            'template' => 'chart.html.twig',
            'since' => $since,
        ];
    }
}
