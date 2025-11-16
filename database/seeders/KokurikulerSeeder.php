<?php

namespace Database\Seeders;

use App\Models\KokurikulerDimension;
use App\Models\KokurikulerSubdimension;
use App\Models\KokurikulerTemplate;
use Illuminate\Database\Seeder;

class KokurikulerSeeder extends Seeder
{
    public function run()
    {
        $dimensions = [
            [
                'name' => 'Keimanan',
                'description' => 'Kemampuan memahami, menghayati, dan mengamalkan ajaran agama',
                'subdimensions' => [
                    [
                        'name' => 'Pemahaman Ajaran Agama',
                        'berkembang' => 'memahami ajaran agama secara dasar',
                        'cakap' => 'memahami dan menghayati ajaran agama dengan baik',
                        'mahir' => 'memahami, menghayati, dan mengamalkan ajaran agama secara konsisten',
                    ],
                    [
                        'name' => 'Pengamalan Ibadah',
                        'berkembang' => 'melakukan ibadah dengan bimbingan',
                        'cakap' => 'melakukan ibadah secara mandiri dan teratur',
                        'mahir' => 'melakukan ibadah dengan kesadaran penuh dan konsisten',
                    ],
                ],
            ],
            [
                'name' => 'Kewargaan',
                'description' => 'Kemampuan memahami hak dan kewajiban sebagai warga negara',
                'subdimensions' => [
                    [
                        'name' => 'Rasa Cinta Tanah Air',
                        'berkembang' => 'menunjukkan rasa cinta tanah air secara sederhana',
                        'cakap' => 'menunjukkan rasa cinta tanah air melalui tindakan nyata',
                        'mahir' => 'menunjukkan rasa cinta tanah air yang kuat dan konsisten',
                    ],
                    [
                        'name' => 'Kesadaran Berbangsa dan Bernegara',
                        'berkembang' => 'memahami identitas sebagai bagian dari bangsa',
                        'cakap' => 'menunjukkan kesadaran berbangsa dan bernegara dalam kehidupan sehari-hari',
                        'mahir' => 'menunjukkan kesadaran berbangsa dan bernegara yang tinggi dan konsisten',
                    ],
                ],
            ],
            [
                'name' => 'Penalaran Kritis',
                'description' => 'Kemampuan berpikir kritis dan logis dalam menyelesaikan masalah',
                'subdimensions' => [
                    [
                        'name' => 'Kemampuan Analisis',
                        'berkembang' => 'menganalisis informasi secara sederhana',
                        'cakap' => 'menganalisis informasi dengan baik dan sistematis',
                        'mahir' => 'menganalisis informasi secara mendalam dan kritis',
                    ],
                    [
                        'name' => 'Kemampuan Menyelesaikan Masalah',
                        'berkembang' => 'menyelesaikan masalah sederhana dengan bimbingan',
                        'cakap' => 'menyelesaikan masalah secara mandiri dan sistematis',
                        'mahir' => 'menyelesaikan masalah kompleks dengan solusi yang kreatif dan efektif',
                    ],
                ],
            ],
            [
                'name' => 'Kreativitas',
                'description' => 'Kemampuan menciptakan ide-ide baru dan inovatif',
                'subdimensions' => [
                    [
                        'name' => 'Kemampuan Berpikir Kreatif',
                        'berkembang' => 'menghasilkan ide-ide sederhana',
                        'cakap' => 'menghasilkan ide-ide yang bervariasi dan menarik',
                        'mahir' => 'menghasilkan ide-ide inovatif dan orisinal',
                    ],
                    [
                        'name' => 'Kemampuan Menciptakan Karya',
                        'berkembang' => 'menciptakan karya sederhana dengan bimbingan',
                        'cakap' => 'menciptakan karya yang menarik dan bermakna',
                        'mahir' => 'menciptakan karya yang inovatif, orisinal, dan bermakna',
                    ],
                ],
            ],
            [
                'name' => 'Kolaborasi',
                'description' => 'Kemampuan bekerja sama dalam tim',
                'subdimensions' => [
                    [
                        'name' => 'Kemampuan Bekerja Sama',
                        'berkembang' => 'bekerja sama dalam kelompok dengan bimbingan',
                        'cakap' => 'bekerja sama dalam kelompok secara efektif',
                        'mahir' => 'bekerja sama dalam kelompok dengan kepemimpinan yang baik',
                    ],
                    [
                        'name' => 'Kemampuan Berkomunikasi dalam Tim',
                        'berkembang' => 'berkomunikasi dalam tim secara sederhana',
                        'cakap' => 'berkomunikasi dalam tim dengan baik dan efektif',
                        'mahir' => 'berkomunikasi dalam tim dengan sangat baik dan memfasilitasi kolaborasi',
                    ],
                ],
            ],
            [
                'name' => 'Kemandirian',
                'description' => 'Kemampuan bekerja mandiri dan bertanggung jawab',
                'subdimensions' => [
                    [
                        'name' => 'Kemampuan Bekerja Mandiri',
                        'berkembang' => 'bekerja mandiri dengan pengawasan',
                        'cakap' => 'bekerja mandiri dengan baik dan terorganisir',
                        'mahir' => 'bekerja mandiri dengan disiplin tinggi dan hasil yang maksimal',
                    ],
                    [
                        'name' => 'Tanggung Jawab',
                        'berkembang' => 'menunjukkan tanggung jawab terhadap tugas yang diberikan',
                        'cakap' => 'menunjukkan tanggung jawab yang tinggi terhadap tugas dan komitmen',
                        'mahir' => 'menunjukkan tanggung jawab yang sangat tinggi dan konsisten',
                    ],
                ],
            ],
            [
                'name' => 'Kesehatan',
                'description' => 'Kemampuan menjaga kesehatan fisik dan mental',
                'subdimensions' => [
                    [
                        'name' => 'Kesehatan Fisik',
                        'berkembang' => 'memahami pentingnya menjaga kesehatan fisik',
                        'cakap' => 'menjaga kesehatan fisik dengan baik dan teratur',
                        'mahir' => 'menjaga kesehatan fisik dengan sangat baik dan menjadi contoh',
                    ],
                    [
                        'name' => 'Kesehatan Mental',
                        'berkembang' => 'memahami pentingnya menjaga kesehatan mental',
                        'cakap' => 'menjaga kesehatan mental dengan baik',
                        'mahir' => 'menjaga kesehatan mental dengan sangat baik dan membantu orang lain',
                    ],
                ],
            ],
            [
                'name' => 'Komunikasi',
                'description' => 'Kemampuan berkomunikasi secara efektif',
                'subdimensions' => [
                    [
                        'name' => 'Kemampuan Berbicara',
                        'berkembang' => 'berbicara dengan jelas dan sederhana',
                        'cakap' => 'berbicara dengan jelas, lancar, dan menarik',
                        'mahir' => 'berbicara dengan sangat jelas, lancar, menarik, dan persuasif',
                    ],
                    [
                        'name' => 'Kemampuan Menulis',
                        'berkembang' => 'menulis dengan struktur yang sederhana',
                        'cakap' => 'menulis dengan struktur yang baik dan jelas',
                        'mahir' => 'menulis dengan struktur yang sangat baik, jelas, dan menarik',
                    ],
                ],
            ],
        ];

        foreach ($dimensions as $dimensionData) {
            $dimension = KokurikulerDimension::firstOrCreate(
                ['name' => $dimensionData['name']],
                [
                    'description' => $dimensionData['description'],
                ]
            );

            foreach ($dimensionData['subdimensions'] as $subdimensionData) {
                KokurikulerSubdimension::firstOrCreate(
                    [
                        'dimension_id' => $dimension->id,
                        'name' => $subdimensionData['name'],
                    ],
                    [
                        'berkembang' => $subdimensionData['berkembang'],
                        'cakap' => $subdimensionData['cakap'],
                        'mahir' => $subdimensionData['mahir'],
                    ]
                );
            }
        }

        KokurikulerTemplate::firstOrCreate(
            ['level' => 'tinggi'],
            ['template_text' => 'Ananda {{ student_name }} sudah baik dalam {{ list_of_descriptions }}.']
        );

        KokurikulerTemplate::firstOrCreate(
            ['level' => 'rendah'],
            ['template_text' => 'Ananda {{ student_name }} perlu bantuan dalam {{ list_of_descriptions }}.']
        );
    }
}

