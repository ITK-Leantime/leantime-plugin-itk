# ITK plugin

## Initial setup

In the `bin`folder, create release-exclude.txt with the folders/files excluded from future releases.

```shell
echo "" > release-exclude.txt
```

Implement the build function and fill out the varibles (`plugin_name`,`plugin_repo_url`) in bin/local.create-release.Implement the build function and fill out the varibles (`plugin_name`,`plugin_repo_url`) in bin/local.create-release.Implement the build function and fill out the varibles (`plugin_name`,`plugin_repo_url`) in bin/local.create-release.Implement the build function and fill out the varibles (`plugin_name`,`plugin_repo_url`) in bin/local.create-release.Implement the build function and fill out the varibles (`plugin_name`,`plugin_repo_url`) in bin/local.create-release.Implement the build function and fill out the varibles (`plugin_name`,`plugin_repo_url`) in bin/local.create-release.Implement the build function and fill out the varibles (`plugin_name`,`plugin_repo_url`) in bin/local.create-release.Implement the build function and fill out the varibles (`plugin_name`,`plugin_repo_url`) in bin/local.create-release.Implement the build function and fill out the varibles (`plugin_name`,`plugin_repo_url`) in bin/local.create-release.Implement the build function and fill out the varibles (`plugin_name`,`plugin_repo_url`) in bin/local.create-release.Implement the build function and fill out the varibles (`plugin_name`,`plugin_repo_url`) in bin/local.create-release.

## Installation

Download a release from
<https://github.com/ITK-Leantime/leantime-plugin-itk/releases> and extract into
your Leantime plugins folder.

Install and enable the plugin:

``` shell
bin/leantime plugin:install leantime/itk --no-interaction
bin/leantime plugin:enable leantime/itk --no-interaction
```

### Install _before_ running coding standards

### Composer normalize

```shell name=composer-normalize
docker run --interactive --rm --volume ${PWD}:/app itkdev/php8.3-fpm:latest composer install
docker run --rm --volume ${PWD}:/app itkdev/php8.3-fpm:latest composer normalize
```

### Coding standards

#### Check and apply with phpcs

```shell name=check-coding-standards
docker run --interactive --rm --volume ${PWD}:/app itkdev/php8.3-fpm:latest composer coding-standards-check
```

```shell name=apply-coding-standards
docker run --interactive --rm --volume ${PWD}:/app itkdev/php8.3-fpm:latest composer coding-standards-apply
```

#### Check and apply with prettier

```shell name=prettier-check
docker run --rm -v "$(pwd):/work" tmknom/prettier:latest --check assets
```

```shell name=prettier-apply
docker run --rm -v "$(pwd):/work" tmknom/prettier:latest --write assets
```

#### Check and apply markdownlint

```shell name=markdown-check
docker run --rm --volume $PWD:/md peterdavehello/markdownlint markdownlint --ignore vendor --ignore LICENSE.md '**/*.md'
```

```shell name=markdown-apply
docker run --rm --volume $PWD:/md peterdavehello/markdownlint markdownlint --ignore vendor --ignore LICENSE.md '**/*.md' --fix
```

#### Check with shellcheck

```shell name=shell-check
docker run --rm --volume "$PWD:/app" --workdir /app peterdavehello/shellcheck shellcheck bin/create-release
docker run --rm --volume "$PWD:/app" --workdir /app peterdavehello/shellcheck shellcheck bin/deploy
```

### Code analysis

```shell name=code-analysis
docker run --interactive --rm --volume ${PWD}:/app itkdev/php8.3-fpm:latest composer install
docker run --interactive --rm --volume ${PWD}:/app itkdev/php8.3-fpm:latest composer code-analysis
```

## Test release build

```shell name=test-create-release
docker compose build && docker compose run --rm php bin/create-release dev-test
```

The create-release script replaces `@@VERSION@@` in
[register.php](https://github.com/ITK-Leantime/leantime-dataexport/blob/f7c3992f78270c03b6fc84dbc9b1bbd6e48e53d6/register.php#L9)
and
[Services/DataExport.php](https://github.com/ITK-Leantime/leantime-dataexport/blob/f7c3992f78270c03b6fc84dbc9b1bbd6e48e53d6/Services/DataExport.php#L15)
with the tag provided (in the above it is `dev-test`).

## Deploy

The deploy script downloads a [release](https://github.com/ITK-Leantime/leantime-dataexport/releases) from Github and
unzips it. The script should be passed a tag as argument. In the process the script deletes itself, but the script
finishes because it [is still in memory](https://linux.die.net/man/3/unlink).
