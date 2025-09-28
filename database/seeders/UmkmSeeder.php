<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Umkm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UmkmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample UMKM users
        $umkmUsers = [
            [
                'name' => 'Sari Dewi',
                'email' => 'sari.dewi@example.com',
                'role' => 'umkm',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'role' => 'umkm',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Rina Kartika',
                'email' => 'rina.kartika@example.com',
                'role' => 'umkm',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($umkmUsers as $userData) {
            $user = User::create($userData);
            
            // Create UMKM for each user
            $umkmData = $this->getUmkmData($user->id, $user->name);
            Umkm::create($umkmData);
        }

        // Create regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);
    }

    private function getUmkmData($userId, $ownerName): array
    {
        $umkmSamples = [
            [
                'nama_umkm' => 'Warung Bu Sari',
                'deskripsi' => 'Warung makan tradisional dengan menu masakan Jawa yang lezat dan terjangkau. Menyajikan nasi gudeg, ayam bakar, dan berbagai lauk pauk tradisional.',
                'kategori' => 'Makanan & Minuman',
                'alamat' => 'Jl. Malioboro No. 15, Yogyakarta',
                'no_telepon' => '081234567890',
                'email' => 'warungbusari@gmail.com',
                'latitude' => -7.7956,
                'longitude' => 110.3695,
                'status' => 'active',
            ],
            [
                'nama_umkm' => 'Konveksi Budi Jaya',
                'deskripsi' => 'Usaha konveksi yang memproduksi berbagai jenis pakaian seperti seragam sekolah, kaos, dan kemeja dengan kualitas terbaik.',
                'kategori' => 'Fashion & Tekstil',
                'alamat' => 'Jl. Gajah Mada No. 45, Semarang',
                'no_telepon' => '082345678901',
                'email' => 'budijayakonveksi@gmail.com',
                'latitude' => -6.9919,
                'longitude' => 110.4203,
                'status' => 'active',
            ],
            [
                'nama_umkm' => 'Kerajinan Rina Art',
                'deskripsi' => 'Membuat berbagai kerajinan tangan seperti tas rajut, aksesoris wanita, dan souvenir unik dengan desain modern dan tradisional.',
                'kategori' => 'Kerajinan Tangan',
                'alamat' => 'Jl. Diponegoro No. 23, Bandung',
                'no_telepon' => '083456789012',
                'email' => 'rinaart.craft@gmail.com',
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'status' => 'active',
            ],
        ];

        // Get random UMKM data or cycle through them
        static $counter = 0;
        $umkmData = $umkmSamples[$counter % count($umkmSamples)];
        $counter++;

        // Add user_id to the data
        $umkmData['user_id'] = $userId;
        
        return $umkmData;
    }
}
