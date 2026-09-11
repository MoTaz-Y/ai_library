<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookAndCategorySeeder extends Seeder
{
    public function run(): void
    {
        $prog = Category::firstOrCreate(['name' => 'Programming', 'description' => 'Software development books']);
        $ai = Category::firstOrCreate(['name' => 'Artificial Intelligence', 'description' => 'AI, ML, and Data Science']);

        Book::firstOrCreate(['isbn' => '978-0132350884'], [
            'title' => 'Clean Code',
            'author' => 'Robert C. Martin',
            'description' => 'A Handbook of Agile Software Craftsmanship, essential for writing clean PHP and JavaScript code.',
            'category_id' => $prog->id,
            'publication_date' => '2008-08-01',
            'available_copies' => 5,
        ]);

        Book::firstOrCreate(['isbn' => '978-1491957660'], [
            'title' => 'Hands-On Machine Learning',
            'author' => 'Aurélien Géron',
            'description' => 'Concepts, tools, and techniques to build intelligent systems using Python, Scikit-Learn, and TensorFlow.',
            'category_id' => $ai->id,
            'publication_date' => '2019-10-15',
            'available_copies' => 3,
        ]);
    }
}