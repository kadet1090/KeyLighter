$script:bg    = [Console]::BackgroundColor;
$script:first = $true;
$script:last  = 0;

function Write-PromptFancyEnd {
    Write-Host  -NoNewline -ForegroundColor $script:bg

    $script:bg = [System.ConsoleColor]::Black
}

function Write-PromptSegment {
    param(
        [Parameter(
            Position=0,
            Mandatory=$true,
            ValueFromPipeline=$true,
            ValueFromPipelineByPropertyName=$true
        )][string]$Text,

        [Parameter(Position=1)][System.ConsoleColor] $Background = [Console]::BackgroundColor,
        [Parameter(Position=2)][System.ConsoleColor] $Foreground = [System.ConsoleColor]::White
    )

    if(!$script:first) {
        Write-Host  -NoNewline -BackgroundColor $Background -ForegroundColor $script:bg
    } else {
        $script:first = $false
    }

    Write-Host $text -NoNewline -BackgroundColor $Background -ForegroundColor $Foreground

    $script:bg = $background;
}

function Get-FancyDir {
    return $(Get-Location).ToString().Replace($env:USERPROFILE, '~').Replace('\', '  ');
}

function Get-GitBranch {
    $HEAD = Get-Content $(Join-Path $(Get-GitDirectory) HEAD)
    if($HEAD -like 'ref: refs/heads/*') {
        return $HEAD -replace 'ref: refs/heads/(.*?)', "$1";
    } else {
        return $HEAD.Substring(0, 8);
    }
}

function Write-PromptStatus {
    if($script:last) {
        Write-PromptSegment ' ✔ ' Green Black
    } else {
        Write-PromptSegment " ✖ $lastexitcode " Red White
    }
}

function Write-PromptUser {
    if($global:admin) {
        Write-PromptSegment ' # ADMIN ' Magenta White;
    } else {
        Write-PromptSegment " $env:USERNAME " Yellow Black;
    }
}

function Write-PromptVirtualEnv {
    if($env:VIRTUAL_ENV) {
        Write-PromptSegment " $(split-path $env:VIRTUAL_ENV -leaf) " Cyan Black
    }
}

function Write-PromptDir {
    Write-PromptSegment " $(Get-FancyDir) " DarkGray White
}

# Depends on posh-git
function Write-PromptGit {
    if(Get-GitDirectory) {
        Write-PromptSegment "  $(Get-GitBranch) " Blue White
    }
}

function prompt {
    $script:last  = $?;
    $script:first = $true;

    Write-PromptStatus
    Write-PromptUser
    Write-PromptVirtualEnv
    Write-PromptDir
    Write-PromptGit

    Write-PromptFancyEnd

    return ' '
}