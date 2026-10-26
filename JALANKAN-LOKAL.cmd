@echo off
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0jalankan-lokal.ps1" %*
if errorlevel 1 pause
