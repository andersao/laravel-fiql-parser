<?php

namespace Tests\Unit;

use Tests\TestCase;

class EloquentBuilderMacroTest extends TestCase {
    public function testWithSimpleQuery() {
        $query = User::query()->filter('last_name==foo');
        $this->assertEquals('select * from `users` where `last_name` = ?', $query->toSql());
        $this->assertEquals(['foo'], $query->getBindings());
    }

    public function testWithHydratedQuery() {
        $query = User::query()->where('tenant_id', '1')->filter('last_name==foo');
        $this->assertEquals('select * from `users` where `tenant_id` = ? and `last_name` = ?', $query->toSql());
        $this->assertEquals(['1', 'foo'], $query->getBindings());
    }
}
