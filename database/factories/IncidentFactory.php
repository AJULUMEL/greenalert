<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incident>
 */
class IncidentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $samples = [
            [
                'title' => 'Kerusakan kompresor ruang pendingin (Cold Storage)',
                'description' => "Pukul 03:20, kompresor utama ruang pendingin mengalami kegagalan mendadak. Suhu naik di atas batas spesifikasi dan ada risiko kehilangan produk susu pasteurisasi. Tindakan sementara: alihkan stok ke ruang pendingin cadangan, kontak teknisi HVAC, catat lot terdampak untuk inspeksi mikrobiologi.",
                'severity' => 'High',
                'status' => 'Open',
            ],
            [
                'title' => 'Kontaminasi mikroba pada batch susu segar',
                'description' => "Hasil uji kualitas menunjukkan kenaikan jumlah koloni dan deteksi kontaminan pada batch pasteurisasi produksi pagi. Batch ditahan, proses recall sedang dipertimbangkan. Lacak supplier, periksa pembersihan CIP, lakukan uji ulang dan analisis akar penyebab.",
                'severity' => 'Critical',
                'status' => 'On Progress',
            ],
            [
                'title' => 'Keterlambatan pasokan susu dari peternak mitra',
                'description' => "Suply chain: truk pengangkut dari koperasi peternak terlambat 6 jam karena kerusakan kendaraan dan kondisi jalan berlumpur. Dampak: kapasitas produksi di lini pasteurisasi berkurang, jadwal produksi bergeser.",
                'severity' => 'Medium',
                'status' => 'On Progress',
            ],
            [
                'title' => 'Tumpahan bahan pembersih di area produksi',
                'description' => "Tumpahan cairan pembersih (alkalinity tinggi) terjadi di lorong antara mesin pengemasan dan ruang QA. Area dibatasi, petugas kebersihan menggunakan PPE, sampel diambil untuk memastikan tidak ada kontak dengan produk.",
                'severity' => 'High',
                'status' => 'Open',
            ],
            [
                'title' => 'Kecelakaan kerja: terpeleset dan terkilir',
                'description' => "Operator packaging terpeleset saat membersihkan area basah dan mengalami terkilir pergelangan kaki. Pertolongan pertama diberikan, laporan K3 dibuat, evaluasi prosedur kerja dan tanda peringatan dilakukan.",
                'severity' => 'Medium',
                'status' => 'Resolved',
            ],
            [
                'title' => 'Pemadaman listrik mendadak pada lini pasteurisasi',
                'description' => "Pada jam 01:15 terjadi pemadaman listrik singkat yang memicu trip pada panel utama sehingga lini pasteurisasi berhenti. Tim maintenance dan kelistrikan menormalkan sistem, produk yang setengah proses dikarantina untuk pemeriksaan kualitas.",
                'severity' => 'High',
                'status' => 'On Progress',
            ],
            [
                'title' => 'Masalah kualitas: pH susu di luar spesifikasi',
                'description' => "Hasil uji inline menunjukkan pH melebihi rentang yang ditentukan pada beberapa sampel. Lakukan penarikan batch, cek kondisi pakan sapi pada supplier dan prosedur handling pasca-panen.",
                'severity' => 'High',
                'status' => 'On Progress',
            ],
            [
                'title' => 'Gangguan pada mesin pengemasan otomatis (line jam)',
                'description' => "Mesin pengemasan mengalami paper jam dan sensor ganda menyebabkan produksi tersendat. Operator menghentikan mesin, tim teknisi mengatasi masalah mekanik dan kalibrasi sensor.",
                'severity' => 'Low',
                'status' => 'Resolved',
            ],
            [
                'title' => 'Kebocoran pipa susu mentah di area penerimaan',
                'description' => "Ditemukan kebocoran pada sambungan pipa susu mentah saat transfer dari truk tangki. Transfer dihentikan, area dibersihkan, sambungan diganti, dan susu yang tumpah diuji sebelum diputuskan untuk diproses atau dibuang.",
                'severity' => 'High',
                'status' => 'Open',
            ],
            [
                'title' => 'Gangguan HVAC di ruang penyimpanan bahan baku',
                'description' => "Sistem HVAC ruang bahan baku menunjukkan fluktuasi suhu yang dapat memengaruhi stabilitas ragi/kultur. Kontrol suhu sementara diaktifkan, vendor HVAC dijadwalkan untuk pemeriksaan.",
                'severity' => 'Medium',
                'status' => 'On Progress',
            ],
        ];

        $sample = $this->faker->randomElement($samples);

        return [
            'title' => $sample['title'],
            'description' => $sample['description'],
            'severity' => $sample['severity'],
            'status' => $sample['status'],
            'reported_by' => User::factory(),
            'incident_date' => $this->faker->dateTimeBetween('-60 days', 'now'),
        ];
    }

    /**
     * Define critical severity state
     */
    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'Critical',
        ]);
    }

    /**
     * Define high severity state
     */
    public function high(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'High',
        ]);
    }

    /**
     * Define open status state
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Open',
        ]);
    }

    /**
     * Define in progress status state
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'On Progress',
        ]);
    }

    /**
     * Define resolved status state
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Resolved',
        ]);
    }
}
