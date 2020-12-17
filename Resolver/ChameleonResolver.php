<?php

namespace AppBundle\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

namespace ChameleonSystem\GraphQLBundle\Resolver;

use ChameleonSystem\GraphQLBundle\Service\SchemaCreator;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Resolver\ResolverMap;
use Symfony\Component\Inflector\Inflector;

final class ChameleonResolver extends ResolverMap
{
    /**
     * @var SchemaCreator
     */
    private $schemaCreator;
    /**
     * @var Connection
     */
    private $db;


    public function __construct(SchemaCreator $schemaCreator, Connection $db)
    {
        $this->schemaCreator = $schemaCreator;
        $this->db = $db;
    }


    protected function map()
    {
        $schema = $this->schemaCreator->createSchema();
        $db = $this->db;
        $rootQuery = [
            self::RESOLVE_FIELD => static function ($value, Argument $args, \ArrayObject $context, ResolveInfo $info) use ($schema, $db) {
                // single entry
                foreach ($schema as $element) {
                    if ($element->name === $info->fieldName || $element->name === Inflector::singularize($info->fieldName)) {

                        $qb = $db->createQueryBuilder();
                        $qb->select('id')
                            ->from($element->table);

                        $argsCopy = $args->getArrayCopy();
                        if (count($argsCopy) > 0) {
                            $where = $qb->expr()->andX();
                            foreach ($argsCopy as $k => $v) {
                                $where->add($qb->expr()->like($k, $qb->expr()->literal(sprintf("%%%s%%", $v))));
                            }
                            $qb->where($where);
                        }

                        $sql = $qb->getSQL();
                        $result = $db->fetchAll($sql);

                        return array_map(function($row) {return ['id' => $row['id']];}, $result);
                    }
                }

                return [];
            }
        ];

        $resolvers = [
            'Query' => $rootQuery
        ];


        foreach ($schema as $element) {
            $resolvers[$element->type][self::RESOLVE_FIELD] = static function($value, Argument $args, \ArrayObject $context, ResolveInfo $info) use ($db, $element){

                if (isset($value[0][$info->fieldName]["id"])) {
                    return $value[0][$info->fieldName]["id"];
                }
                if (isset($value[0][$info->fieldName])) {
                    return $value[0][$info->fieldName];
                }

                $id = $value;

                // todo: what the heck.. find out how we get those different results
                if (is_array($id)) {
                    if (isset($id[0]["id"])) {
                        $id = $id[0]["id"];
                    }
                    if (isset($id["id"])) {
                        $id = $id["id"];
                    }
                }

                $cache_key = implode("_", array_slice($info->path, 0, -1)) . $id;
                try {
                    $cache = $context->offsetGet($cache_key);
                    return $cache[0][$info->fieldName];
                } catch (\ErrorException $e) {
                    // no cache entry
                }

                $qb = $db->createQueryBuilder();
                $qb
                    ->from($element->table)
                    ->select(array_map(function($f) {
                        if ($f->ref !== null) {
                            return sprintf("%s as %s", $f->fieldName, $f->name);
                        }
                        return $f->fieldName;
                    },
                        $element->displayFields
                    ))
                    ->where($qb->expr()->eq('id', $qb->expr()->literal($id)));

                $SQL = $qb->getSQL();
                $result = $db->fetchAll($SQL);

                $context->offsetSet($cache_key, $result);

                return $result[0][$info->fieldName];
            };

        }

        return $resolvers;
    }
}
