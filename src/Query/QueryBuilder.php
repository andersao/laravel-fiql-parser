<?php

namespace Prettus\Laravel\FIQL\Query;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Prettus\FIQLParser\Expression;
use Prettus\FIQLParser\Parser;

class QueryBuilder
{
    /**
     * @param EloquentBuilder|Builder $builder
     * @param Expression|string $expression
     * @return Builder
     * @throws \Prettus\FIQLParser\Exceptions\FIQLObjectException
     * @throws \Prettus\FIQLParser\Exceptions\FiqlFormatException
     */
    public static function applyFilter($builder, $expression): Builder
    {
        $queryBuilder = $builder instanceof EloquentBuilder ? $builder->getQuery() : $builder;
        if (is_string($expression)) {
            $expression = Parser::fromString($expression);
        }
        return self::applyWhere($queryBuilder, $expression);
    }

    private static function arrayEvery(array $data, \Closure $callback): bool
    {
        foreach ($data as $value) {
            if (!$callback($value)) {
                return false;
            }
        }
        return true;
    }

    private static function applyWhere(Builder $builder, $expression, string $operator = 'and'): Builder
    {
        $elements = $expression instanceof Expression ? $expression->toArray() : $expression;

        if (array_key_exists('or', $elements) or array_key_exists('and', $elements)) {
            $operators = array_keys($elements);
            foreach ($operators as $operator) {
                self::addCondition($builder, $operator, $elements[$operator]);
            }
        } elseif (is_array($elements)) {
            $elementsCount = sizeof($elements);
            $isCondition = $elementsCount == 3 && self::arrayEvery($elements, function ($value) {
                return is_string($value);
            });

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
            return;
        }

        if ($selector == 'and') {
            $builder->where(function ($query) use ($selector, $value) {
                return self::applyWhere($query, $value, $selector);
            });
            return;
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
