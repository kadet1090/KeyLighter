$script:keylighter = Join-Path $PSScriptRoot "keylighter"
$script:esc = [char]27

function Show-Source {
    param(
        [Parameter(
            Position=0,
            ValueFromPipeline=$true, 
            parametersetname="nopipeline"
        )][string]$File,

        [Parameter(
            Position=1,
            ValueFromPipelineByPropertyName=$true
        )]
        [Alias('l','Lang')]
        [string]$Language,

        [Parameter(
            Position=2,
            ValueFromPipelineByPropertyName=$true
        )]
        [Alias('f','Format')]
        [string]$Formatter,

        [Alias('s')][switch]$Silent,

        [Alias('v')][int]$VerboseLevel = 0
    )

    Begin {
        $pipeline = -not ($PSCmdlet.ParameterSetName -eq "nopipeline");
    }

    Process {
        $source += $_ + "`n";
    }

    End {
        $params = @();

        if($Language) {
            $params += @("-l", $language)
        }

        if($Formatter) {
            $params += @("-f", $formatter)
        }

        if($Silent) {
            $params += @("-s")
        }

        if($VerboseLevel -gt 0) {
            $params += @("-v", $VerboseLevel)
        }

        if(!$pipeline) {
            php $script:keylighter $File @params;
        } else   {
            if($file) {
                $source | php $script:keylighter php://stdin @params;
            } else {
                php $script:keylighter
            }
        }
    }
}

function Get-KeyLighterLanguages {
    $languages = @();
    php $script:keylighter --languages | % { 
        if($_ -match "(\w+(?:, \w+)*)\s*=>") { 
            $languages += $Matches[1].Trim().Split(', ', [System.StringSplitOptions]::RemoveEmptyEntries);
        } 
    }

    $languages;
}

function Get-KeyLighterFormatters {
    $formatters = @();
    php $script:keylighter --formatters | % { 
        if($_ -match "$script:esc\[33m(\w+)$script:esc\[0m") { 
            $formatters += $Matches[1];
        } 
    }

    $formatters;
}

if (Get-Command Register-ArgumentCompleter -ea Ignore) {
    Register-ArgumentCompleter -Command Show-Source -Parameter Language -ScriptBlock {
        param($commandName, $parameterName, $wordToComplete, $commandAst, $fakeBoundParameter);

        Get-KeyLighterLanguages | Where-Object { $_ -like "$wordToComplete*" } | % {
            New-CompletionResult -CompletionText $_
        }
    }

    Register-ArgumentCompleter -Command Show-Source -Parameter Formatter -ScriptBlock {
        param($commandName, $parameterName, $wordToComplete, $commandAst, $fakeBoundParameter);

        Get-KeyLighterFormatters | Where-Object { $_ -like "$wordToComplete*" } | % {
            New-CompletionResult -CompletionText $_
        }
    }
}

# Aliases
Set-Alias KeyLighter Show-Source
Set-Alias kl         Show-Source