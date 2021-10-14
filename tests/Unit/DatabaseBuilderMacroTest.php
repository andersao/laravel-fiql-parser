<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseBuilderMacroTest extends TestCase {
    public function testFilterMacro() {
        $query = DB::table('users')->filter('last_name==foo');
        $this->assertEquals('select * from `users` where `last_name` = ?', $query->toSql());
        $this->assertEquals(['foo'], $query->getBindings());
    }
}
