<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
    }
}
