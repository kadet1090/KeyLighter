# Contributing
Contributions are **welcome** and will be fully **credited**.
We accept contributions via Pull Requests on [Github](https://github.com/kadet1090/keylighter).

## Documentation
Detailed documentation related to KeyLighter development can be found in
[`/Docs/`](https://github.com/kadet1090/keylighter/Docs/) directory.  

## Issues
Don't want to code but have suggestion or found issue? That's cool too -
Just remember to properly describe it, and if it's highlighting problem
report it via KeyLighter page: http://keylighter.kadet.net/. 
Submit code which is highlighted incorrectly, click **create issue**
button and fill issue with more information.

![buttton](https://dl.dropboxusercontent.com/u/60020102/ShareX/2016-06/2016-06-21_00-28-51-1c9.png)

In other case create issue via GitHub with template:
```md
Describe what is wrong, include piece of code or screenshot if needed.

# Steps to reproduce
...

# What was expected result?
...

Version: Version number or commit hash if using dev-master - preferably `git describe --all --long` output.
```

## Pull Requests
- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - The easiest way to apply the conventions is to install [PHP Code Sniffer](http://pear.php.net/package/PHP_CodeSniffer).
- **Add tests!** - Your patch won't be accepted if it doesn't have tests.
- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.
- **Consider our release cycle** - We try to follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.
- **Create feature branches** - Don't ask us to pull from your master branch.
- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.
- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.
- **Fill an issue** - For bigger changes you should fill an issue, especially when code architecture is going to be altered. 

## Running Tests
``` bash
$ phpunit test
```

**Happy coding**!
