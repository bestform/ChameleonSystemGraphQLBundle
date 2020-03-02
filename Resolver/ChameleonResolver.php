<?php

namespace AppBundle\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

namespace ChameleonSystem\GraphQLBundle\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

final class ChameleonResolver extends ResolverMap
{

    protected function map()
    {
        return [
            'RootQuery' => [
                self::RESOLVE_FIELD => static function ($value, Argument $args, \ArrayObject $context, ResolveInfo $info) {
                    if ('articles' === $info->fieldName) {
                        $first = $args['first'] ?? -1;
                        $offset = $args['offset'] ?? 0;
                        $match = $args['match'] ?? null;
                        if (isset($args['cheaperThan'])) {
                            return Articles::getAllCheaperThan($args['cheaperThan'], $first, $offset, $match);
                        }
                        return Articles::getAll($first, $offset, $match);
                    }
                    if ('users' === $info->fieldName) {
                        return Users::getAll();
                    }
                    if ('user' === $info->fieldName) {
                        return Users::getById($args['id']);
                    }
                    if ('orders' === $info->fieldName) {
                        return Orders::getAll();
                    }

                    return null;
                }
            ],
            'Article' => [
                self::RESOLVE_FIELD => static function($value, Argument $args, \ArrayObject $context, ResolveInfo $info) {

                    if ('categories' === $info->fieldName) {
                        return Articles::categoriesForArticle($value['id']);
                    }

                    if ('image' === $info->fieldName) {
                        return Articles::imageForArticle($value['id']) ?? [];
                    }

                    if ('thumb' === $info->fieldName) {
                        if (!isset($args['width'], $args['height'])) {
                            return null;
                        }

                        return Articles::thumbForArticle($value['id'], $args['width'], $args['height']);
                    }

                    return $value[$info->fieldName];
                }
            ],
            'User' => [
                self::RESOLVE_FIELD => static function($value, Argument $args, \ArrayObject $context, ResolveInfo $info) {
                    if ('orders' === $info->fieldName) {
                        return Orders::ordersForUser($value['id']);
                    }

                    return $value[$info->fieldName];
                }
            ],
            'Order' => [
                self::RESOLVE_FIELD => static function($value, Argument $args, \ArrayObject $context, ResolveInfo $info) {
                    if ('articles' === $info->fieldName) {
                        return Orders::articlesForOrder($value['id']);
                    }
                    if ('buyer' === $info->fieldName) {
                        return Orders::getUserForOrder($value['id']);
                    }

                    return $value[$info->fieldName];
                }
            ]
        ];
    }
}
