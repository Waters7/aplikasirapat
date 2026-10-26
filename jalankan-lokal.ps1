param(
    [ValidateRange(1024, 65535)][int]$Port = 8000,
    [string]$PhpPath,
    [switch]$PrepareOnly
)

$ErrorActionPreference = 'Stop'
$appDirectory = Join-Path $PSScriptRoot 'laravel-app'

if (-not $PhpPath) {
    $candidates = @(
        Get-ChildItem 'C:\laragon\bin\php\*\php.exe' -ErrorAction SilentlyContinue |
            Sort-Object FullName -Descending | ForEach-Object FullName
        (Get-Command php.exe -ErrorAction SilentlyContinue).Source
        'C:\xampp\php\php.exe'
    )
    $PhpPath = $candidates | Where-Object { $_ -and (Test-Path -LiteralPath $_) } | Select-Object -First 1
}
if (-not $PhpPath -or -not (Test-Path -LiteralPath $PhpPath)) {
    throw 'PHP tidak ditemukan. Gunakan -PhpPath dengan lokasi lengkap php.exe (PHP 8.2 atau lebih baru).'
}
if (-not (Test-Path -LiteralPath (Join-Path $appDirectory 'vendor\autoload.php'))) {
    throw 'Dependensi PHP belum terpasang. Jalankan composer install di folder laravel-app terlebih dahulu.'
}
if (-not (Test-Path -LiteralPath (Join-Path $appDirectory 'public\build\manifest.json'))) {
    throw 'Aset belum dibangun. Jalankan npm ci lalu npm run build di folder laravel-app.'
}

function Invoke-Artisan {
    & $PhpPath artisan @args
    if ($LASTEXITCODE -ne 0) { throw ('Perintah artisan gagal: ' + ($args -join ' ')) }
}

$previewEnvironment = @{
    APP_ENV = 'preview'
    APP_URL = "http://127.0.0.1:$Port"
    APP_CONFIG_CACHE = Join-Path $appDirectory 'bootstrap\cache\preview-config.php'
    DB_CONNECTION = 'sqlite'
    DB_DATABASE = Join-Path $appDirectory 'database\preview.sqlite'
    DB_URL = ''
    SESSION_COOKIE = 'rapat_preview_session'
}
$previousEnvironment = @{}
foreach ($name in $previewEnvironment.Keys) {
    $previousEnvironment[$name] = [Environment]::GetEnvironmentVariable($name, 'Process')
}

Push-Location $appDirectory
try {
    & $PhpPath -r "exit(PHP_VERSION_ID >= 80200 && extension_loaded('pdo_sqlite') ? 0 : 1);"
    if ($LASTEXITCODE -ne 0) { throw 'Diperlukan PHP >= 8.2 dengan ekstensi pdo_sqlite.' }

    # All preview database operations are restricted to this project's SQLite file.
    foreach ($name in $previewEnvironment.Keys) {
        [Environment]::SetEnvironmentVariable($name, $previewEnvironment[$name], 'Process')
    }
    foreach ($directory in @('storage\framework\cache\data', 'storage\framework\sessions', 'storage\framework\views', 'storage\logs', 'bootstrap\cache')) {
        [System.IO.Directory]::CreateDirectory((Join-Path $appDirectory $directory)) | Out-Null
    }
    if (-not (Test-Path -LiteralPath '.env.preview')) {
        $content = [System.IO.File]::ReadAllText((Join-Path $appDirectory '.env.example'))
        $content = $content -replace '(?m)^APP_ENV=.*$', 'APP_ENV=preview'
        $content = $content -replace '(?m)^DB_CONNECTION=.*$', 'DB_CONNECTION=sqlite'
        $content = $content -replace '(?m)^DB_DATABASE=.*$', 'DB_DATABASE=database/preview.sqlite'
        [System.IO.File]::WriteAllText((Join-Path $appDirectory '.env.preview'), $content)
    }
    if ([System.IO.File]::ReadAllText((Join-Path $appDirectory '.env.preview')) -match '(?m)^APP_KEY=\s*$') {
        Invoke-Artisan key:generate --env=preview --force --no-ansi
    }
    if (-not (Test-Path -LiteralPath $previewEnvironment.DB_DATABASE)) {
        [System.IO.File]::WriteAllBytes($previewEnvironment.DB_DATABASE, [byte[]]@())
    }
    Invoke-Artisan migrate --env=preview --force --no-ansi
    Invoke-Artisan db:seed --env=preview --force --no-ansi

    Write-Host "Database uji lokal: $($previewEnvironment.DB_DATABASE)"
    Write-Host 'Pada database baru, login: admin / password. Data MySQL lama tidak dimuat.'
    if (-not $PrepareOnly) {
        Write-Host "Buka http://127.0.0.1:$Port - tekan Ctrl+C untuk berhenti."
        Invoke-Artisan serve --env=preview --host=127.0.0.1 "--port=$Port" --tries=0 --no-reload
    }
}
finally {
    foreach ($name in $previousEnvironment.Keys) {
        [Environment]::SetEnvironmentVariable($name, $previousEnvironment[$name], 'Process')
    }
    Pop-Location
}
