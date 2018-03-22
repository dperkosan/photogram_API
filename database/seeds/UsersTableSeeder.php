<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $this->customSeed();

        $faker = Faker\Factory::create();

        $allUsers = [];

        foreach (range(1, 50) as $index) {
            $allUsers[] = [
              'name' => $faker->name,
              'username' => $faker->unique()->userName,
              'email' => $faker->unique()->safeEmail,
              'password' => bcrypt('admin'),
              'gender_id' => $faker->randomElement([1, 2, 3]),
              'phone' => $faker->phoneNumber,
              'about' => $faker->text(200),
              'image' => 'images/user/placeholder-[~FORMAT~].jpg',
              'type_id' => 1,
              'active' => 1,
            ];
        }

        DB::table('users')->insert($allUsers);
    }

    public function customSeed()
    {
        $testUser = config('boilerplate.test_user');
        $testUser['password'] = bcrypt($testUser['password']);

        $users = [
          [
            'username'  => 'UrosHCS',
            'name'      => 'Uroš Anđelić',
            'email'     => 'uroshcs@gmail.com',
            'password'  => bcrypt('urosadmin'),
            'gender_id' => User::GENDER_MALE,
            'phone'     => '555-333',
            'about'     => 'My name is Neo',
            'image'     => 'images/user/1/UrosHCS-[~FORMAT~].jpg',
            'active'    => 1,
            'type_id'   => 1,
          ],
          [
            'username'  => 'esteban',
            'name'      => 'Stefan Petković',
            'email'     => 'stefan.petkovic@diwanee.com',
            'password'  => bcrypt('1'),
            'gender_id' => User::GENDER_MALE,
            'phone'     => '123456',
            'about'     => 'about me something',
            'image'     => 'images/user/2/esteban-[~FORMAT~].jpg',
            'active'    => 1,
            'type_id'   => 1,
          ],
          [
            'username'  => 'Wanted',
            'name'      => 'Stefan Milić',
            'email'     => 'stefan.milic@diwanee.com',
            'password'  => bcrypt('1'),
            'gender_id' => User::GENDER_MALE,
            'phone'     => '123456',
            'about'     => 'about me something',
            'image'     => 'images/user/placeholder-[~FORMAT~].jpg',
            'active'    => 1,
            'type_id'   => 1,
          ],
          [
            'username'  => 'bojan',
            'name'      => 'Bojan Lazarević',
            'email'     => 'bojan.lazarevic@diwanee.com',
            'password'  => bcrypt('bojanadmin'),
            'gender_id' => User::GENDER_MALE,
            'phone'     => '123456',
            'about'     => 'about me something',
            'image'     => 'images/user/placeholder-[~FORMAT~].jpg',
            'active'    => 1,
            'type_id'   => 1,
          ],
          [
            'username'  => 'damir',
            'name'      => 'Damir Perkošan',
            'email'     => 'damir.perkosan@diwanee.com',
            'password'  => bcrypt('12345'),
            'gender_id' => User::GENDER_OTHER,
            'phone'     => '123456',
            'about'     => 'about me something',
            'image'     => 'images/user/placeholder-[~FORMAT~].jpg',
            'active'    => 1,
            'type_id'   => 1,
          ],
          [
            'username'  => 'igor',
            'name'      => 'Igor Bogdanović',
            'email'     => 'igor.bogdanovic@diwanee.com',
            'password'  => bcrypt('1'),
            'gender_id' => User::GENDER_MALE,
            'phone'     => '123456',
            'about'     => 'about me something',
            'image'     => 'images/user/2/igor-[~FORMAT~].jpg',
            'active'    => 1,
            'type_id'   => 1,
          ],
          $testUser
        ];


        DB::table('users')->insert($users);
    }
}