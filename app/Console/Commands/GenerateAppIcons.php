<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateAppIcons extends Command
{
    protected $signature = 'app:generate-icons';
    protected $description = 'Générer les icônes PWA de l\'application';

    public function handle(): int
    {
        $sizes = [72, 96, 128, 144, 152, 192, 384, 512];
        $outputDir = public_path('icons');

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        foreach ($sizes as $size) {
            $img = imagecreatetruecolor($size, $size);

            // Background color #3C50E0
            $bg = imagecolorallocate($img, 60, 80, 224);
            imagefilledrectangle($img, 0, 0, $size, $size, $bg);

            // Rounded corners effect with anti-aliased circle
            $white = imagecolorallocate($img, 255, 255, 255);

            // Text "DT"
            $fontSize = (int) ($size * 0.35);
            $fontPath = resource_path('fonts/arial-bold.ttf');

            // Use built-in font if TTF not available
            if (file_exists($fontPath)) {
                $bbox = imagettfbbox($fontSize, 0, $fontPath, 'DT');
                $textWidth = $bbox[2] - $bbox[0];
                $textHeight = $bbox[1] - $bbox[7];
                $x = (int) (($size - $textWidth) / 2) - $bbox[0];
                $y = (int) (($size - $textHeight) / 2) - $bbox[7];
                imagettftext($img, $fontSize, 0, $x, $y, $white, $fontPath, 'DT');
            } else {
                // Fallback: use built-in GD font scaled
                $font = 5; // largest built-in font
                $charWidth = imagefontwidth($font);
                $charHeight = imagefontheight($font);
                $textWidth = $charWidth * 2; // "DT" = 2 chars
                $x = (int) (($size - $textWidth) / 2);
                $y = (int) (($size - $charHeight) / 2);
                imagestring($img, $font, $x, $y, 'DT', $white);
            }

            $filename = $outputDir . '/icon-' . $size . 'x' . $size . '.png';
            imagepng($img, $filename);
            imagedestroy($img);

            $this->info("Icône générée : icon-{$size}x{$size}.png");
        }

        $this->info('Toutes les icônes ont été générées dans public/icons/');
        return Command::SUCCESS;
    }
}
