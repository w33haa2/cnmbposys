<?php

use Illuminate\Database\Seeder;
use App\UserInfo;
use Carbon\Carbon;

class UserInfosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
            'uid' => 1, 
            'firstname' => 'HRM',
            'lastname' => 'kouceyla', 
            'middlename' => 'Cool', 
            'birthdate' => '12-12-12', 
            'address' => 'bario ugok',
            'gender' => 'M', 
            'contact_number' => '0000000000',
            ],
            [
            'uid' => 2, 
            'firstname' => 'HRA',
            'lastname' => 'kouceyla', 
            'middlename' => 'Cool', 
            'birthdate' => '12-12-12', 
            'address' => 'bario ugok',
            'gender' => 'M', 
            'contact_number' => '0000000000',
            ],
            [
            'uid' => 3, 
            'firstname' => 'OM',
            'lastname' => 'kouceyla', 
            'middlename' => 'Cool', 
            'birthdate' => '12-12-12', 
            'address' => 'bario ugok',
            'gender' => 'M', 
            'contact_number' => '0000000000',
            ],
            [
            'uid' => 4, 
            'firstname' => 'TL',
            'lastname' => 'kouceyla', 
            'middlename' => 'Cool', 
            'birthdate' => '12-12-12', 
            'address' => 'bario ugok',
            'gender' => 'M', 
            'contact_number' => '0000000000',
            ]];
       
            foreach($data as $datum){
                UserInfo::create($datum);
            }
    }
}
