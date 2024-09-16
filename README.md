# ITK plugin

## Initial development setup

In the `bin`folder, fill out release-exclude.txt with the folders/files excluded from future releases.

Implement the build function and fill out the varibles (`plugin_name`,`plugin_repo_url`) in bin/local.create-release.

Run composer install

```shell name=development-install
docker run --interactive --rm --volume ${PWD}:/app itkdev/php8.3-fpm:latest composer install
```

## Installation

Download a release from <https://github.com/ITK-Leantime/leantime-plugin-itk/releases> and extract into your Leantime
plugins folder.

Install and enable the plugin:

``` shell
bin/leantime plugin:install leantime/itk --no-interaction
bin/leantime plugin:enable leantime/itk --no-interaction
```

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
docker run --rm --volume "$PWD:/app" --workdir /app peterdavehello/shellcheck shellcheck bin/local.create-release
```

### Code analysis

```shell name=code-analysis
docker run --interactive --rm --volume ${PWD}:/app itkdev/php8.3-fpm:latest composer code-analysis
```

## Test release build

```shell name=test-create-release
docker compose build && docker compose run --rm php bin/create-release dev-test
```

The create-release script replaces `@@VERSION@@` in that will be placed in `register.php` and when importing assets in a
services file.

## Deploy

The deploy script downloads a [release](https://github.com/ITK-Leantime/leantime-dataexport/releases) from Github and
unzips it. The script should be passed a tag as argument. In the process the script deletes itself, but the script
finishes because it [is still in memory](https://linux.die.net/man/3/unlink).
