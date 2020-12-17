<?php


namespace ChameleonSystem\GraphQLBundle\Service;


final class GraphQLField
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $fieldName;

    /**
     * @var string
     */
    public $ref;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $mltRef;

    public function __construct(string $name, string $fieldName, string $type = "String")
    {
        $this->name = $name;
        $this->fieldName = $fieldName;
        $this->type = $type;
    }


}