<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
          'username' => 'Pandapaw',
          'email' => 'kenxu95@gmail.com',
          'password' => 'password1',
          'firstname' => 'Kenny',
          'lastname' => 'Xu'
          // 'bio' => 'I am person',
          // 'contactFrequency' => 1
        ]);

        DB::table('users')->insert([
          'username' => 'Peachbear',
          'email' => 'superlaserpickzap@gmail.com',
          'password' => 'password2',
          'firstname' => 'Jesus',
          'lastname' => 'Man'
          // 'bio' => 'Walking on... water',
          // 'contactFrequency' => 2
        ]);
    }
}
