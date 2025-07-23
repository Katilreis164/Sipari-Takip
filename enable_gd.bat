@echo off
echo GD eklentisini etkinlestirme...
powershell -Command "(gc C:\xampp\php\php.ini) -replace ';extension=gd', 'extension=gd' | Set-Content C:\xampp\php\php.ini"
echo Apache'yi yeniden baslatmayi unutmayin!
pause 