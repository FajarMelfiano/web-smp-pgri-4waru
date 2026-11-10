<?php

return [

    // Identitas
    'nama' => 'SMP PGRI 4 Waru',
    'npsn' => '20540020',
    'naungan' => 'YPLP DASMEN PGRI Jawa Timur',
    't_berdiri' => 1979,
    'sk' => '421.3/2829/404.3.1/1979',

    // Alamat & kontak
    'alamat' => 'Jl. Anggrek No. 109, Kureksari',
    'kelurahan' => 'Kureksari',
    'kecamatan' => 'Waru',
    'kabupaten' => 'Sidoarjo',
    'provinsi' => 'Jawa Timur',
    'pos' => '61256',
    'telepon' => '(031) 8538447',
    'email' => 'smp.pgri4waru@gmail.com',
    'website' => 'https://smppgri4waru.id', // placeholder
    'instagram' => 'https://www.instagram.com/smppgri4waru/',

    // Properti
    'luas_tanah' => 660,
    'pukul' => 'pagi',
    'hari' => 6,
    'koordinat' => '-7.3619, 112.7327',

    // Navigasi utama
    'nav' => [
        ['label' => 'Beranda', 'uri' => '/'],
        ['label' => 'Profil', 'uri' => '/profil'],
        ['label' => 'Akademik', 'uri' => '/akademik'],
        ['label' => 'PPDB', 'uri' => '/ppdb'],
        ['label' => 'Kontak', 'uri' => '/kontak'],
    ],

    // Mata pelajaran (11 mapel) — dikelompokkan di blade
    'mapel' => [
        'Bahasa Indonesia',
        'Bahasa Inggris',
        'Matematika',
        'IPA',
        'Sejarah',
        'Geografi',
        'Pendidikan Kewarganegaraan',
        'Senibina',
        'Prakarya',
        'Seni Rupa',
        'PJOK',
    ],

];
