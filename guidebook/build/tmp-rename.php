#!/usr/bin/env php
<?php
/**
 * Sementara mengganti nama akun uji nyata (baris 21 & 23) supaya tidak muncul
 * di tangkapan layar, lalu mengembalikannya. Data asli disimpan di
 * /tmp/opencode/realnames.json.
 *
 *   php tmp-rename.php apply
 *   php tmp-rename.php restore
 */
$root = '/home/melfiano/Documents/web-smp-pgri-4waru/laravel';
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PpdbAccount;

const STORE = '/tmp/opencode/realnames.json';
$mode = $argv[1] ?? 'apply';

if ($mode === 'apply') {
    $ids = [21, 23];
    $saved = [];
    foreach (PpdbAccount::whereIn('id', $ids)->get() as $a) {
        $saved[$a->id] = $a->nama_lengkap;
        $a->nama_lengkap = $a->id === 21 ? 'Peserta Uji Satu' : 'Peserta Uji Dua';
        $a->save();
    }
    file_put_contents(STORE, json_encode($saved));
    echo "applied: ".json_encode($saved)."\n";
} else {
    $saved = json_decode((string) file_get_contents(STORE), true) ?: [];
    foreach ($saved as $id => $nama) {
        $a = PpdbAccount::find((int) $id);
        if ($a) {
            $a->nama_lengkap = $nama;
            $a->save();
        }
    }
    echo "restored: ".json_encode($saved)."\n";
}