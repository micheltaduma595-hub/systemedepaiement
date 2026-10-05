@echo off
chcp 65001 >nul
echo ============================================
echo  VERIFICATION DES ECHEANCES DE PAIEMENT
echo ============================================
echo.
echo Date: %date% a %time%
echo.
echo Verification des echeances en cours...
echo.

C:\xampp\php\php.exe C:\xampp\htdocs\systemedepaiement\cron\verifier_echeances.php

echo.
echo ============================================
echo  FIN DE LA VERIFICATION
echo ============================================
pause