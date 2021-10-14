<?php

namespace Tests\Unit;

use Prettus\Laravel\FIQL\Query\QueryBuilder;
use Tests\TestCase;
use Prettus\FIQLParser\Parser;
use Illuminate\Support\Facades\DB;

class QueryBuilderTest extends TestCase
{
    public function testShouldBuildQueryFromString() {
        $builder = DB::table('users');
        $query = QueryBuilder::applyFilter($builder,'last_name==foo');
        $this->assertEquals('select * from `users` where `last_name` = ?', $query->toSql());
        $this->assertEquals(['foo'], $query->getBindings());
    }

    public function testShouldBuildQueryFromExpression() {
        $builder = DB::table('users');
        $query = QueryBuilder::applyFilter($builder, Parser::fromString('last_name==foo'));
        $this->assertEquals('select * from `users` where `last_name` = ?', $query->toSql());
        $this->assertEquals(['foo'], $query->getBindings());
    }

    /**
     * @dataProvider queriesProvider
     */
    public function testBuildQueries(string $table, string $fiql, string $sql, array $values = [])
    {
        $builder = DB::table($table);
        $expression = Parser::fromString($fiql);
        $query = QueryBuilder::applyFilter($builder, $expression);
        $this->assertEquals($sql, $query->toSql());
        $this->assertEquals($values, $query->getBindings());
    }

    public function queriesProvider(): array
    {
        return [
            [
                'users',
                'last_name==foo',
                'select * from `users` where `last_name` = ?',
                ['foo']
            ],
            [
                'users',
                'last_name!=foo',
                'select * from `users` where `last_name` != ?',
                ['foo']
            ],
            [
                'users',
                'last_name==*foo',
                'select * from `users` where `last_name` like ?',
                ['%foo']
            ],
            [
                'users',
                'last_name==foo*',
                'select * from `users` where `last_name` like ?',
                ['foo%']
            ],
            [
                'users',
                'last_name==*foo*',
                'select * from `users` where `last_name` like ?',
                ['%foo%']
            ],
            [
                'users',
                'age=lt=50',
                'select * from `users` where `age` < ?',
                ['50']
            ],
            [
                'users',
                'age=le=50',
                'select * from `users` where `age` <= ?',
                ['50']
            ],
            [
                'users',
                'age=gt=50',
                'select * from `users` where `age` > ?',
                ['50']
            ],
            [
                'users',
                'age=ge=50',
                'select * from `users` where `age` >= ?',
                ['50']
            ],
            [
                'users',
                'last_name==foo;age=gt=50',
                'select * from `users` where ((`last_name` = ?) and (`age` > ?))',
                ['foo', '50']
            ],
            [
                'users',
                'first_name==bar,last_name==foo',
                'select * from `users` where ((`first_name` = ?) or (`last_name` = ?))',
                ['bar', 'foo']
            ],
            [
                'users',
                'last_name==foo*,(age=lt=55;age=gt=5)',
                'select * from `users` where ((`last_name` like ?) or (((`age` < ?) and (`age` > ?))))',
                ['foo%', '55', '5']
            ],
        ];
    }
}
