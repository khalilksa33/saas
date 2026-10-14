<?php
/**
 * Icon & Favicon Generator for iLoveDigital
 * Generates PNGs and multi-resolution ICO file using PHP GD
 */

function createIcon(int $size, bool $withBorder = true) {
    $img = imagecreatetruecolor($size, $size);
    imagealphablending($img, false);
    imagesavealpha($img, true);

    $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
    imagefilledrectangle($img, 0, 0, $size, $size, $transparent);
    imagealphablending($img, true);

    // Draw background rounded box
    $radius = (int)($size * 0.25);
    $bgR = 14; $bgG = 18; $bgB = 34; // Deep modern navy
    $bgColor = imagecolorallocate($img, $bgR, $bgG, $bgB);

    // Draw rounded rectangle
    drawRoundedRect($img, 0, 0, $size - 1, $size - 1, $radius, $bgColor);

    if ($withBorder && $size >= 32) {
        $borderColor = imagecolorallocatealpha($img, 129, 140, 248, 60);
        drawRoundedRectBorder($img, 0, 0, $size - 1, $size - 1, $radius, $borderColor);
    }

    // Draw Heart with gradient colors
    $cx = $size / 2;
    $cy = $size * 0.48;
    $scale = $size / 48.0;

    // Heart sampling points
    for ($y = (int)($size * 0.18); $y <= (int)($size * 0.82); $y++) {
        $normY = ($y - $size * 0.18) / ($size * 0.64); // 0 to 1
        // Interpolate color: indigo -> pink -> cyan
        if ($normY < 0.5) {
            $t = max(0.0, min(1.0, $normY / 0.5));
            $r = (int)round(99 + (236 - 99) * $t);
            $g = (int)round(102 + (72 - 102) * $t);
            $b = (int)round(241 + (153 - 241) * $t);
        } else {
            $t = max(0.0, min(1.0, ($normY - 0.5) / 0.5));
            $r = (int)round(236 + (6 - 236) * $t);
            $g = (int)round(72 + (182 - 72) * $t);
            $b = (int)round(153 + (212 - 153) * $t);
        }
        $r = max(0, min(255, $r));
        $g = max(0, min(255, $g));
        $b = max(0, min(255, $b));
        $lineColor = imagecolorallocate($img, $r, $g, $b);

        // Heart equation mapping
        for ($x = (int)($size * 0.18); $x <= (int)($size * 0.82); $x++) {
            $nx = ($x - $cx) / (14 * $scale);
            $ny = ($cy - $y) / (14 * $scale);
            // Classic 2D heart implicit equation: (x^2 + y^2 - 1)^3 - x^2 * y^3 <= 0
            $val = pow($nx * $nx + $ny * $ny - 1.0, 3) - ($nx * $nx * pow($ny, 3));
            if ($val <= 0) {
                imagesetpixel($img, $x, $y, $lineColor);
            }
        }
    }

    // Draw Digital Pulse Wave through center
    $pulseColor = imagecolorallocate($img, 255, 255, 255);
    $cyanAccent = imagecolorallocate($img, 56, 189, 248);

    $pulsePoints = [
        [$size * 0.22, $size * 0.48],
        [$size * 0.36, $size * 0.48],
        [$size * 0.42, $size * 0.34],
        [$size * 0.48, $size * 0.62],
        [$size * 0.54, $size * 0.38],
        [$size * 0.60, $size * 0.52],
        [$size * 0.66, $size * 0.48],
        [$size * 0.78, $size * 0.48],
    ];

    $thickness = max(1, (int)($size * 0.05));
    for ($i = 0; $i < count($pulsePoints) - 1; $i++) {
        drawLineThick($img, (int)$pulsePoints[$i][0], (int)$pulsePoints[$i][1],
                            (int)$pulsePoints[$i+1][0], (int)$pulsePoints[$i+1][1],
                            $pulseColor, $thickness);
    }

    // Spark nodes
    if ($size >= 32) {
        $nodeRadius = max(1, (int)($size * 0.04));
        imagefilledellipse($img, (int)($size * 0.48), (int)($size * 0.62), $nodeRadius * 2, $nodeRadius * 2, $pulseColor);
        imagefilledellipse($img, (int)($size * 0.54), (int)($size * 0.38), (int)($nodeRadius * 1.5), (int)($nodeRadius * 1.5), $cyanAccent);
    }

    return $img;
}

function drawRoundedRect($img, $x1, $y1, $x2, $y2, $radius, $color) {
    imagefilledrectangle($img, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
    imagefilledrectangle($img, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
    imagefilledellipse($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
}

function drawRoundedRectBorder($img, $x1, $y1, $x2, $y2, $radius, $color) {
    imageline($img, $x1 + $radius, $y1, $x2 - $radius, $y1, $color);
    imageline($img, $x1 + $radius, $y2, $x2 - $radius, $y2, $color);
    imageline($img, $x1, $y1 + $radius, $x1, $y2 - $radius, $color);
    imageline($img, $x2, $y1 + $radius, $x2, $y2 - $radius, $color);
    imagearc($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color);
    imagearc($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color);
    imagearc($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color);
    imagearc($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 0, 90, $color);
}

function drawLineThick($image, $x1, $y1, $x2, $y2, $color, $thick = 1) {
    if ($thick <= 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
    }
    $k = ($y2 - $y1) / ($x2 - $x1);
    $a = $t / sqrt(1 + pow($k, 2));
    $points = [
        round($x1 - (1 + $k) * $a), round($y1 + (1 - $k) * $a),
        round($x1 - (1 - $k) * $a), round($y1 - (1 + $k) * $a),
        round($x2 + (1 + $k) * $a), round($y2 - (1 - $k) * $a),
        round($x2 + (1 - $k) * $a), round($y2 + (1 + $k) * $a),
    ];
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
}

// Generate Icons
$sizes = [
    16 => 'c:/Users/Khalil/OneDrive/saas/assets/images/favicon-16x16.png',
    32 => 'c:/Users/Khalil/OneDrive/saas/assets/images/favicon-32x32.png',
    180 => 'c:/Users/Khalil/OneDrive/saas/assets/images/apple-touch-icon.png',
    192 => 'c:/Users/Khalil/OneDrive/saas/assets/images/icon-192x192.png',
    512 => 'c:/Users/Khalil/OneDrive/saas/assets/images/icon-512x512.png',
];

$pngDataArray = [];

foreach ($sizes as $size => $path) {
    $img = createIcon($size);
    imagepng($img, $path);
    ob_start();
    imagepng($img);
    $pngDataArray[$size] = ob_get_clean();
    imagedestroy($img);
    echo "Generated {$size}x{$size} -> {$path}\n";
}

// Create standard Windows ICO binary containing 16x16 and 32x32 PNG entries
function writeIcoFile(array $pngs, string $destPath) {
    $numImages = count($pngs);
    $header = pack('vvv', 0, 1, $numImages);
    $entries = '';
    $dataOffset = 6 + ($numImages * 16);
    $imageData = '';

    foreach ($pngs as $size => $data) {
        $width = ($size >= 256) ? 0 : $size;
        $height = ($size >= 256) ? 0 : $size;
        $colorCount = 0;
        $reserved = 0;
        $planes = 1;
        $bpp = 32;
        $dataSize = strlen($data);

        $entries .= pack('CCCCvvVV', $width, $height, $colorCount, $reserved, $planes, $bpp, $dataSize, $dataOffset);
        $imageData .= $data;
        $dataOffset += $dataSize;
    }

    file_put_contents($destPath, $header . $entries . $imageData);
    echo "Generated ICO -> {$destPath}\n";
}

// Write to root favicon.ico and assets/images/favicon.ico
$icoPngs = [
    16 => $pngDataArray[16],
    32 => $pngDataArray[32],
];

writeIcoFile($icoPngs, 'c:/Users/Khalil/OneDrive/saas/favicon.ico');
writeIcoFile($icoPngs, 'c:/Users/Khalil/OneDrive/saas/assets/images/favicon.ico');

echo "All icons successfully created!\n";
