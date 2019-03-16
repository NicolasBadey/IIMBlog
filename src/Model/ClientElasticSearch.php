<?php
namespace App\Model;

use App\Logger\ElasticsearchLogger;
use Elasticsearch\ClientBuilder;

class ClientElasticSearch
{
    /**
     * @var \Elasticsearch\Client
     */
    private $client;
    /**
     * @var ElasticsearchLogger
     */
    private $logger;

    /**
     * Client constructor.
     * @param array $elasticsearch_config
     * @param ElasticsearchLogger $logger
     */
    public function __construct(array $elasticsearch_config, ElasticsearchLogger $logger)
    {
        $this->logger = $logger;
        $this->client = ClientBuilder::create()
            ->setHosts($elasticsearch_config['hosts'])
            ->setLogger($logger)
            ->build();
    }

    /**
     * @param $params
     * @return array
     */
    public function index(array $params, string $index, string $type): array
    {
        $paramsIndex = $this->prepareForIndex($params, $index, $type);

        $data = $this->client->index($paramsIndex);
        $this->logRequestInfo();

        return $data;
    }

    /**
     * @param $params
     * @return array
     */
    public function delete(array $params): array
    {
        $data = $this->client->delete($params);
        $this->logRequestInfo();

        return $data;
    }

    /**
     * @param array $params
     * @param string $type
     * @return array
     */
    public function bulk(array $params, string $index, string $type): array
    {
        $paramsIndex = $this->prepareForBulkIndex($params, $index, $type);

        $data = $this->client->bulk($paramsIndex);
        $this->logRequestInfo();

        return $data;
    }

    public function prepareForBulkIndex(array $params, string $index, string $type): array
    {
        $paramsIndex = [];

        foreach ($params as $param) {
            $paramsIndex['body'][] = [
                'index' => [
                    '_index' => $index,
                    '_type' => $type,
                    '_id' => $param['id'],
                ]
            ];

            unset($param['id']);
            $paramsIndex['body'][] = $param;
        }

        return $paramsIndex;
    }

    public function prepareForIndex(array $param, string $index, string $type): array
    {
        $paramIndex= [
            'index' => $index,
            'type' => $type,
            'id' => $param['id'],
        ];

        unset($param['id']);
        $paramIndex['body'] = $param;

        return $paramIndex;
    }


    /**
     * @param $params
     * @return array
     */
    public function search(array $params): array
    {
        $data = $this->client->search($params);
        $this->logRequestInfo();

        return $data;
    }
        

    /**
     * @param $params
     * @return array
     */
    public function suggest(array $params): array
    {
        $data = $this->client->suggest($params);
        $this->logRequestInfo();

        return $data;
    }

    /**
     * @return \Elasticsearch\Namespaces\ClusterNamespace
     */
    public function cluster()
    {
        $data = $this->client->cluster();
        $this->logRequestInfo();

        return $data;
    }

    private function logRequestInfo()
    {
        $info = $this->client->transport->getConnection()->getLastRequestInfo();

        $this->logger->logQuery(
            $info['request']['uri'],
            $info['request']['http_method'],
            $info['request']['body'],
            $info['response']['transfer_stats']['total_time'],
            [
                'method' => $info['request']['scheme'],
                'transport' => $info['request']['scheme'],
                'host' => explode(':', $this->client->transport->getConnection()->getHost())[0],
                'port' => '',
            ]
        );
    }

    /**
     * @param $params
     * @return array|bool
     */
    public function exists($params)
    {
        return $this->client->exists($params);
    }

    /**
     * @return \Elasticsearch\Namespaces\IndicesNamespace
     */
    public function indices()
    {
        return $this->client->indices();
    }
}
