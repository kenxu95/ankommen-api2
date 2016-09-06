<?php

use Illuminate\Database\Seeder;


class AssetsTableSeeder extends Seeder
{

  // ONLY FOR SVG's !!!!
  private function dataurl_from_file($filePath){
    $type = pathinfo($filePath, PATHINFO_EXTENSION);
    $data = file_get_contents($filePath);
    return 'data:image/svg+xml;utf8,' . $data;
    // return 'data:image/' . $type . ';base64,' . base64_encode($data);
  }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('assets')->insert([
        'name' => 'Hacker',
        'description' => 'Able to hack things.',
        'dataurl' => $this->dataurl_from_file('public/assets/Hacker.svg')
        ]);

      DB::table('assets')->insert([
        'name' => 'Bus',
        'description' => 'Able to drive a bus.',
        'dataurl' => $this->dataurl_from_file('public/assets/Bus.svg')
        ]);

      DB::table('assets')->insert([
        'name' => 'Laptop',
        'description' => 'Able to human .',
        'dataurl' => $this->dataurl_from_file('public/assets/Dna.svg')       
      ]);

      DB::table('assets')->insert([
        'name' => 'Desert',
        'description' => 'Able to eat deserts.',
        'dataurl' => $this->dataurl_from_file('public/assets/Desert.svg')       
        ]);
      
      DB::table('assets')->insert([
        'name' => 'Burglar',
        'description' => 'Able to steal things.',
        'dataurl' => $this->dataurl_from_file('public/assets/Burglar.svg')       
        ]);

      DB::table('assets')->insert([
        'name' => 'Ninja',
        'description' => 'Able to Ninja.',
        'dataurl' => $this->dataurl_from_file('public/assets/Ninja.svg')       
        ]);

      DB::table('assets')->insert([
        'name' => 'Santa',
        'description' => 'Able to shake belly like a bowlful of jelly.',
        'dataurl' => $this->dataurl_from_file('public/assets/Santa.svg')       
        ]);

      DB::table('assets')->insert([
        'name' => 'Surfer',
        'description' => 'Able to surf.',
        'dataurl' => $this->dataurl_from_file('public/assets/Surfer.svg')       
        ]);

      DB::table('assets')->insert([
        'name' => 'Candy',
        'description' => 'Has Candy.',
        'dataurl' => $this->dataurl_from_file('public/assets/Candy.svg')       
        ]);        

      DB::table('assets')->insert([
        'name' => 'Cheese',
        'description' => 'Able to become a mouse.',
        'dataurl' => $this->dataurl_from_file('public/assets/Cheese.svg')       
        ]);      
    }
  }  

















