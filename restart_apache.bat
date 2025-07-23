@echo off
echo Apache'yi yeniden baslatiliyor...
net stop Apache2.4
net start Apache2.4
echo Apache yeniden baslatildi!
pause 