<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ApiSettings;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Wallet;
use Faker\Generator as Faker;

use Illuminate\Support\Arr;
use SebastianBergmann\Environment\Console;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
/*
$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});*/

$factory->define(ApiSettings::class, function (Faker $faker){
    return [
        'comments_allow_amount' => 5,
        'comments_allow_seconds' => 60,
        'hours_expire_notification' => 2,
        'retain_percentage' => 5
    ];
});

$factory->define(User::class, function (Faker $faker){
    $sign = $faker->randomElement(['Capricorn','Aquarius','Pisces','Aries','Taurus','Gemini','Cancer','Leo','Virgo','Libra','Scorpio','Sagittarius']);
    $birth_year = rand(1980,2007);
    switch($sign){
        case 'Capricorn':{
            $birthdate = $faker->dateTimeBetween(($birth_year-1).'-12-22', $birth_year.'-01-20')->format('Y-m-d');
            break;
        }
        case 'Aquarius':{
            $birthdate = $faker->dateTimeBetween($birth_year.'-01-21', $birth_year.'-02-18')->format('Y-m-d');
            break;
        }
        case 'Pisces':{
            $birthdate = $faker->dateTimeBetween($birth_year.'-02-19', $birth_year.'-03-20')->format('Y-m-d');
            break;
        }
        case 'Aries':{
            $birthdate = $faker->dateTimeBetween($birth_year.'-03-21', $birth_year.'-04-20')->format('Y-m-d');
            break;
        }
        case 'Taurus':{
            $birthdate = $faker->dateTimeBetween($birth_year.'-04-21', $birth_year.'-05-21')->format('Y-m-d');
            break;
        }
        case 'Gemini':{
            $birthdate = $faker->dateTimeBetween($birth_year.'-05-22', $birth_year.'-06-21')->format('Y-m-d');
            break;
        }
        case 'Cancer':{
            $birthdate = $faker->dateTimeBetween($birth_year.'-06-22', $birth_year.'-07-22')->format('Y-m-d');
            break;
        }
        case 'Leo':{
            $birthdate = $faker->dateTimeBetween($birth_year.'-07-23', $birth_year.'-08-23')->format('Y-m-d');
            break;
        }
        case 'Virgo':{
            $birthdate = $faker->dateTimeBetween($birth_year.'-08-24', $birth_year.'-09-22')->format('Y-m-d');
            break;
        }
        case 'Libra':{
            $birthdate = $faker->dateTimeBetween($birth_year.'-09-23', $birth_year.'-10-23')->format('Y-m-d');
            break;
        }
        case 'Scorpio':{
            $birthdate = $faker->dateTimeBetween($birth_year.'-10-24', $birth_year.'-11-22')->format('Y-m-d');
            break;
        }
        case 'Sagittarius':{
            $birthdate = $faker->dateTimeBetween($birth_year.'-11-23', $birth_year.'-12-21')->format('Y-m-d');
            break;
        }
    }
    return [
        'name' => $faker->name,
        'username' => $faker->userName,
        'sign' => $sign,
        'birthdate' => $birthdate,
        'subscribe' => $faker-> boolean(30),
        'email' => $faker->email,
        'coin_balance' => rand(0,500)
    ];
});

$factory->define(Post::class, function (Faker $faker){
    $users = User::all()
    ->pluck('id_user')
    ->toArray();
    $randomUser = Arr::random($users);
    $type = $faker->randomElement(['Foto','Video','Texto']);
    return [
        'type' => $type,
        'body_message' => $faker->text($maxNbChars=1200),
        'file_index' => $type != 'Texto' ? $faker->uuid() : null,
        'id_user' => $randomUser
    ];
});

$factory->define(Comment::class, function (Faker $faker){
    $subscribed_users = User::where('subscribe','=',true)
    ->pluck('id_user')
    ->toArray();
    $posts = Post::all()->pluck('id_post')->toArray();
    return [
        'message'=> $faker->text($maxNbChars=200),
        'id_user' => Arr::random($subscribed_users),
        'id_post' => Arr::random($posts)
    ];
});