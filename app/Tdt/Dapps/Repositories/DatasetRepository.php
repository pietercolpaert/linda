<?php namespace Tdt\Dapps\Repositories;

class DatasetRepository
{
    /**
     * Get all of the datasets
     *
     * @param integer $limit  The limit of the amount of returned datasets
     * @param integer $offset The offset of the applied limit
     *
     * @return array
     */
    public function getAll($limit, $offset)
    {

    }

    /**
     * Get a certain dataset
     *
     * @param string $title The title of the dataset
     *
     * @return EasyRdf_Resource
     */
    public function get($title)
    {

    }

    /**
     * Create a new dataset
     *
     * @param array $config The config of the new dataset
     *
     * @return string (title)
     */
    public function add($config)
    {

    }

    /**
     * Return validation rules for a dataset
     *
     * @return array
     */
    public function getValidator()
    {
        return [
            'title' => 'required'
        ];
    }

    public function getFields()
    {
        return [
            'title' => [
                'sem_uri' => 'http://purl.org/dc/terms/',
                'type' => 'string',
                'view_name' => 'Title',
                'required' => true,
                'description' => 'The title of the dataset, will act as the unique name of the dataset.',
                'domain' => 'dcat:Dataset'
            ],
            'description' => [
                'sem_uri' => 'http://purl.org/dc/terms/',
                'required' => true,
                'type' => 'string',
                'view_name' => 'Description',
                'description' => 'The description of the dataset.',
                'domain' => 'dcat:Dataset'
            ],
            'comment' => [
                'sem_uri' => 'http://purl.org/dc/terms/',
                'required' => false,
                'type' => 'text',
                'view_name' => 'Comment',
                'description' => 'Additional comments on the dataset.',
                'domain' => 'dcat:Dataset'
            ],
            'score' => [
                'sem_uri' => 'http://ns1.org/',
                'required' => false,
                'type' => 'enumeration',
                'values' => 'red|orange|green',
                'view_name' => 'Score',
                'description' => 'The score of a dataset.',
                'domain' => 'dcat:Dataset'
            ],
            'recommendation' => [
                'sem_uri' => 'http://ns1.org/',
                'required' => false,
                'type' => 'text',
                'view_name' => 'Recommendation',
                'description' => 'Small recommendations made by the researchers to make the dataset better.',
                'domain' => 'dcat:Dataset'
            ],
            'rights' => [
                'sem_uri' => 'http://purl.org/dc/terms/',
                'required' => false,
                'type' => 'list',
                'values' => 'https://github.com/tdt/licenses.json',
                'view_name' => 'Rights',
                'description' => 'The link to the license that rests on the dataset.',
                'domain' => 'dcat:Distribution'
            ],
            'useFor' => [
                'sem_uri' => 'http://ns1.org/',
                'required' => false,
                'type' => 'string',
                'view_name' => 'Usage',
                'description' => 'Links to certain applicable domains',
                'domain' => 'dcat:Distribution'
            ],
            'creator' => [
                'sem_uri' => 'http://purl.org/dc/terms/',
                'required' => false,
                'type' => 'string',
                'view_name' => 'Creator',
                'description' => '',
                'domain' => 'dcat:CatalogRecord'
            ],
            'contributor' => [
                'sem_uri' => 'http://purl.org/dc/terms/',
                'required' => false,
                'type' => 'string',
                'view_name' => 'Contributor',
                'description' => 'Which researchers contributed to the meta-data of the dataset.',
                'domain' => 'dcat:CatalogRecord'
            ],
        ];
    }
}
