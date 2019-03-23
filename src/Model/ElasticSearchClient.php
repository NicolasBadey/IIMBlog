<?php
namespace App\Model;

use App\Logger\ElasticsearchLogger;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Namespaces\IndicesNamespace;
use ElasticsearchETL\ElasticsearchClientInterface;

/**
 * Class ElasticSearchClient
 * @package App\Model
 *
 * Layer on top of elasticsearch-php ClientBuilder for service and inject Symfony logger
 */
class ElasticSearchClient implements ElasticsearchClientInterface
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
    public function index(array $params): array
    {
        $data = $this->client->index($params);
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
    public function bulk(array $params = []): array
    {
        $data = $this->client->bulk($params);
        $this->logRequestInfo();

        return $data;
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
        $this->logger->logQuery($this->client->transport->getConnection()->getLastRequestInfo());
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
     * @param $params
     * @return array
     */
    public function count($params)
    {
        return $this->client->count($params);
    }

    /**
     * @param $params
     * @return array
     */
    public function refresh($params)
    {
        return $this->client->indices()->refresh($params);
    }

    /**
     * @return \Elasticsearch\Namespaces\IndicesNamespace
     */
    public function indices(): IndicesNamespace
    {
        return $this->client->indices();
    }

    public function getIndexNameFromAlias(string $alias): array
    {
        $aliaseInfo = $this->client->indices()->getAlias([
            'name' => $alias
        ]);
        return array_keys($aliaseInfo);
    }
}
