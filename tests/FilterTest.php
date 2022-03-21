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
use James\Eloquent\Filter\Tests\Models\{Book, Oauth, User};
use James\Eloquent\Filter\Tests\Filters\{OauthFilter, UserFilter};

class FilterTest extends TestCase
{
    public function test_factory()
    {
        User::factory()->create(['sex' => '男']);

        $this->assertEquals(1, User::query()->count());
        $this->assertEquals(1, User::query()->filter('sex:男')->count());
    }

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

    public function test_relation_book()
    {
        User::factory()->create();
        $user = User::factory()->create(['id' => 10]);
        Book::factory()->create(['user_id' => $user->getKey()]);

        $relation = User::query()->with('book')->find(10)->book;
        $this->assertSame('10', $relation->first()->user_id);

        Book::factory()->create(['user_id' => $user->getKey(), 'name' => 'demo']);
        Book::factory()->create(['user_id' => $user->getKey() + 1, 'name' => 'demo']);

        $relation = User::query()->with('book')->filter(new UserFilter(request()->merge(['book_name' => 'demo'])))->get();
        $this->assertCount(1, $relation);
        $this->assertCount(2, $relation->first()->book);

        $relation = User::query()->with('book')->find(10);
        $this->assertCount(2, $relation->book);

        $book = Book::query()->get();
        $this->assertCount(3, $book);

        $book = Book::query()->selectRaw('count(*) as count, user_id')->groupBy('user_id')->get()->pluck('count', 'user_id');
        $this->assertSame('2', $book['10']);
        $this->assertSame('1', $book['11']);
    }

    public function test_ignore_filter()
    {
        User::factory()->create(['name' => 'eto']);
        User::factory()->create(['name' => 'vinhson']);

        $user = User::ignoreRequest('name')->get();
        $this->assertCount('2', $user);
        $user = User::ignoreRequest('name')->filter()->get();
        $this->assertCount('2', $user);

        $user = User::query()->filter(new UserFilter(request()->merge(['name' => 'eto'])))->get();
        $this->assertCount('1', $user);
        $user = User::ignoreRequest('name')->filter(new UserFilter(request()->merge(['name' => 'eto'])))->get();
        $this->assertCount('2', $user);
    }

    public function test_accept_filter()
    {
        User::factory()->create(['name' => 'eto']);
        User::factory()->create(['name' => 'vinhson']);

        $user = User::acceptRequest('name')->get();
        $this->assertCount('2', $user);
        $user = User::acceptRequest('name')->filter()->get();
        $this->assertCount('2', $user);

        $user = User::query()->filter(new UserFilter(request()->merge(['name' => 'eto'])))->get();
        $this->assertCount('1', $user);

        User::factory()->create(['name' => 'eto', 'email' => '123@qq.com']);
        User::factory()->create(['name' => 'vinhson', 'email' => '123@qq.com']);
        $user = User::query()->filter(new UserFilter(request()->merge(['name' => 'eto', 'email' => '123@qq.com'])))->get();
        $this->assertCount('1', $user);
        $user = User::acceptRequest('email')->filter(new UserFilter(request()->merge(['name' => 'eto', 'email' => '123@qq.com'])))->get();
        $this->assertCount('2', $user);
    }

    public function test_parent_filter()
    {
        $this->withoutExceptionHandling();

        Oauth::factory()->create([]);

        $oauth = Oauth::query()->filter()->get();
        $this->assertCount('1', $oauth);

        Oauth::factory()->create(['name' => 'eto']);
        $oauth = Oauth::query()->filter(new OauthFilter(request()->merge(['name' => 'eto'])))->get();
        $this->assertCount('1', $oauth);

        Oauth::ignoreRequest('name');
        $oauth = Oauth::query()->filter(new OauthFilter(request()->merge(['name' => 'eto'])))->get();
        $this->assertCount('2', $oauth);

        Oauth::ignoreRequest([]);
        $oauth = Oauth::query()->filter(new OauthFilter(request()->merge(['name' => 'eto'])))->get();
        $this->assertCount('1', $oauth);

        $oauth = Oauth::ignoreRequest('name')->filter(new OauthFilter(request()->merge(['name' => 'eto'])))->get();
        $this->assertCount('2', $oauth);
    }
}
