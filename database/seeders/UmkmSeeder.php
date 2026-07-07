<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Umkm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UmkmSeeder extends Seeder
{
    private array $umkmSamples = [
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
        [
            'nama_umkm' => 'Kopi Nusantara',
            'deskripsi' => 'Kedai kopi lokal yang menyajikan kopi robusta dan arabika dari berbagai daerah Indonesia dengan suasana santai.',
            'kategori' => 'Makanan & Minuman',
            'alamat' => 'Jl. Ahmad Yani No. 12, Surabaya',
            'no_telepon' => '081298765401',
            'email' => 'kopinusantara@gmail.com',
            'latitude' => -7.2575,
            'longitude' => 112.7521,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Bakery Manis Lestari',
            'deskripsi' => 'Toko roti dan kue rumahan dengan pilihan cake ulang tahun, donat, dan pastry fresh every day.',
            'kategori' => 'Makanan & Minuman',
            'alamat' => 'Jl. Sudirman No. 78, Jakarta',
            'no_telepon' => '081355443322',
            'email' => 'bakerymanis@gmail.com',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Sembako Murah Jaya',
            'deskripsi' => 'Toko sembako lengkap untuk kebutuhan harian dengan harga bersahabat dan pelayanan cepat.',
            'kategori' => 'Retail',
            'alamat' => 'Jl. Veteran No. 9, Malang',
            'no_telepon' => '082112223334',
            'email' => 'sembakomurahjaya@gmail.com',
            'latitude' => -7.9819,
            'longitude' => 112.6265,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Batik Laksmi',
            'deskripsi' => 'Produksi batik tulis dan cap dengan motif khas daerah yang elegan untuk kebutuhan harian maupun resmi.',
            'kategori' => 'Fashion & Tekstil',
            'alamat' => 'Jl. Parangtritis No. 21, Yogyakarta',
            'no_telepon' => '081377889900',
            'email' => 'batiklaksmi@gmail.com',
            'latitude' => -7.8306,
            'longitude' => 110.3891,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Dapur Mami Rasa',
            'deskripsi' => 'Catering rumahan dan nasi box untuk acara keluarga, rapat, dan kebutuhan konsumsi harian.',
            'kategori' => 'Makanan & Minuman',
            'alamat' => 'Jl. Kartini No. 31, Solo',
            'no_telepon' => '082233445566',
            'email' => 'dapurrasamami@gmail.com',
            'latitude' => -7.5755,
            'longitude' => 110.8243,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Tahu Crispy Nabila',
            'deskripsi' => 'Camilan renyah dan gurih dengan varian pedas, original, dan balado yang cocok untuk semua usia.',
            'kategori' => 'Makanan & Minuman',
            'alamat' => 'Jl. Pandanaran No. 54, Semarang',
            'no_telepon' => '081244556677',
            'email' => 'tahucrispynabila@gmail.com',
            'latitude' => -6.9932,
            'longitude' => 110.4205,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Sepatu Karya Anak Bangsa',
            'deskripsi' => 'UMKM sepatu lokal dengan model kasual dan formal yang dibuat dengan bahan berkualitas.',
            'kategori' => 'Fashion & Tekstil',
            'alamat' => 'Jl. Industri No. 88, Bandung',
            'no_telepon' => '082212345678',
            'email' => 'sepatukaryaab@gmail.com',
            'latitude' => -6.9147,
            'longitude' => 107.6098,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Rajut Fitara',
            'deskripsi' => 'Produk rajutan berupa tas, dompet, dan aksesori handmade dengan desain unik dan kekinian.',
            'kategori' => 'Kerajinan Tangan',
            'alamat' => 'Pancakarya Blok 53 No 328, Semarang',
            'no_telepon' => '085326268654',
            'email' => 'rajutfitara@gmail.com',
            'latitude' => -6.9667,
            'longitude' => 110.4167,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Bunda Frozen Food',
            'deskripsi' => 'Penyedia makanan beku seperti nugget, sosis, dan dimsum untuk kebutuhan keluarga sehari-hari.',
            'kategori' => 'Makanan & Minuman',
            'alamat' => 'Jl. Letjend Suprapto No. 19, Bogor',
            'no_telepon' => '081266778899',
            'email' => 'bundafrozenfood@gmail.com',
            'latitude' => -6.5971,
            'longitude' => 106.8060,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Aksesoris Cantika',
            'deskripsi' => 'Menjual aksesoris wanita seperti anting, gelang, kalung, dan bros dengan pilihan desain modern.',
            'kategori' => 'Kerajinan Tangan',
            'alamat' => 'Jl. Asia Afrika No. 40, Bandung',
            'no_telepon' => '082133224455',
            'email' => 'aksesoriscantika@gmail.com',
            'latitude' => -6.9219,
            'longitude' => 107.6095,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Martabak Bang Eko',
            'deskripsi' => 'Martabak manis dan telur dengan topping beragam, cocok untuk camilan malam keluarga.',
            'kategori' => 'Makanan & Minuman',
            'alamat' => 'Jl. Veteran No. 66, Yogyakarta',
            'no_telepon' => '081377700123',
            'email' => 'martabakbangeko@gmail.com',
            'latitude' => -7.8014,
            'longitude' => 110.3648,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Percetakan Sinar Abadi',
            'deskripsi' => 'Layanan percetakan untuk banner, brosur, stiker, dan kebutuhan promosi usaha kecil.',
            'kategori' => 'Jasa',
            'alamat' => 'Jl. Dr. Wahidin No. 11, Semarang',
            'no_telepon' => '082155667788',
            'email' => 'percetakansinarabadi@gmail.com',
            'latitude' => -6.9817,
            'longitude' => 110.4091,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Kue Kering Nenek',
            'deskripsi' => 'Kue kering rumahan untuk hampers, acara keluarga, dan suguhan hari raya.',
            'kategori' => 'Makanan & Minuman',
            'alamat' => 'Jl. Dipatiukur No. 3, Bandung',
            'no_telepon' => '081255443210',
            'email' => 'kuekeringnenek@gmail.com',
            'latitude' => -6.8933,
            'longitude' => 107.6107,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Bengkel Motor Maju',
            'deskripsi' => 'Bengkel servis dan perawatan motor dengan layanan tune up, ganti oli, dan servis ringan.',
            'kategori' => 'Jasa',
            'alamat' => 'Jl. MT Haryono No. 27, Surabaya',
            'no_telepon' => '082188990011',
            'email' => 'bengkelmotormaju@gmail.com',
            'latitude' => -7.2762,
            'longitude' => 112.7942,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Sablon Kaos Sprint',
            'deskripsi' => 'Layanan sablon kaos untuk komunitas, event, dan kebutuhan seragam dengan kualitas rapi.',
            'kategori' => 'Fashion & Tekstil',
            'alamat' => 'Jl. Merdeka No. 5, Malang',
            'no_telepon' => '081233445599',
            'email' => 'sablonkaossprint@gmail.com',
            'latitude' => -7.9839,
            'longitude' => 112.6214,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Pancing Sejahtera',
            'deskripsi' => 'Toko perlengkapan memancing dan kebutuhan outdoor untuk hobi dan komunitas.',
            'kategori' => 'Retail',
            'alamat' => 'Jl. Pahlawan No. 82, Medan',
            'no_telepon' => '082244556600',
            'email' => 'pancingsejahtera@gmail.com',
            'latitude' => 3.5952,
            'longitude' => 98.6722,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Keripik Mak Nyus',
            'deskripsi' => 'Produksi keripik singkong dan pisang dengan rasa pedas manis dan kemasan menarik.',
            'kategori' => 'Makanan & Minuman',
            'alamat' => 'Jl. Ahmad Dahlan No. 14, Solo',
            'no_telepon' => '081300112233',
            'email' => 'keripikmaknyus@gmail.com',
            'latitude' => -7.5575,
            'longitude' => 110.8317,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Florist Melati Indah',
            'deskripsi' => 'Toko bunga dan rangkaian hadiah untuk wisuda, pernikahan, dan ucapan selamat.',
            'kategori' => 'Jasa',
            'alamat' => 'Jl. S Parman No. 60, Jakarta',
            'no_telepon' => '081122334455',
            'email' => 'floristmelatiindah@gmail.com',
            'latitude' => -6.1754,
            'longitude' => 106.8272,
            'status' => 'active',
        ],
        [
            'nama_umkm' => 'Keramik Desa Pundi',
            'deskripsi' => 'Kerajinan keramik handmade untuk dekorasi rumah, vas, dan suvenir.',
            'kategori' => 'Kerajinan Tangan',
            'alamat' => 'Jl. Raya Cirebon No. 7, Cirebon',
            'no_telepon' => '082199887766',
            'email' => 'keramikdesapundi@gmail.com',
            'latitude' => -6.7370,
            'longitude' => 108.5523,
            'status' => 'active',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $umkmUsers = [];

        for ($i = 1; $i <= 20; $i++) {
            $sample = $this->umkmSamples[$i - 1];

            $umkmUsers[] = [
                'name' => $sample['nama_umkm'],
                'email' => 'umkm' . $i . '@example.com',
                'role' => 'user',
                'user_type' => 'umkm',
                'password' => Hash::make('password'),
            ];
        }

        foreach ($umkmUsers as $index => $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $umkmData = $this->getUmkmData($user->id, $index);
            Umkm::updateOrCreate(
                ['user_id' => $user->id],
                $umkmData
            );
        }

        // Create regular user
        User::updateOrCreate([
            'email' => 'user@example.com',
        ], [
            'name' => 'Regular User',
            'role' => 'user',
            'user_type' => 'user',
            'password' => Hash::make('password'),
        ]);
    }

    private function getUmkmData(int $userId, int $index): array
    {
        $umkmData = $this->umkmSamples[$index % count($this->umkmSamples)];

        // Add user_id to the data
        $umkmData['user_id'] = $userId;
        
        return $umkmData;
    }
}
