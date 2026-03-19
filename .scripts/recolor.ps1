Add-Type -AssemblyName System.Drawing

$sourcePath = "C:\xampp\htdocs\paws_place_final\grabhound_logo_delivery_1772467455618.png"
$destPath = "C:\xampp\htdocs\paws_place_final\grabhound_logo_v4_maroon.png"

$bmp = [System.Drawing.Bitmap]::FromFile($sourcePath)

$newR = 128
$newG = 0
$newB = 0

for ($x = 0; $x -lt $bmp.Width; $x++) {
    for ($y = 0; $y -lt $bmp.Height; $y++) {
        $pixel = $bmp.GetPixel($x, $y)
        
        # Check if the pixel is not fully transparent
        if ($pixel.A -gt 0) {
            # Check if it's a reddish color (R > G + 20 and R > B + 20)
            if ($pixel.R -gt ($pixel.G + 5) -and $pixel.R -gt ($pixel.B + 5)) {
                
                # Calculate the intensity mapping (how close to the original "pure" color it is)
                # To keep anti-aliasing, we mix the new color based on the original pixel's intensity or luminosity.
                # Assuming the original was a relatively solid color with antialiasing to white/transparent.
                # For simplicity, if it's prominently red, we just enforce the new hue.
                # We can calculate brightness/lightness of the pixel.
                $maxRGB = [Math]::Max($pixel.R, [Math]::Max($pixel.G, $pixel.B))
                $minRGB = [Math]::Min($pixel.R, [Math]::Min($pixel.G, $pixel.B))
                $luminance = ($maxRGB + $minRGB) / 2
                
                # Instead of a complex HSL conversion, let's just make the dominant reddish pixels exactly 128,0,0
                # But to preserve anti-aliasing, we blend with 128,0,0 based on how red it was.
                # Actually, simpler: if it's strongly red, make it 128,0,0. 
                # If it's a lighter red (anti-aliased edge), we can interpolate between 128,0,0 and 255,255,255 based on its original lightness.
                
                # Let's use a simple approach: if it's near the main red, swap to #800000.
                # If R is the max component, we scale the new RGB to match the pixel's luminosity or just swap the base color.
                
                # Let's just flatly replace anything reddish with #800000, keeping alpha.
                # It might make edges harsh. Let's try to preserve some graduation.
                $factorR = $pixel.R / 255.0
                # Just replacing it directly for test.
                $newColor = [System.Drawing.Color]::FromArgb($pixel.A, $newR, $newG, $newB)
                $bmp.SetPixel($x, $y, $newColor)
            }
        }
    }
}

$bmp.Save($destPath, [System.Drawing.Imaging.ImageFormat]::Png)
$bmp.Dispose()
Write-Output "Image processed to $destPath"
