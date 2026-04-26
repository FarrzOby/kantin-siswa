<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\MenuItem;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create users
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@kantinsiswa.com',
            'username' => 'admin',
            'role'     => 'admin',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name'     => 'Kasir Utama',
            'email'    => 'kasir@kantinsiswa.com',
            'username' => 'kasir1',
            'role'     => 'kasir',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name'     => 'Agassi Farras',
            'email'    => 'siswa@kantinsiswa.com',
            'username' => 'agassi',
            'role'     => 'siswa',
            'kelas'    => 'XI IPA 1',
            'nis'      => '2024001',
            'password' => Hash::make('password'),
        ]);

        // Create categories
        $categories = [
            ['name' => 'Makanan Berat', 'slug' => 'makanan-berat',  'icon' => '🍚', 'sort_order' => 1],
            ['name' => 'Makanan Ringan', 'slug' => 'makanan-ringan', 'icon' => '🍟', 'sort_order' => 2],
            ['name' => 'Minuman',        'slug' => 'minuman',        'icon' => '🥤', 'sort_order' => 3],
            ['name' => 'Snack',          'slug' => 'snack',          'icon' => '🍪', 'sort_order' => 4],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Create menu items
        $items = [
            // Makanan Berat (category_id: 1)
            ['category_id' => 1, 'name' => 'Nasi Ayam Geprek', 'description' => 'Nasi putih dengan ayam geprek sambel spesial', 'price' => 15000, 'stock' => 30, 'is_featured' => true],
            ['category_id' => 1, 'name' => 'Nasi Rendang', 'description' => 'Nasi putih dengan rendang sapi empuk', 'price' => 18000, 'stock' => 20, 'is_featured' => true],
            ['category_id' => 1, 'name' => 'Nasi Goreng Spesial', 'description' => 'Nasi goreng dengan telur, ayam, dan sayur', 'price' => 14000, 'stock' => 25],
            ['category_id' => 1, 'name' => 'Mie Ayam Bakso', 'description' => 'Mie ayam dengan bakso dan pangsit goreng', 'price' => 13000, 'stock' => 20],
            ['category_id' => 1, 'name' => 'Gado-Gado', 'description' => 'Sayuran segar dengan bumbu kacang', 'price' => 12000, 'stock' => 15],

            // Makanan Ringan (category_id: 2)
            ['category_id' => 2, 'name' => 'Pisang Goreng Keju', 'description' => 'Pisang goreng crispy dengan topping keju', 'price' => 8000, 'stock' => 40],
            ['category_id' => 2, 'name' => 'Cireng Bumbu Rujak', 'description' => 'Cireng crispy dengan bumbu rujak pedas', 'price' => 5000, 'stock' => 50],
            ['category_id' => 2, 'name' => 'Batagor', 'description' => 'Bakso tahu goreng dengan saus kacang', 'price' => 8000, 'stock' => 30],

            // Minuman (category_id: 3)
            ['category_id' => 3, 'name' => 'Es Teh Manis', 'description' => 'Teh manis dingin segar', 'price' => 4000, 'stock' => 100, 'is_featured' => true],
            ['category_id' => 3, 'name' => 'Es Jeruk', 'description' => 'Jeruk peras segar dengan es', 'price' => 6000, 'stock' => 60],
            ['category_id' => 3, 'name' => 'Air Mineral', 'description' => 'Air mineral botol 600ml', 'price' => 3000, 'stock' => 100],
            ['category_id' => 3, 'name' => 'Jus Alpukat', 'description' => 'Jus alpukat creamy dengan susu', 'price' => 10000, 'stock' => 30],

            // Snack (category_id: 4)
            ['category_id' => 4, 'name' => 'Keripik Singkong', 'description' => 'Keripik singkong renyah berbagai rasa', 'price' => 5000, 'stock' => 50],
            ['category_id' => 4, 'name' => 'Roti Bakar Coklat', 'description' => 'Roti bakar dengan selai coklat', 'price' => 7000, 'stock' => 30],
        ];

        foreach ($items as $item) {
            MenuItem::create($item);
        }
    }
}
