<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Burgers (category_id: 1)
        $this->createProducts([
            ['name' => 'Classic Burger', 'price' => 55.00, 'earn' => 15.00],
            ['name' => 'Cheeseburger', 'price' => 60.00, 'earn' => 18.00],
            ['name' => 'Double Burger', 'price' => 75.00, 'earn' => 20.00],
            ['name' => 'Chicken Burger', 'price' => 50.00, 'earn' => 15.00],
            ['name' => 'Veggie Burger', 'price' => 45.00, 'earn' => 12.00],
            ['name' => 'BBQ Burger', 'price' => 65.00, 'earn' => 18.00],
            ['name' => 'Mushroom Burger', 'price' => 60.00, 'earn' => 16.00],
            ['name' => 'Spicy Burger', 'price' => 58.00, 'earn' => 16.00],
            ['name' => 'Fish Burger', 'price' => 52.00, 'earn' => 14.00],
            ['name' => 'Deluxe Burger', 'price' => 80.00, 'earn' => 22.00],
        ], 1);

        // Pizza (category_id: 2)
        $this->createProducts([
            ['name' => 'Margherita Pizza', 'price' => 70.00, 'earn' => 20.00],
            ['name' => 'Pepperoni Pizza', 'price' => 85.00, 'earn' => 25.00],
            ['name' => 'Vegetarian Pizza', 'price' => 75.00, 'earn' => 22.00],
            ['name' => 'Hawaiian Pizza', 'price' => 80.00, 'earn' => 23.00],
            ['name' => 'BBQ Chicken Pizza', 'price' => 90.00, 'earn' => 26.00],
            ['name' => 'Meat Lovers Pizza', 'price' => 95.00, 'earn' => 28.00],
            ['name' => 'Supreme Pizza', 'price' => 100.00, 'earn' => 30.00],
            ['name' => 'Mushroom Pizza', 'price' => 78.00, 'earn' => 22.00],
            ['name' => 'Four Cheese Pizza', 'price' => 88.00, 'earn' => 25.00],
            ['name' => 'Buffalo Pizza', 'price' => 92.00, 'earn' => 27.00],
        ], 2);

        // Drinks (category_id: 3)
        $this->createProducts([
            ['name' => 'Cola', 'price' => 15.00, 'earn' => 5.00],
            ['name' => 'Sprite', 'price' => 15.00, 'earn' => 5.00],
            ['name' => 'Orange Juice', 'price' => 18.00, 'earn' => 6.00],
            ['name' => 'Lemonade', 'price' => 16.00, 'earn' => 5.00],
            ['name' => 'Iced Tea', 'price' => 14.00, 'earn' => 4.00],
            ['name' => 'Water', 'price' => 10.00, 'earn' => 3.00],
            ['name' => 'Coffee', 'price' => 20.00, 'earn' => 7.00],
            ['name' => 'Milkshake', 'price' => 25.00, 'earn' => 8.00],
            ['name' => 'Smoothie', 'price' => 28.00, 'earn' => 9.00],
            ['name' => 'Hot Chocolate', 'price' => 22.00, 'earn' => 7.00],
        ], 3);

        // Sides (category_id: 4)
        $this->createProducts([
            ['name' => 'French Fries', 'price' => 25.00, 'earn' => 8.00],
            ['name' => 'Onion Rings', 'price' => 28.00, 'earn' => 9.00],
            ['name' => 'Coleslaw', 'price' => 20.00, 'earn' => 6.00],
            ['name' => 'Mozzarella Sticks', 'price' => 35.00, 'earn' => 12.00],
            ['name' => 'Garlic Bread', 'price' => 22.00, 'earn' => 7.00],
            ['name' => 'Chicken Wings', 'price' => 45.00, 'earn' => 15.00],
            ['name' => 'Caesar Salad', 'price' => 38.00, 'earn' => 12.00],
            ['name' => 'Potato Wedges', 'price' => 30.00, 'earn' => 10.00],
            ['name' => 'Nachos', 'price' => 40.00, 'earn' => 13.00],
            ['name' => 'Sweet Potato Fries', 'price' => 32.00, 'earn' => 10.00],
        ], 4);

        // Desserts (category_id: 5)
        $this->createProducts([
            ['name' => 'Ice Cream', 'price' => 20.00, 'earn' => 7.00],
            ['name' => 'Chocolate Cake', 'price' => 35.00, 'earn' => 12.00],
            ['name' => 'Apple Pie', 'price' => 30.00, 'earn' => 10.00],
            ['name' => 'Cheesecake', 'price' => 38.00, 'earn' => 13.00],
            ['name' => 'Brownie', 'price' => 25.00, 'earn' => 8.00],
            ['name' => 'Tiramisu', 'price' => 40.00, 'earn' => 14.00],
            ['name' => 'Fruit Salad', 'price' => 28.00, 'earn' => 9.00],
            ['name' => 'Pudding', 'price' => 22.00, 'earn' => 7.00],
            ['name' => 'Cookie', 'price' => 15.00, 'earn' => 5.00],
            ['name' => 'Sundae', 'price' => 32.00, 'earn' => 11.00],
        ], 5);
    }

    private function createProducts(array $products, int $categoryId): void
    {
        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'price' => $product['price'],
                'earn' => $product['earn'],
                'available' => true,
                'category_id' => $categoryId,
            ]);
        }
    }
}