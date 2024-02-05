@echo off

echo Wartość dwóch parametrów przekazanych do skryptu
echo %1
echo %2

rem To jest komentarz i nie ma wpływu na działanie programu
rem
set /p imie=Jak masz na imię?

echo
:: Wyświetl odpowiedź użytkownika
echo Witaj, %imie%.
pause
