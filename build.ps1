Set-Location .\gokada-client
npm run build
if(-Not $?){
    Set-Location ..
    exit;
}
Set-Location ..
Remove-Item .\docs -Recurse -ErrorAction Ignore
New-Item -ItemType Directory -Force -Path .\docs

Copy-Item .\gokada-client\dist\* .\docs -Recurse