<?php


namespace ChameleonSystem\GraphQLBundle\Service;


final class GraphQLElement
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $name_plural;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $table;

    /**
     * @var array
     */
    public $queryFields = [];

    /**
     * @var GraphQLField[]
     */
    public $queryFieldsUnique = [];

    /**
     * @var GraphQLField[]
     */
    public $displayFields = [];

}