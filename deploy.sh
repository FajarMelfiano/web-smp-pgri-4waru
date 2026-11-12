#!/usr/bin/env bash
#
# Deploy SMP PGRI 4 Waru ke cPanel Rumahweb.
#
#   bash ~/public_html/web-smp-pgri-4waru/deploy.sh
#
# Dipakai dua cara:
#   1. Manual dari cPanel > Terminal setelah selesai mengerjakan sesuatu.
#   2. Otomatis lewat Cron Job cPanel (tiap 5 menit). Kalau tidak ada commit
#      baru, skrip berhenti dalam hitungan detik tanpa menyentuh apa pun.
#
# Tidak butuh SSH dari luar, tidak butuh password tersimpan di GitHub.
# Semua kredensial tetap di .env server.

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP="$ROOT/laravel"
BRANCH="main"

# Penanda commit yang cachenya sudah dibangun. Berkas ini yang membuat deploy
# tahu bedanya "belum ada commit baru" dan "commit-nya sudah di-pull manual
# tapi config/view cache masih versi lama".
MARKER="$APP/storage/framework/.deployed-commit"

bagian() { printf '\n\033[1m== %s\033[0m\n' "$*"; }
gagal() { printf '\033[31m%s\033[0m\n' "$*" >&2; exit 1; }

# PHP CLI cPanel sering masih 8.1/8.2; composer.json minta ^8.3.
php -r 'exit(version_compare(PHP_VERSION, "8.3.0", ">=") ? 0 : 1);' \
  || gagal "PHP CLI $(php -r 'echo PHP_VERSION;') < 8.3. Setel di cPanel > MultiPHP Manager (bagian CLI)."

[ -f "$APP/.env" ] || gagal "$APP/.env tidak ada. Salin dari .env.example lalu isi kredensial database."
[ -d "$APP/vendor" ] || gagal "$APP/vendor tidak ada. Jalankan dulu: php $ROOT/composer.phar install --no-dev --no-scripts"

cd "$ROOT"

LAMA="$(git rev-parse HEAD)"
git fetch --prune origin "$BRANCH" >/dev/null 2>&1 || gagal "git fetch gagal. Cek koneksi atau kredensial git di server."
BARU="$(git rev-parse "origin/$BRANCH")"
SUDAH="$(cat "$MARKER" 2>/dev/null || true)"

if [ "$LAMA" = "$BARU" ] && [ "$SUDAH" = "$BARU" ]; then
  echo "Sudah terbaru ($(git log -1 --format='%h %s')). Tidak ada yang dikerjakan."
  exit 0
fi

if [ "$LAMA" != "$BARU" ]; then
  bagian "Terapkan $(git log -1 --format='%h' "$LAMA") -> $(git log -1 --format='%h %s' "$BARU")"
  # --ff-only: kalau ada commit lokal di server, berhenti daripada menimpanya.
  git merge --ff-only "$BARU" || gagal "Merge gagal. Kalau pesannya soal berkas
untracked, ada file yang dibuat manual di server dengan nama sama.
Periksa dengan: git status --short"
else
  bagian "Cache belum dibangun untuk $(git rev-parse --short HEAD) - bangun ulang"
fi

BERUBAH="$(git diff --name-only "$LAMA" "$BARU")"

cd "$APP"

# composer install makan 1-2 menit, jadi hanya saat manifest berubah.
if printf '%s\n' "$BERUBAH" | grep -qE '^laravel/composer\.(json|lock)$'; then
  bagian "Dependensi berubah - composer install"
  [ -f "$ROOT/composer.phar" ] || gagal "composer.phar tidak ada di $ROOT."
  php "$ROOT/composer.phar" install --no-dev --optimize-autoloader --no-scripts --no-interaction
  # --no-scripts melewati post-autoload-dump, jadi discover dijalankan manual.
  php artisan package:discover --ansi
fi

bagian "Bersihkan cache lama"
php artisan optimize:clear

bagian "Migrasi database"
php artisan migrate --force --no-interaction

bagian "Bangun cache produksi"
# route:cache TIDAK dipakai: routes/web.php memakai Closure (/, /ppdb,
# /storage/{path}, /sitemap.xml) dan route:cache menolak route Closure.
php artisan config:cache
php artisan view:cache

# symlink() dan exec() dimatikan di shared hosting ini, jadi
# artisan storage:link gagal ("Call to undefined function exec"). ln tetap jalan.
if [ ! -L public/storage ]; then
  ln -sfn ../storage/app/public public/storage
  echo "public/storage -> ../storage/app/public"
fi

chmod -R u+rwX storage bootstrap/cache
mkdir -p "$(dirname "$MARKER")"
printf '%s' "$BARU" >"$MARKER"

bagian "Selesai: $(git -C "$ROOT" log -1 --format='%h %s')"
