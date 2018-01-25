<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users[] = [
            [
                'username' => 'UrosHCS',
                'name' => 'Uroš Anđelić',
                'email' => 'uroshcs@gmail.com',
                'password' => bcrypt('urosadmin'),
                'gender_id' => 1,
                'phone' => '555-333',
                'about' => 'My name is Neo',
                'image' => 'user/1/uros.jpg',
                'active' => 1,
                'type_id' => 1,
            ],
            [
                'username' => 'stefke',
                'name' => 'Stefan Petković',
                'email' => 'stefan.petkovic@diwanee.com',
                'password' => bcrypt('stefanadmin'),
                'gender_id' => 1,
                'phone' => '123456',
                'about' => 'about me something',
                'image' => '',
                'active' => 1,
                'type_id' => 1,
            ],
            [
                'username' => 'Wanted',
                'name' => 'Stefan Milić',
                'email' => 'stefan.milic@diwanee.com',
                'password' => bcrypt('stefanadmin'),
                'gender_id' => 1,
                'phone' => '123456',
                'about' => 'about me something',
                'image' => '',
                'active' => 1,
                'type_id' => 1,
            ],
//            [
//                'username' => 'bojan',
//                'name' => 'Bojan Lazarević',
//                'email' => 'bojan.lazarevic@diwanee.com',
//                'password' => bcrypt('bojanadmin'),
//                'gender_id' => 1,
//                'phone' => '123456',
//                'about' => 'about me something',
//                'image' => '',
//                'active' => 1,
//                'type_id' => 1,
//            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}