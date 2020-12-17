<?php


namespace ChameleonSystem\GraphQLBundle\Service;


use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\Inflector\Inflector;
use Twig\Environment;

final class SchemaCreator
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var Connection
     */
    private $db;

    public function __construct(Environment $twig, Connection $db)
    {
        $this->twig = $twig;
        $this->db = $db;
    }

    /**
     * createSchema
     * @return GraphQLElement[]
     * @throws \Exception
     */
    public function createSchema(): array
    {
        $list = \TdbCmsTblConfList::GetList();
        $list->AddFilterString("`graphql_access` = '1'");
        $list->AddFilterString("`graphql_name` != ''");

        $elements = [];

        while (($item = $list->Next()) !== false) {
            $element = new GraphQLElement();
            $element->name = $item->fieldGraphqlName;
            $element->name_plural = Inflector::pluralize($item->fieldGraphqlName);
            $element->type = ucwords($item->fieldGraphqlName);
            $element->table = $item->fieldName;

            $element->displayFields[] = new GraphQLField("id", "id");
            $element->queryFieldsUnique[] = new GraphQLField("id", "id");
            $element->queryFields = [];

            $displayFieldList = $item->GetFieldCmsFieldConfMltList();
            $displayFieldList->AddFilterString("`graphql_display` = '1'");
            while (($field = $displayFieldList->next()) !== false) {
                $graphQLField = new GraphQLField($field->fieldName, $field->fieldName);

                // todo: handle other references
                if (in_array($field->GetFieldType()->fieldConstname, ['CMSFIELD_EXTENDEDTABLELIST', 'CMSFIELD_TABLELIST'])) {
                    // todo: handle configured parent table
                    $graphQLField->ref = substr($field->fieldName, 0, -3);
                    $graphQLField->name = substr($field->fieldName, 0, -3);
                } else {
                    $graphQLField->type = "String";
                }

                $element->displayFields[] = $graphQLField;
            }
            $queryFieldList = $item->GetFieldCmsFieldConfMltList();
            $queryFieldList->AddFilterString("`graphql_query` = '1'");
            while (($field = $queryFieldList->next()) !== false) {
                $element->queryFields[] = new GraphQLField($field->fieldName, $field->fieldName);
            }

            $elements[] = $element;
        }

        // resolve ref field types
        foreach ($elements as $element) {
            foreach ($element->displayFields as $field) {
                if ($field->ref === null) {
                    continue;
                }
                foreach ($elements as $outerElement) {
                    if ($outerElement->table === $field->ref) {
                        $field->type = $outerElement->type;
                        break;
                    }
                }
                if ($field->type === "String") {
                    throw new \Exception("Field reference not in schema: " . $field->name);
                }
            }
        }

        return $elements;
    }

    public function createSchemaFile()
    {
        $elements = $this->createSchema();

        return $this->twig->render("@ChameleonSystemGraphQLBundle/Resources/templates/schema.twig", [
            "elements" => $elements
        ]);
    }

}