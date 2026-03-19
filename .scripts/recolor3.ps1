Add-Type -AssemblyName System.Drawing

$sourcePath = "C:\Users\EJ Bantayanon\.gemini\antigravity\brain\6b359964-e074-43fe-bf19-b3ac88b4e3c2\media__1772469601827.png"
$destPath = "C:\xampp\htdocs\paws_place_final\paw_place\client\img\paws_logo_v6_maroon.png"

if (!(Test-Path $sourcePath)) {
    Write-Output "Source file not found: $sourcePath"
    # Fallback to the other one just in case
    $sourcePath = "C:\Users\EJ Bantayanon\.gemini\antigravity\brain\6b359964-e074-43fe-bf19-b3ac88b4e3c2\media__1772469603389.png"
    if (!(Test-Path $sourcePath)) {
        exit
    }
}

$bmp = [System.Drawing.Bitmap]::FromFile($sourcePath)

$w = $bmp.Width
$h = $bmp.Height

for ($x = 0; $x -lt $w; $x++) {
    for ($y = 0; $y -lt $h; $y++) {
        $p = $bmp.GetPixel($x, $y)
        
        # Check if the pixel has alpha and is reddish/dark red
        # User's image might have a different shade of red, let's just target anything where Red is the dominant color
        if ($p.R -gt ($p.G + 5) -and $p.R -gt ($p.B + 5)) {
            
            # Whiteness (how close to white it is)
            $minVal = [Math]::Min($p.R, [Math]::Min($p.G, $p.B))
            $whiteness = $minVal / 255.0
            
            # Darkness/blackness (how close to black it is) 
            # To handle shadows or dark gradients
            $maxVal = [Math]::Max($p.R, [Math]::Max($p.G, $p.B))
            $darkness = 1.0 - ($maxVal / 255.0)

            # Base color for #800000
            $baseR = 128.0
            $baseG = 0.0
            $baseB = 0.0
            
            # Add whiteness and subtract darkness
            $newR = $baseR + (255.0 - $baseR) * $whiteness - ($baseR * $darkness)
            $newG = $baseG + (255.0 - $baseG) * $whiteness - ($baseG * $darkness)
            $newB = $baseB + (255.0 - $baseB) * $whiteness - ($baseB * $darkness)
            
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
