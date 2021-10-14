<?php

namespace Prettus\Laravel\FIQL;

use Illuminate\Database\Query\Builder;
use Prettus\FIQLParser\Expression;

class QueryBuilder
{
    public static function apply($expression, Builder $builder): Builder
    {
        return self::applyWhere($builder, $expression);
    }

    private static function applyWhere(Builder $builder, $expression, string $operator = 'and'): Builder
    {
        $elements = $expression instanceof Expression ? $expression->toArray() : $expression;

        if (is_array($elements)) {
            $elementsCount = sizeof($elements);
            $isCondition = $elementsCount  == 3;

            if ($isCondition) {
                list($selector, $comparison, $value) = $elements;
                self::addCondition($builder, $selector, $value, $comparison);
            } else {
                foreach ($elements as $element) {
                    if ($operator == 'or') {
                        $builder->orWhere(function ($query) use ($element) {
                            return self::applyWhere($query, $element);
                        });
                    } else {
                        $builder->where(function ($query) use ($element) {
                            return self::applyWhere($query, $element);
                        });
                    }
                }
            }
        }

        return $builder;
    }

    private static function addCondition(Builder $builder, $selector, $value, $comparison = '='): void
    {
        if ($selector == 'or') {
            $builder->orWhere(function ($query) use ($selector, $value) {
                return self::applyWhere($query, $value, $selector);
            });
        }

        if ($selector == 'and') {
            $builder->where(function ($query) use ($selector, $value) {
                return self::applyWhere($query, $value, $selector);
            });
        }

        $operator = $comparison == '==' ? '=' : $comparison;
        $param = $value;

        if (str_contains($value, '*')) {
            $param = preg_replace('/\*/m', '%', $value);
            $operator = 'like';
        }

        $builder->where($selector, $operator, $param);
    }
}
