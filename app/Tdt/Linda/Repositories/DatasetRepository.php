<?php namespace Tdt\Linda\Repositories;

use \ML\JsonLD\JsonLD;

class DatasetRepository
{
    /**
     * Get all of the datasets in the form of EasyRdf_Resource's
     *
     * @param integer $limit  The limit of the amount of returned datasets
     * @param integer $offset The offset of the applied limit
     *
     * @return array
     */
    public function getAll($limit = 1000, $offset = 0)
    {
        $collection = $this->getMongoCollection();

        $cursor = $collection->find([]);

        $results = [];

        foreach ($cursor as $element) {
            unset($element['_id']);

            $expand = JsonLD::expand(json_encode($element));

            $element = (array)$expand;
            $graph = new \EasyRdf_Graph();

            $json_ld = json_encode($element);

            $graph->parse($json_ld, 'jsonld');

            $results[] = $graph;
        }

        return $results;
    }

    /**
     * Get a certain dataset
     *
     * @param string $id The id of the dataset
     *
     * @return EasyRdf_Resource
     */
    public function get($id)
    {
        $uri = \URL::to('/datasets/' . $id);

        $collection = $this->getMongoCollection();

        $cursor = $collection->find(
            [
                '@graph' => ['$elemMatch' => [
                        '@id' => $uri
                    ]
                ]
            ]
        );

        if ($cursor->hasNext()) {
            $jsonLd = $cursor->getNext();
            unset($jsonLd['_id']);

            $expand = JsonLD::expand(json_encode($jsonLd));

            $jsonLd = (array)$expand;
            $graph = new \EasyRdf_Graph();

            $json_ld = json_encode($jsonLd);

            $graph->parse($json_ld, 'jsonld');

            return $graph;

        } else {
            return [];
        }
    }

    /**
     * Create a new dataset
     *
     * @param array $config The config of the new dataset
     *
     * @return void
     */
    public function add($config)
    {
        // Create a auto-generated subject URI
        $id = $this->getIncrementalId();

        $uri = \URL::to('/datasets/' . $id);

        $context = $this->getContext();

        $graph = $this->createGraph($uri, $config);

        $serializer = new \EasyRdf_Serialiser_JsonLd();

        $jsonld = $serializer->serialise($graph, 'jsonld');

        $compact_document = (array)JsonLD::compact($jsonld, $context);

        $collection = $this->getMongoCollection();
        $collection->insert($compact_document);
    }

    /**
     * Create a new graph
     *
     * @param $uri    string The URI of the dataset
     * @param $config array  The configuration that makes up the new graph
     *
     * @return \EasyRdf_Graph
     */
    private function createGraph($uri, $config)
    {
        $graph = new \EasyRdf_Graph();
        $dataset = $graph->resource($uri . '#dataset');
        $dataset->addType('dcat:Dataset');

        foreach ($this->getFields() as $field) {
            if ($field['domain'] == 'dcat:Dataset') {
                if ($field['single_value'] && in_array($field['type'], ['string', 'text', 'list'])) {
                    $graph->add($dataset, $field['sem_term'], trim($config[$field['var_name']]));

                } elseif (!$field['single_value'] && in_array($field['type'], ['string', 'list'])) {
                    if (!empty($config[$field['var_name']])) {
                        foreach ($config[$field['var_name']] as $val) {
                            $graph->add($dataset, $field['sem_term'], $val);
                        }
                    }
                }
            }
        }

        // Add the datarecord resource

        $datarecord = $graph->resource($uri);
        $datarecord->addType('dcat:CatalogRecord');

        $created = time();

        $datarecord->addLiteral('http://purl.org/dc/terms/issued', date('c', $created));
        $datarecord->addLiteral('http://purl.org/dc/terms/modified', date('c', $created));
        $datarecord->addResource('http://purl.org/dc/terms/creator', \URL::to('/users/' . strtolower(str_replace(" ", "", $config['user']))));

        foreach ($this->getFields() as $field) {
            if ($field['domain'] == 'dcat:CatalogRecord') {
                if ($field['single_value'] && in_array($field['type'], ['string', 'text', 'list'])) {
                    if (filter_var(trim($config[$field['var_name']]), FILTER_VALIDATE_URL)) {
                        $graph->addResource($datarecord, $field['sem_term'], trim($config[$field['var_name']]));
                    } else {
                        $graph->add($datarecord, $field['sem_term'], trim($config[$field['var_name']]));
                    }
                } elseif (!$field['single_value'] && in_array($field['type'], ['string', 'list'])) {
                    if (!empty($config[$field['var_name']])) {
                        foreach ($config[$field['var_name']] as $val) {
                            if (filter_var($val, FILTER_VALIDATE_URL)) {
                                $graph->addResource($datarecord, $field['sem_term'], $val);
                            } else {
                                $graph->add($datarecord, $field['sem_term'], $val);
                            }
                        }
                    }
                }
            }
        }

        // Add the relationship with the dataset
        $graph->addResource($datarecord, 'http://xmlns.com/foaf/spec/primaryTopic', $uri . '#dataset');

        // Add the distribution resource
        foreach ($config['distributions'] as $distribution) {
            $id = $this->getIncrementalId();

            $distr_uri = $uri . '#distribution' . $id;

            $distributionResource = $graph->resource($distr_uri);
            $distributionResource->addType('dcat:Distribution');

            $urls = ['license', 'accessURL', 'downloadURL'];

            foreach ($urls as $url) {
                if (!empty($distribution[$url]) && filter_var($distribution[$url], FILTER_VALIDATE_URL)) {
                    if ($url == 'license') {
                        $graph->addResource($distributionResource, 'dct:' . $url, $distribution[$url]);
                    } else {
                        $graph->addResource($distributionResource, 'dcat:' . $url, $distribution[$url]);
                    }
                }
            }

            if (!empty($distribution['distributionTitle'])) {
                $graph->addLiteral($distributionResource, 'dct:title', $distribution['distributionTitle']);
            }

            // Add the distribution to the dataset
            $graph->addResource($dataset, 'dcat:distribution', $distr_uri);
        }

        return $graph;
    }

    /**
     * Update a dataset
     *
     * @param $id     integer The id of the dataset
     * @param $config array   The configuration that makes up the dataset
     *
     * @return void
     */
    public function update($id, $config)
    {
        $uri = \URL::to('/datasets/' . $id);

        // Find the graph in the collection
        $graph = $this->get($id);

        $context = $this->getContext();

        if (empty($graph)) {
            return null;
        }

        $newGraph = $this->createGraph($uri, $config);

        // Add the contributor
        $newGraph->addResource($uri, 'http://purl.org/dc/terms/contributor', \URL::to('/users/' . strtolower(str_replace(" ", "", $config['user']))));

        // Adjust the modifier timestamp
        $newGraph->delete($uri, 'http://purl.org/dc/terms/issued');

        $newGraph->addLiteral($uri, 'http://purl.org/dc/terms/issued', $graph->getLiteral($uri, 'dc:issued')->getValue());
        $newGraph->addLiteral($uri, 'http://purl.org/dc/terms/modified', date('c'));

        $newGraph->delete($uri, 'http://purl.org/dc/terms/creator');

        $newGraph->addResource($uri, 'http://purl.org/dc/terms/creator', $graph->getResource($uri, 'dc:creator')->getUri());

        $contributors = $graph->all($uri, 'dc:contributor');

        foreach ($contributors as $contributor) {
            $newGraph->addResource($uri, 'http://purl.org/dc/terms/contributor', $contributor->getUri());
        }

        // Delete the json entry and replace it with the updated one
        $collection = $this->getMongoCollection();

        $serializer = new \EasyRdf_Serialiser_JsonLd();

        $jsonld = $serializer->serialise($newGraph, 'jsonld');

        $compact_document = (array)JsonLD::compact($jsonld, $context);

        $collection->remove([
            '@graph' => [
                '$elemMatch' => [
                    '@id' => $uri
                ]
            ]
        ]);

        $collection->insert($compact_document);
    }

    private function getIncrementalId()
    {
        $result = \DB::table('incrementor')->select('*')->first();

        if (empty($result)) {
            $id = 1;
        } else {
            $id = $result->incremental_id;
        }

        $incrementedId = $id + 1;

        \DB::table('incrementor')->where('incremental_id', $id)->delete();
        \DB::table('incrementor')->insert(['incremental_id' => $incrementedId]);

        return $id;
    }

    private function getMongoCollection()
    {
        $connString = 'mongodb://' . \Config::get('database.connections.mongodb.host') . ':' . \Config::get('database.connections.mongodb.port');

        $client = new \MongoClient($connString);

        $mongoCollection = $client->selectCollection(\Config::get('database.connections.mongodb.database'), 'datasets');

        return $mongoCollection;
    }

    /**
     * Return validation rules for a dataset
     *
     * @return array
     */
    public function getRules()
    {
        return [
            'title' => 'required',
            'see_also' => 'multipleuri',
            'description' => 'required'
        ];
    }

    /**
     * Create and return the semantic context
     *
     * @return mixed
     */
    private function getContext()
    {
        $ns = \Prefix::all();

        $context = new \stdClass();
        $namespaces = new \stdClass();

        $context_keyword = '@context';

        foreach ($ns as $namespace) {
            $namespaces->$namespace['prefix'] = $namespace['uri'];
        }

        $context->$context_keyword = $namespaces;

        return $context;
    }

    /**
     * Delete an entry
     *
     * @param integer $id The id of the dataset
     *
     * return void
     */
    public function delete($id)
    {
        $collection = $this->getMongoCollection();

        $uri = \URL::to('/datasets/' . $id);

        $collection->remove([
            '@graph' => [
                '$elemMatch' => [
                    '@id' => $uri
                ]
            ]
        ]);
    }

    public function getFields()
    {
        return [
            [
                'var_name' => 'title',
                'sem_term' => 'http://purl.org/dc/terms/title',
                'short_sem_term' => 'dc:title',
                'type' => 'string',
                'view_name' => 'Title',
                'required' => true,
                'description' => 'The title of the dataset, will act as the unique name of the dataset.',
                'domain' => 'dcat:Dataset',
                'single_value' => true,
            ],
            [
                'var_name' => 'keywords',
                'sem_term' => 'http://www.w3.org/ns/dcat#keyword',
                'short_sem_term' => 'dcat:keyword',
                'type' => 'string',
                'view_name' => 'Keywords',
                'required' => false,
                'description' => 'A keyword or tag describing the dataset.',
                'domain' => 'dcat:Dataset',
                'single_value' => false,
            ],
            [
                'var_name' => 'description',
                'sem_term' => 'http://purl.org/dc/terms/description',
                'short_sem_term' => 'dc:description',
                'required' => true,
                'type' => 'text',
                'view_name' => 'Description',
                'description' => 'The description of the dataset.',
                'domain' => 'dcat:Dataset',
                'single_value' => true,
            ],
            [
                'var_name' => 'periodicity',
                'sem_term' => 'http://purl.org/dc/terms/accrualPeriodicity',
                'short_sem_term' => 'dc:accrualPeriodicity',
                'required' => false,
                'type' => 'list',
                'values' => \URL::to('lists/frequency'),
                'key_name' => 'name',
                'value_name' => 'url',
                'view_name' => 'Periodicity',
                'description' => 'The frequency with which items are added to a collection.',
                'domain' => 'dcat:Dataset',
                'single_value' => true,
            ],
        ];
    }
}
