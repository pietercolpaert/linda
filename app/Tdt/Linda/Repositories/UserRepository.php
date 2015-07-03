<?php namespace Tdt\Linda\Repositories;

use \ML\JsonLD\JsonLD;

class UserRepository
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
        // Create a auto-generated subject URI
        $id = $this->getIncrementalId();

        $uri = \URL::to('/' . $id);

        $context = $this->getContext();

        // Add the dataset resource

        $graph = new \EasyRdf_Graph();
        $dataset = $graph->resource($uri . '#agent');
        $dataset->addType('foaf:Agent');

        foreach ($this->getFields() as $field) {
            if ($field['domain'] == 'foaf:Agent') {
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
        $graph = $this->get($id . '#agent');

        $context = $this->getContext();

        if (empty($graph)) {
            return null;
        }

        foreach ($this->getFields() as $field) {

            $domain = $field['domain'];

            if ($domain == 'foaf:Agent') {
                $resource = $graph->resource($uri . "#agent");

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
            '@id' => $uri . '#agent'
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

        $mongoCollection = $client->selectCollection(\Config::get('database.connections.mongodb.database'), 'users');

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
            'name' => 'required',
            'mail' => 'email',
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

        $uri = \URL::to('/' . $id . '#agent');

        $collection->remove([
            '@id' => $uri
        ]);
    }

    public function getFields()
    {
        return [
            [
                'var_name' => 'name',
                'sem_term' => 'http://xmlns.com/foaf/0.1/name',
                'short_sem_term' => 'foaf:name',
                'type' => 'string',
                'view_name' => 'Name',
                'required' => true,
                'description' => 'The name of the person.',
                'domain' => 'foaf:Agent',
                'single_value' => true,
            ],
            [
                'var_name' => 'mail',
                'sem_term' => 'http://xmlns.com/foaf/0.1/mbox',
                'short_sem_term' => 'foaf:mbox',
                'required' => false,
                'type' => 'string',
                'view_name' => 'Email',
                'description' => 'The email adress of the person.',
                'domain' => 'foaf:Agent',
                'single_value' => true,
            ],
            [
                'var_name' => 'type',
                'sem_term' => 'http://purl.org/dc/terms/type',
                'short_sem_term' => 'dc:type',
                'required' => false,
                'type' => 'list',
                'values' => \URL::to('lists/agents'),
                'key_name' => 'name',
                'value_name' => 'url',
                'view_name' => 'Type',
                'description' => 'Type of the agent.',
                'domain' => 'foaf:Agent',
                'single_value' => true,
            ],
        ];
    }
}
