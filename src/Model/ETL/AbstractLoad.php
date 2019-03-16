<?php

namespace App\Model\ETL;

use App\Model\ClientElasticSearch;

abstract class AbstractLoad
{
    /**
     * @var ClientElasticSearch
     */
    protected $client;
    
    protected $index;

    public function __construct(ClientElasticSearch $client)
    {
        $this->client = $client;
    }

    abstract public function getMappingProperties();
    
    abstract public function getAlias();

    protected function getMapping() :array
    {
        // if you are multi language use : https://www.elastic.co/guide/en/elasticsearch/guide/current/mixed-lang-fields.html

        return [
            'index' => $this->getIndex(),
            'type' => $this->getAlias(),
            'body' => [
                $this->getAlias() => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => $this->getMappingProperties(),
                ]
            ]
        ];
    }

    protected function invertAlias()
    {
        $this->client->indices()->updateAliases([
            'body'=> [
                'actions' => [
                    [
                        'remove' => [
                            'index' => '*',
                            'alias' => $this->getAlias()
                        ]
                    ],
                    [
                        'add' => [
                            'index' => $this->getIndex(),
                            'alias' => $this->getAlias()
                        ]
                    ]
                ]
            ]
        ]);
    }

    protected function deleteUnusedIndices()
    {
        $response = $this->client->indices()->getMapping();
        $indices = array_keys($response);

        foreach ($indices as $key => $existingIndex) {
            //only if it's not the current index and not a 3rd party index
            if ($existingIndex !== $this->getIndex() && 0 === strpos($existingIndex, $this->getAlias())) {
                $this->client->indices()->delete([
                    'index' => $existingIndex
                ]);
            }
        }
    }
    
    public function getIndex()
    {
        if (null === $this->index) {
            $this->index = $this->getAlias().'_'.(new \DateTime())->format('U');
        }
        
        return $this->index;
    }

    public function preLoad()
    {
        $this->client->indices()->create([
            'index' => $this->getIndex()
        ]);

        $this->client->indices()->putMapping($this->getMapping());
    }

    public function postLoad()
    {
        $this->invertAlias();
        $this->deleteUnusedIndices();
    }

    public function bulkLoad(array $data)
    {
        return $this->client->bulk($data, $this->getIndex(), $this->getAlias());
    }

    public function singleLoad(array $data)
    {
        return $this->client->index($data, $this->getAlias(), $this->getAlias());
    }
}
