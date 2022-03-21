<?php
/**
 * This file is part of PHP CS Fixer.
 *
 * (c) vinhson <15227736751@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace James\Eloquent\Filter\Tests\Factory;

use James\Eloquent\Filter\Tests\Models\Oauth;
use Illuminate\Database\Eloquent\Factories\Factory;

class OauthFactory extends Factory
{
    protected $model = Oauth::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'sex' => $this->faker->randomElement(['男', '女']),
            'age' => $this->faker->randomNumber(2),
        ];
    }
}
