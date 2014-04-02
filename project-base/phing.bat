@ECHO OFF
SET BIN_TARGET=%~dp0/vendor/phing/phing/bin/phing
php "%BIN_TARGET%" %*
