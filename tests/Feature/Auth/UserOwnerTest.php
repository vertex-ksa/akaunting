<?php

namespace Tests\Feature\Auth;

use App\Models\Auth\User;
use Tests\TestCase;

class UserOwnerTest extends TestCase
{
    public function testItEagerLoadsTheCreatorRequiredByTheUserApi(): void
    {
        $creator = User::create(['name' => 'Owner regression', 'email' => 'owner-regression@example.invalid', 'password' => 'test-only', 'enabled' => true]);
        $reader = User::create(['name' => 'Reader regression', 'email' => 'reader-regression@example.invalid', 'password' => 'test-only', 'enabled' => true, 'created_by' => $creator->id]);

        $loaded = User::with('owner')->findOrFail($reader->id);

        $this->assertTrue($loaded->relationLoaded('owner'));
        $this->assertSame($creator->id, $loaded->owner->id);
        $this->assertSame($creator->email, $loaded->owner->email);
    }

    public function testItProvidesTheExistingDefaultForUsersWithoutACreator(): void
    {
        $reader = User::create(['name' => 'Missing owner regression', 'email' => 'missing-owner-regression@example.invalid', 'password' => 'test-only', 'enabled' => true, 'created_by' => null]);

        $loaded = User::with('owner')->findOrFail($reader->id);

        $this->assertFalse($loaded->owner->exists);
        $this->assertSame(trans('general.na'), $loaded->owner->name);
    }
}
