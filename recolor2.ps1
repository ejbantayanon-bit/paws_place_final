Add-Type -AssemblyName System.Drawing

$sourcePath = "C:\xampp\htdocs\paws_place_final\grabhound_logo_delivery_1772467455618.png"
$destPath = "C:\xampp\htdocs\paws_place_final\paw_place\client\img\paws_logo_v5_maroon.png"

if (!(Test-Path $sourcePath)) {
    Write-Output "Source file not found."
    exit
}

$bmp = [System.Drawing.Bitmap]::FromFile($sourcePath)

$w = $bmp.Width
$h = $bmp.Height

for ($x = 0; $x -lt $w; $x++) {
    for ($y = 0; $y -lt $h; $y++) {
        $p = $bmp.GetPixel($x, $y)
        
        # Check if the pixel has alpha and is reddish
        if ($p.A -gt 0 -and $p.R -gt ($p.G + 5) -and $p.R -gt ($p.B + 5)) {
            
            # Whiteness (how close to white it is)
            $minVal = [Math]::Min($p.R, [Math]::Min($p.G, $p.B))
            $whiteness = $minVal / 255.0
            
            # Base color for #800000
            $baseR = 128.0
            $baseG = 0.0
            $baseB = 0.0
            
            # Add whiteness to the base color
            $newR = $baseR + (255.0 - $baseR) * $whiteness
            $newG = $baseG + (255.0 - $baseG) * $whiteness
            $newB = $baseB + (255.0 - $baseB) * $whiteness
            
            # Clamp to 0-255 using Min and Max
            $newR = [Math]::Max(0.0, [Math]::Min(255.0, $newR))
            $newG = [Math]::Max(0.0, [Math]::Min(255.0, $newG))
            $newB = [Math]::Max(0.0, [Math]::Min(255.0, $newB))
            
            $newColor = [System.Drawing.Color]::FromArgb($p.A, [int]$newR, [int]$newG, [int]$newB)
            $bmp.SetPixel($x, $y, $newColor)
        }
    }
}

$bmp.Save($destPath, [System.Drawing.Imaging.ImageFormat]::Png)
$bmp.Dispose()
Write-Output "Image processed to $destPath"
