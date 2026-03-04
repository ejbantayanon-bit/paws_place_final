<?php
$src = "C:/xampp/htdocs/paws_place_final/grabhound_logo_delivery_1772467455618.png";
$dest = "C:/xampp/htdocs/paws_place_final/grabhound_logo_v5_maroon.png";
if(!file_exists($src)) {
    die("Source file not found: $src\n");
}
$im = imagecreatefrompng($src);
imagealphablending($im, false);
imagesavealpha($im, true);
$w = imagesx($im);
$h = imagesy($im);

for($x=0; $x<$w; $x++) {
    for($y=0; $y<$h; $y++) {
        $rgb = imagecolorat($im, $x, $y);
        $colors = imagecolorsforindex($im, $rgb);
        $r = $colors['red'];
        $g = $colors['green'];
        $b = $colors['blue'];
        $a = $colors['alpha']; // 0 is opaque, 127 is transparent
        
        // If it's remotely reddish
        if ($r > $g + 5 && $r > $b + 5) {
            
            // Calculate how "white" or "light" the pixel is to preserve anti-aliasing edges
            // Pure red from AI gen is likely around #a32222 or similar.
            // We want to shift the base hue to #800000 (128, 0, 0)
            
            // Extract the "color" part vs the "white" part
            $minVal = min($r, $g, $b); // The white component
            $whiteness = $minVal / 255.0;
            
            // The pure color of the pixel would be mixed with white
            // We want the new base color to be 128, 0, 0
            $baseR = 128;
            $baseG = 0;
            $baseB = 0;
            
            // Reapply the "whiteness" (anti-aliasing transition to white/transparent)
            // Some pixels might be dark due to black antialiasing, let's also preserve darkness
            $maxVal = max($r, $g, $b); // The brightness
            $brightness = $maxVal / 255.0;
            
            // New color: start with base
            $newR = $baseR;
            $newG = $baseG;
            $newB = $baseB;
            
            // Add white to match original whiteness
            $newR = $newR + (255 - $newR) * $whiteness;
            $newG = $newG + (255 - $newG) * $whiteness;
            $newB = $newB + (255 - $newB) * $whiteness;
            
            // Optionally scale by brightness if there are dark edges
            // We know the original redness was bright. If it was darker than our base red, we'd darken it.
            // But maroon is already dark. Let's just use the whiteness mapped version.
            
            $newColor = imagecolorallocatealpha($im, (int)$newR, (int)$newG, (int)$newB, $a);
            imagesetpixel($im, $x, $y, $newColor);
        }
    }
}
imagepng($im, $dest);
imagedestroy($im);
echo "Done saving to $dest\n";
?>
