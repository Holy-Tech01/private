@echo off
echo Starting Auto-Update to GitHub...
cd /d "%~dp0"

:: Add all changes
"C:\Program Files\Git\cmd\git.exe" add .

:: Commit with timestamp
"C:\Program Files\Git\cmd\git.exe" commit -m "Auto-update: %date% %time%"

:: Push to main branch
"C:\Program Files\Git\cmd\git.exe" push -u origin main

echo.
echo Update Complete!
pause
