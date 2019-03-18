<?php

namespace App\Model\ETL;

use App\Model\ClientElasticSearch;

abstract class AbstractLoad
{
    /**
     * @var ClientElasticSearch
     */
    protected $client;

    /**
     * @var string
     *
     * Index Name
     */
    private $index;

    /**
     * @var bool
     *
     * Live mode allow indexing directly in the current alias' index if exists or by create a new index with an alias
     * The point is to show content as fast as possible without wait indexation's end
     * Basically it's the panic button when ES server has been reset directly in prod or for update index without mapping and deletion changes
     */
    private $live;

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

    public function setLiveMode(bool $live)
    {
        $this->live = $live;
    }

    public function getIndexNameFromAlias()
    {
        $aliaseInfo = $this->client->indices()->getAlias([
            'name' => $this->getAlias()
        ]);
        return array_keys($aliaseInfo)[0];
    }
    
    public function getIndex()
    {
        if (null === $this->index) {
            if ($this->live && $this->aliasExists()) {
                //in this case we want to populate current live index if already exists
                $this->index = $this->getIndexNameFromAlias();
            } else {
                $this->index = $this->getAlias().'_'.(new \DateTime())->format('U');
            }
        }

        return $this->index;
    }

    public function preLoad()
    {
        if (true === $this->live && true === $this->aliasExists()) {
            //in this case we ask to populate the live index and he already exists, nothing to do
            return;
        }

        $this->client->indices()->create([
            'index' => $this->getIndex()
        ]);

        $this->client->indices()->putMapping($this->getMapping());


        if (true === $this->live) {
            //in this case we ask to populate the live index but he d'ont exists, so we create and link the alias directly
            $this->invertAlias();
        }
    }

    public function postLoad()
    {
        if (true === $this->live) {
            //in this case we ask to populate the live index and he already exists, nothing to do
            return;
        }

        $this->invertAlias();
        $this->deleteUnusedIndices();
    }

    public function bulkLoad(array $data)
    {
        return $this->client->bulk($data, $this->getIndex(), $this->getAlias());
    }

    public function singleLoad(array $data)
    {
        if ($this->aliasExists()) {
            $this->client->index($data, $this->getAlias(), $this->getAlias());
        } else {
            $this->preLoad();

            $this->client->index($data, $this->getIndex(), $this->getAlias());

            $this->postLoad();
        }
    }

    public function deleteDocument(int $id): array
    {
        return $this->client->delete([
            'index' => $this->getAlias(),
            'type' => $this->getAlias(),
            'id' => $id
        ]);
    }

    /**
     * @return bool
     */
    public function aliasExists()
    {
        return $this->client->indices()->existsAlias([
            'name' => $this->getAlias()
        ]);
    }
}
