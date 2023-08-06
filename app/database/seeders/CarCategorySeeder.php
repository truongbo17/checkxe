<?php

namespace Database\Seeders;

use Bo\CarCategory\Models\CarCategory;
use Illuminate\Database\Seeder;

class CarCategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            "Ford",
            "Honda",
            "Hyundai",
            "Toyota",
            "Isuzu",
            "KIA",
            "Mercedes Benz",
            "BMW",
            "Mini Cooper",
            "Audi",
            "Lamborghini",
            "Volvo",
            "Jaguar",
            "Maserati",
            "Aston Martin",
            "Bentley",
            "Vinfast",
            "Mitsubishi",
            "Chevrolet",
            "Lexus",
            "Mazda",
            "Nissan",
            "Subaru",
            "Ssangyong",
            "Land Rover",
            "Peugeot",
            "Volkswagen",
            "Porsche",
            "Ferrari",
        ];

        foreach ($data as $name) {
            CarCategory::create([
                'name' => $name
            ]);
        }
    }
}
