#Requires -Version 5.1
<#
.SYNOPSIS
  Build WordPress.org review zips for iSystem plugins.
#>
[CmdletBinding()]
param()
$ErrorActionPreference = 'Stop'
$Root = Split-Path -Parent $MyInvocation.MyCommand.Path
$Plugins = Join-Path $Root 'plugins'
$Out = Join-Path $Root 'dist-wporg'
New-Item -ItemType Directory -Force -Path $Out | Out-Null

function Pack-Plugin {
  param(
    [string]$SourceFolder,
    [string]$ZipSlug,
    [string]$MainPhp
  )
  $src = Join-Path $Plugins $SourceFolder
  if (-not (Test-Path $src)) { throw "Missing $src" }
  $stage = Join-Path $env:TEMP "wporg-$ZipSlug"
  if (Test-Path $stage) { Remove-Item -Recurse -Force $stage }
  $dest = Join-Path $stage $ZipSlug
  New-Item -ItemType Directory -Force -Path $dest | Out-Null
  Copy-Item -Recurse -Force (Join-Path $src '*') $dest
  # Ensure readme.txt
  if (-not (Test-Path (Join-Path $dest 'readme.txt'))) {
    throw "Missing readme.txt in $SourceFolder"
  }
  # Drop non-.org clutter
  @('STUBS.md', 'legacy', '.git') | ForEach-Object {
    $p = Join-Path $dest $_
    if (Test-Path $p) { Remove-Item -Recurse -Force $p }
  }
  $zip = Join-Path $Out "$ZipSlug.zip"
  if (Test-Path $zip) { Remove-Item -Force $zip }
  Compress-Archive -Path $dest -DestinationPath $zip -Force
  Write-Host "OK $zip"
}

Pack-Plugin -SourceFolder 'db-cleaner-pro' -ZipSlug 'db-cleaner-pro' -MainPhp 'db-cleaner-pro.php'
Pack-Plugin -SourceFolder 'isystem-gcc-plus' -ZipSlug 'isystem-gcc-plus' -MainPhp 'isystem-gcc-plus.php'
Pack-Plugin -SourceFolder 'OptiByte_WP' -ZipSlug 'optibyte-wp' -MainPhp 'optibyte-wp.php'

Write-Host "Zips in $Out"
Get-ChildItem $Out | Format-Table Name, Length, LastWriteTime
