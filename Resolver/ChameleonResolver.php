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
                    if ('products' === $info->fieldName) {
                        $first = $args['first'] ?? -1;
                        $offset = $args['offset'] ?? 0;
                        $match = $args['match'] ?? null;
                        $category = $args['category'] ?? null;
                        if ('' === $category) {
                            $category = null;
                        }
                        if (isset($args['cheaperThan'])) {
                            return Products::getAllCheaperThan($args['cheaperThan'], $first, $offset, $match, $category);
                        }
                        return Products::getAll($first, $offset, $match, $category);
                    }
                    if ('users' === $info->fieldName) {
                        return Users::getAll();
                    }
                    if ('user' === $info->fieldName) {
                        return Users::getById($args['id']);
                    }
                    if ('orders' === $info->fieldName) {
                        $first = $args['first'] ?? -1;
                        return Orders::getAll($first);
                    }
                    if ('categories' === $info->fieldName) {
                        return Categories::allCategories();
                    }

                    return null;
                }
            ],
            'Product' => [
                self::RESOLVE_FIELD => static function($value, Argument $args, \ArrayObject $context, ResolveInfo $info) {

                    if ('categories' === $info->fieldName) {
                        return Categories::categoriesForArticle($value['id']);
                    }

                    if ('image' === $info->fieldName) {
                        return Products::imageForArticle($value['id']) ?? [];
                    }

                    if ('thumb' === $info->fieldName) {
                        if (!isset($args['width'], $args['height'])) {
                            return null;
                        }

                        return Products::thumbForArticle($value['id'], $args['width'], $args['height']);
                    }

                    return $value[$info->fieldName];
                }
            ],
            'User' => [
                self::RESOLVE_FIELD => static function($value, Argument $args, \ArrayObject $context, ResolveInfo $info) {
                    if ('orders' === $info->fieldName) {
                        $first = $args['first'] ?? -1;
                        return Orders::ordersForUser($value['id'], $first);
                    }

                    return $value[$info->fieldName];
                }
            ],
            'Order' => [
                self::RESOLVE_FIELD => static function($value, Argument $args, \ArrayObject $context, ResolveInfo $info) {
                    if ('products' === $info->fieldName) {
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
