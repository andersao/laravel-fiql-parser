<?php

namespace Prettus\Laravel\FIQL\Query;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Prettus\FIQLParser\Expression;
use Prettus\FIQLParser\Parser;

class FilterFIQL
{
    /**
     * @var Builder|EloquentBuilder
     */
    private $builder;

    /**
     * @var Expression
     */
    private $expression;

    /**
     * @param Builder|EloquentBuilder $builder
     * @param Expression|string $expression
     */
    public function __construct($builder, $expression)
    {
        $this->builder = $builder;
        $this->expression = $this->ensureExpression($expression);
    }

    private function ensureExpression($expression): Expression
    {
        if (is_string($expression)) {
            return Parser::fromString($expression);
        }
        return $expression;
    }

    public function apply(): Builder
    {
        return QueryBuilder::applyFilter($this->builder, $this->expression);
    }
}
