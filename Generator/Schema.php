<?php


namespace ChameleonSystem\GraphQLBundle\Generator;


use Doctrine\DBAL\Connection;

final class Schema
{
    /**
     * @var Connection
     */
    private $db;

    private $validFields = [
        'CMSFIELD_NUMBER' => 'Int',
        'CMSFIELD_STRING' => 'String',
        'CMSFIELD_PRICE' => 'Float'
    ];

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function schemaForTable(string $tableName): Type
    {
        $query = 'SELECT id FROM cms_tbl_conf WHERE `name`=:table_name';
        $tblConfId = $this->db->fetchColumn($query, ['table_name' => $tableName]);

        if (!$tblConfId) {
            throw new \InvalidArgumentException('Invalid table name');
        }

        $query = 'SELECT * FROM cms_field_type';
        $fieldTypes = $this->db->fetchAll($query);
        $fieldTypeMap = $this->mapFromFieldTypes($fieldTypes);

        $query = 'SELECT * FROM cms_field_conf WHERE cms_tbl_conf_id=:tbl_conf_id';
        $fields = $this->db->fetchAll($query, ['tbl_conf_id' => $tblConfId]);
        if (!$fields) {
            throw new \Exception('Something went wrong fetching fields for table');
        }

        $type = new Type($tableName);

        foreach ($fields as $field) {
            $dataType = $fieldTypeMap[$field['cms_field_type_id']];
            if (!array_key_exists($dataType, $this->validFields)) {
                continue;
            }

            $type->addField($field['name'], $this->validFields[$dataType]);
        }

        return $type;
    }

    private function mapFromFieldTypes(array $fieldTypes)
    {
        $map = [];
        foreach ($fieldTypes as $fieldType) {
            $map[$fieldType['id']] = $fieldType['constname'];
        }

        return $map;
    }
}
