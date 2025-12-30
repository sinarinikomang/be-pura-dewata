<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;



Route::get('/', function () {
    return view('welcome');
});



// Route 1: Serve file dari storage dengan folder spesifik
Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    $validFolders = ['artikel', 'galeri', 'pura', 'default'];
    
    if (!in_array($folder, $validFolders)) {
        abort(404, "Folder tidak valid: {$folder}. Gunakan: " . implode(', ', $validFolders));
    }
    
    $fullPath = storage_path("app/public/{$folder}/{$filename}");
    
    if (!file_exists($fullPath)) {
        // Coba tanpa folder
        $fullPath = storage_path("app/public/{$filename}");
        
        if (!file_exists($fullPath)) {
            abort(404, "File {$folder}/{$filename} tidak ditemukan");
        }
    }
    
    // Determine MIME type
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'pdf' => 'application/pdf',
    ];
    
    $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
    
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
        'Access-Control-Allow-Origin' => '*'
    ]);
})->where('filename', '.*');

// Route 2: Auto-detect folder untuk file
Route::get('/file/{filename}', function ($filename) {
    $folders = ['artikel', 'galeri', 'pura', 'default'];
    
    foreach ($folders as $folder) {
        $fullPath = storage_path("app/public/{$folder}/{$filename}");
        
        if (file_exists($fullPath)) {
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
            ];
            
            $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
            
            return response()->file($fullPath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000',
                'Access-Control-Allow-Origin' => '*'
            ]);
        }
    }
    
    // Coba di root
    $rootPath = storage_path("app/public/{$filename}");
    if (file_exists($rootPath)) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];
        
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        
        return response()->file($rootPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
            'Access-Control-Allow-Origin' => '*'
        ]);
    }
    
    abort(404, "File {$filename} tidak ditemukan di folder artikel/, galeri/, pura/, atau default/");
})->where('filename', '.*');

// Route 3: Debug dan test semua gambar
Route::get('/debug-images', function () {
    $folders = ['artikel', 'galeri'];
    $results = [];
    
    foreach ($folders as $folder) {
        $folderPath = storage_path("app/public/{$folder}");
        
        if (is_dir($folderPath)) {
            $files = scandir($folderPath);
            $folderFiles = [];
            
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $fullPath = $folderPath . '/' . $file;
                    $fileInfo = [
                        'name' => $file,
                        'size' => filesize($fullPath),
                        'modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
                        'urls' => [
                            'api_specific' => url("/api/images/{$folder}/{$file}"),
                            'api_auto' => url("/api/image/{$file}"),
                            'web_specific' => url("/storage/{$folder}/{$file}"),
                            'web_auto' => url("/file/{$file}"),
                            'symlink' => url("/storage/{$file}"),
                        ]
                    ];
                    
                    $folderFiles[] = $fileInfo;
                }
            }
            
            $results[$folder] = [
                'count' => count($folderFiles),
                'files' => $folderFiles
            ];
        } else {
            $results[$folder] = [
                'error' => 'Folder tidak ditemukan',
                'path' => $folderPath
            ];
        }
    }
    
    $html = "<!DOCTYPE html>
    <html>
    <head>
        <title>Debug Images</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .folder { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
            .file { background: white; padding: 10px; margin: 5px 0; border-left: 3px solid #007bff; }
            .url { font-size: 12px; color: #666; margin: 2px 0; }
            .success { color: green; }
            .error { color: red; }
            .test-btn { background: #007bff; color: white; border: none; padding: 3px 8px; margin: 2px; cursor: pointer; border-radius: 3px; }
        </style>
    </head>
    <body>
        <h1>Debug Images in Storage</h1>";
    
    foreach ($results as $folderName => $folderData) {
        $html .= "<div class='folder'>
            <h2>Folder: {$folderName}</h2>";
        
        if (isset($folderData['error'])) {
            $html .= "<p class='error'>{$folderData['error']}</p>
                     <p>Path: {$folderData['path']}</p>";
        } else {
            $html .= "<p>Total files: {$folderData['count']}</p>";
            
            foreach ($folderData['files'] as $file) {
                $html .= "<div class='file'>
                    <strong>{$file['name']}</strong> ({$file['size']} bytes, modified: {$file['modified']})
                    <div class='urls'>";
                
                foreach ($file['urls'] as $type => $url) {
                    $html .= "<div class='url'>
                        {$type}: <a href='{$url}' target='_blank'>{$url}</a>
                        <button class='test-btn' onclick=\"testImage('{$url}', '{$file['name']}')\">Test</button>
                    </div>";
                }
                
                $html .= "</div></div>";
            }
        }
        
        $html .= "</div>";
    }
    
    $html .= "
        <div id='test-result'></div>
        <script>
            function testImage(url, filename) {
                const resultDiv = document.getElementById('test-result');
                resultDiv.innerHTML = '<p>Testing: ' + filename + '</p><p>URL: ' + url + '</p><p>Loading...</p>';
                
                const img = new Image();
                img.onload = function() {
                    resultDiv.innerHTML = '<p class=\"success\">✅ SUCCESS: ' + filename + ' loaded</p>' +
                                        '<p><img src=\"' + url + '\" style=\"max-width: 300px;\"></p>' +
                                        '<p>URL: ' + url + '</p>';
                };
                img.onerror = function() {
                    resultDiv.innerHTML = '<p class=\"error\">❌ ERROR: ' + filename + ' failed to load</p>' +
                                        '<p>URL: ' + url + '</p>' +
                                        '<p>Check browser console for details</p>';
                };
                img.src = url + '?t=' + new Date().getTime(); // Cache bust
            }
        </script>
    </body>
    </html>";
    
    return $html;
});

// Route 4: Test akses gambar spesifik
Route::get('/test-image-access', function () {
    $testFiles = [
        'artikel' => [
            'sXNiFkFXyzlULpUFhNnzKE1r1fx1xd7K5s8MHme6.jpg',
            'ETnjbmoVAj7HfanN7DqUBuSNeFHNpXmET4QSUxZL.jpg'
        ],
        'galeri' => []
    ];
    
    // Cari file di galeri
    $galeriPath = storage_path('app/public/galeri');
    if (is_dir($galeriPath)) {
        $files = scandir($galeriPath);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $testFiles['galeri'][] = $file;
                if (count($testFiles['galeri']) >= 2) break;
            }
        }
    }
    
    $results = [];
    
    foreach ($testFiles as $folder => $files) {
        foreach ($files as $filename) {
            $paths = [
                'storage' => storage_path("app/public/{$folder}/{$filename}"),
                'public' => public_path("storage/{$folder}/{$filename}"),
            ];
            
            $urls = [
                'api_folder' => url("/api/images/{$folder}/{$filename}"),
                'api_auto' => url("/api/image/{$filename}"),
                'web_folder' => url("/storage/{$folder}/{$filename}"),
                'web_auto' => url("/file/{$filename}"),
            ];
            
            $exists = [];
            foreach ($paths as $type => $path) {
                $exists[$type] = file_exists($path);
            }
            
            $results[] = [
                'folder' => $folder,
                'filename' => $filename,
                'exists' => $exists,
                'urls' => $urls
            ];
        }
    }
    
    return response()->json([
        'success' => true,
        'test_files' => $results,
        'storage_path' => storage_path('app/public'),
        'public_path' => public_path('storage'),
        'symlink_exists' => is_link(public_path('storage')),
        'symlink_target' => is_link(public_path('storage')) ? readlink(public_path('storage')) : 'Not a symlink'
    ]);
});

// Route 5: Direct image access (backward compatibility)
Route::get('/img/{folder}/{filename}', function ($folder, $filename) {
    return redirect("/storage/{$folder}/{$filename}", 301);
});

// Route 6: Serve default image jika tidak ditemukan
Route::get('/default-image', function () {
    $defaultPath = storage_path('app/public/default/default.jpg');
    
    if (!file_exists($defaultPath)) {
        // Create default image jika tidak ada
        $defaultDir = storage_path('app/public/default');
        if (!is_dir($defaultDir)) {
            mkdir($defaultDir, 0755, true);
        }
        
        // Create a simple default image
        $im = imagecreatetruecolor(400, 300);
        $bgColor = imagecolorallocate($im, 240, 240, 240);
        $textColor = imagecolorallocate($im, 180, 180, 180);
        imagefill($im, 0, 0, $bgColor);
        imagestring($im, 5, 120, 140, 'No Image Available', $textColor);
        imagejpeg($im, $defaultPath, 80);
        imagedestroy($im);
    }
    
    return response()->file($defaultPath, [
        'Content-Type' => 'image/jpeg',
        'Cache-Control' => 'public, max-age=3600'
    ]);
});