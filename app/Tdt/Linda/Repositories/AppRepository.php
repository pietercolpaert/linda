<?php namespace Tdt\Linda\Repositories;

use \ML\JsonLD\JsonLD;

class AppRepository
{
    /**
     * Get all of the datasets in the form of EasyRdf_Resource's
     *
     * @param integer $limit  The limit of the amount of returned datasets
     * @param integer $offset The offset of the applied limit
     *
     * @return array
     */
    public function getAll($limit, $offset)
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
        $uri = \URL::to('/' . $id);

        $collection = $this->getMongoCollection();

        $cursor = $collection->find(
            ['@id' => $uri]
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
        \EasyRdf_Namespace::set('odapps', 'http://semweb.mmlab.be/ns/odapps#');

        // Create a auto-generated subject URI
        $id = $this->getIncrementalId();

        $uri = \URL::to('/' . $id);

        $context = $this->getContext();

        // Add the dataset resource

        $graph = new \EasyRdf_Graph();
        $dataset = $graph->resource($uri . '#application');
        $dataset->addType('odapps:Application');

        foreach ($this->getFields() as $field) {
            if ($field['domain'] == 'odapps:Application') {
                if ($field['single_value'] && in_array($field['type'], ['string', 'text', 'list'])) {

                    $graph->addLiteral($dataset, $field['sem_term'], trim($config[$field['var_name']]));

                } else if (!$field['single_value'] && in_array($field['type'], ['string', 'list'])) {

                    if (!empty($config[$field['var_name']])) {
                        foreach ($config[$field['var_name']] as $val) {
                            $graph->addLiteral($dataset, $field['sem_term'], $val);
                        }
                    }
                }
            }
        }

        $serializer = new \EasyRdf_Serialiser_JsonLd();

        $jsonld = $serializer->serialise($graph, 'jsonld');

        $compact_document = (array)JsonLD::compact($jsonld, $context);

        // Add the datarecord resource

        /*$datarecord = $graph->resource($uri);
        $datarecord->addType('dcat:CatalogRecord');

        $created = time();

        $datarecord->addLiteral('http://purl.org/dc/terms/issued', $created);
        $datarecord->addLiteral('http://purl.org/dc/terms/modified', $created);
        $datarecord->addLiteral('http://purl.org/dc/terms/creator', \URL::to('/user/' . $config['user']));

        foreach ($this->getFields() as $field) {
            if ($field['domain'] == 'dcat:CatalogRecord') {
                if ($field['single_value'] && in_array($field['type'], ['string', 'text', 'list'])) {

                    $graph->addLiteral($datarecord, $field['sem_term'], trim($config[$field['var_name']]));

                } else if (!$field['single_value'] && in_array($field['type'], ['string', 'list'])) {

                    if (!empty($config[$field['var_name']])) {
                        foreach ($config[$field['var_name']] as $val) {
                            $graph->addLiteral($datarecord, $field['sem_term'], $val);
                        }
                    }
                }
            }
        }

        // Add the relationship with the dataset
        $graph->addResource($datarecord, 'http://xmlns.com/foaf/spec/primaryTopic', $uri . '#dataset');

        // Add the distribution resource
        $distribution = $graph->resource($uri . '#distribution');
        $distribution->addType('dcat:Distribution');

        foreach ($this->getFields() as $field) {
            if ($field['domain'] == 'dcat:Distribution') {
                if (in_array($field['type'], ['string', 'text', 'list'])) {
                    $graph->addLiteral($distribution, $field['sem_term'], trim($config[$field['var_name']]));
                }
            }
        }

        // Add the distribution to the dataset
        $graph->addResource($dataset, 'dcat:distribution', $uri . '#distribution');*/

        $serializer = new \EasyRdf_Serialiser_JsonLd();

        $jsonld = $serializer->serialise($graph, 'jsonld');

        $compact_document = (array)JsonLD::compact($jsonld, $context);

        $collection = $this->getMongoCollection();
        $collection->insert($compact_document);
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
        $uri = \URL::to('/' . $id);

        // Find the graph in the collection
        $graph = $this->get($id . '#application');

        $context = $this->getContext();

        if (empty($graph)) {
            return null;
        }

        foreach ($this->getFields() as $field) {

            $domain = $field['domain'];

            if ($domain == 'odapps:Application') {
                $resource = $graph->resource($uri . "#application");

                $graph->delete($resource, $field['short_sem_term']);

                if ($field['single_value'] && in_array($field['type'], ['string', 'text', 'list'])) {

                    $graph->addLiteral($resource, $field['sem_term'], trim($config[$field['var_name']]));

                } else if (!$field['single_value'] && in_array($field['type'], ['string', 'list'])) {

                    if (!empty($config[$field['var_name']])) {
                        foreach ($config[$field['var_name']] as $val) {
                            $graph->addLiteral($resource, $field['sem_term'], $val);
                        }
                    }
                }
            }

        }

        // Delete the json entry and replace it with the updated one
        $collection = $this->getMongoCollection();

        $serializer = new \EasyRdf_Serialiser_JsonLd();

        $jsonld = $serializer->serialise($graph, 'jsonld');

        $compact_document = (array)JsonLD::compact($jsonld, $context);

        $collection->remove([
            '@id' => $uri . '#application'
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

        $mongoCollection = $client->selectCollection(\Config::get('database.connections.mongodb.database'), 'apps');

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
            'description' => 'required',
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

        $uri = \URL::to('/' . $id);

        $collection->remove([
            '@id' => $uri
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
                'description' => 'The title of the app, will act as the unique name.',
                'domain' => 'odapps:Application',
                'single_value' => true,
            ],
            [
                'var_name' => 'description',
                'sem_term' => 'http://purl.org/dc/terms/description',
                'short_sem_term' => 'dc:description',
                'required' => true,
                'type' => 'text',
                'view_name' => 'Description',
                'description' => 'The description of the app.',
                'domain' => 'odapps:Application',
                'single_value' => true,
            ],
            [
                'var_name' => 'consumes',
                'sem_term' => 'http://semweb.mmlab.be/ns/odapps#consumes',
                'short_sem_term' => 'odapps:consumes',
                'required' => false,
                'type' => 'list',
                'values' => \URL::to('lists/datasets'),
                'key_name' => 'name',
                'value_name' => 'url',
                'view_name' => 'Consumes',
                'description' => 'What datasets does the app consume.',
                'domain' => 'odapps:Application',
                'single_value' => false,
            ],
            [
                'var_name' => 'creator',
                'sem_term' => 'http://purl.org/dc/terms/creator',
                'short_sem_term' => 'dc:creator',
                'required' => true,
                'type' => 'list',
                'values' => \URL::to('lists/users'),
                'key_name' => 'name',
                'value_name' => 'url',
                'view_name' => 'Creator',
                'description' => 'Name of the creator of the application.',
                'domain' => 'odapps:Application',
                'single_value' => true,
            ],
            [
                'var_name' => 'contributor',
                'sem_term' => 'http://purl.org/dc/terms/contributor',
                'short_sem_term' => 'dc:contributor',
                'required' => false,
                'type' => 'list',
                'values' => \URL::to('lists/users'),
                'key_name' => 'name',
                'value_name' => 'url',
                'view_name' => 'Contributor',
                'description' => 'Name of the contributors of the application.',
                'domain' => 'odapps:Application',
                'single_value' => true,
            ],
            [
                'var_name' => 'app_type',
                'sem_term' => 'http://purl.org/dc/terms/type',
                'short_sem_term' => 'dc:type',
                'required' => false,
                'type' => 'list',
                'values' => \URL::to('lists/apps'),
                'key_name' => 'name',
                'value_name' => 'url',
                'view_name' => 'Type',
                'description' => 'Type of the app.',
                'domain' => 'odapps:Application',
                'single_value' => true,
            ],
            [
                'var_name' => 'spatial',
                'sem_term' => 'http://purl.org/dc/terms/spatial',
                'short_sem_term' => 'dc:spatial',
                'required' => false,
                'type' => 'list',
                'values' => \URL::to('lists/geo'),
                'key_name' => 'name',
                'value_name' => 'url',
                'view_name' => 'Spatial',
                'description' => 'Spatial relevance of the app.',
                'domain' => 'odapps:Application',
                'single_value' => true,
            ],
        ];
    }
}
