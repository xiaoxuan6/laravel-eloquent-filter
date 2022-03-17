<?php
/**
 * This file is part of PHP CS Fixer.
 *
 * (c) vinhson <15227736751@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace James\Eloquent\Filter\Tests;

use Illuminate\Support\Facades\DB;
use James\Eloquent\Filter\Tests\Models\User;
use James\Eloquent\Filter\Tests\Filters\UserFilter;

class FilterTest extends TestCase
{
    /**
     * @dataProvider data
     */
    public function test_filter_user_equal($a, $b)
    {
        DB::table('users')->insert([$a, $b]);

        $user = User::query()->filter(new UserFilter(request()->merge(['name' => 'vinhson'])))->get();
        $this->assertEquals(1, $user->count());

        User::query()->create([
            'name' => 'vinhsons',
            'email' => '1234@email.com',
            'age' => 20,
            'sex' => '男'
        ]);

        $user = User::query()->filter(new UserFilter(request()->merge(['name' => 'vinhsons', 'email' => '1234@email.com'])))->get();
        $this->assertEquals(1, $user->count());

        $this->assertEquals(3, User::query()->count());
    }

    /**
     * @dataProvider data
     */
    public function test_filter_user_like($a, $b)
    {
        DB::table('users')->insert([$a, $b]);

        $user = User::query()->filter(new UserFilter(request()->merge(['like_name' => 'son'])))->get();
        $this->assertEquals(2, $user->count(), sprintf('User count：%s', $user->count()));
    }

    /**
     * @dataProvider data
     */
    public function test_filter_user_equal_and_like($a, $b, $c)
    {
        DB::table('users')->insert([$a, $b, $c]);

        $user = User::query()->filter(new UserFilter(request()->merge(['like_name' => 'son', 'email' => '123456@email.com'])))->get();
        $this->assertEquals(1, $user->count());
        $this->assertEquals(3, User::query()->count());
    }

    /**
     * @dataProvider data
     */
    public function test_filter_user_append($a, $b, $c, $d)
    {
        DB::table('users')->insert([$a, $b, $c, $d]);

        // equal
        $user = User::query()->filter(['name:eto'])->get();
        $this->assertEquals(1, $user->count());

        // null
        $user = User::query()->filter('name:|null')->get();
        $this->assertEquals(1, $user->count());

        // like or start or end
        $user = User::query()->filter(['name:son|like'])->get();
        $this->assertEquals(2, $user->count());

        $user = User::query()->filter(['name:son|end'])->get();
        $this->assertEquals(1, $user->count());

        $user = User::query()->filter(['name:vin|start'])->get();
        $this->assertEquals(2, $user->count());

        $user = User::query()->filter(['name:vin|start', 'email:123@email.com'])->get();
        $this->assertEquals(1, $user->count());

        $user = User::query()->filter(['name:vin|start', 'name:eto|or'])->get();
        $this->assertEquals(3, $user->count());

        // in or notIn
        $user = User::query()->filter(['name:vinhson,vinhsons|in'])->get();
        $this->assertEquals(2, $user->count());

        $user = User::query()->filter(['name:vinhson,vinhsons|notIn'])->get();
        $this->assertEquals(1, $user->count());

        // between or notBetween
        $user = User::query()->filter(['age:10,18|between'])->get();
        $this->assertEquals(1, $user->count());

        $user = User::query()->filter(['age:10,18|notBetween'])->get();
        $this->assertEquals(3, $user->count());
    }

    public function data(): array
    {
        return [
            [
                [
                    'name' => 'vinhsons',
                    'email' => '123@email.com',
                    'age' => 15,
                    'sex' => '男'
                ],
                [
                    'name' => 'vinhson',
                    'email' => '123456@email.com',
                    'age' => 20,
                    'sex' => '男'
                ],
                [
                    'name' => 'eto',
                    'email' => '123456@email.com',
                    'age' => 20,
                    'sex' => '男'
                ],
                [
                    'name' => null,
                    'email' => '123456@email.com',
                    'age' => 20,
                    'sex' => '男'
                ]
            ]
        ];
    }
}
