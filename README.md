# ITK plugin

## Installation

Download a release from
<https://github.com/ITK-Leantime/leantime-plugin-itk/releases> and extract into
your Leantime plugins folder.

Install and enable the plugin:

``` shell
bin/leantime plugin:install leantime/itk --no-interaction
bin/leantime plugin:enable leantime/itk --no-interaction
```

## Usage

``` shell
bin/leantime itk:user:password-reset --help
```

## Development

Clone this repository into your Leantime plugins folder:

``` shell
git clone https://github.com/itk-leantime/leantime-plugin-itk app/Plugins/Itk
```

Install plugin dependencies:

``` shell
cd app/Plugins/Itk
docker run --tty --interactive --rm --env COMPOSER=composer-plugin.json --volume ${PWD}:/app itkdev/php8.1-fpm:latest composer install --no-dev
```

Install and enable the plugin:

``` shell
bin/leantime plugin:install leantime/itk --no-interaction
bin/leantime plugin:enable leantime/itk --no-interaction
```

### Coding standards

``` shell
docker run --tty --interactive --rm --env COMPOSER=composer-plugin.json --volume ${PWD}:/app itkdev/php8.1-fpm:latest composer install
docker run --tty --interactive --rm --env COMPOSER=composer-plugin.json --volume ${PWD}:/app itkdev/php8.1-fpm:latest composer coding-standards-check
```

```shell
docker run --tty --interactive --rm --volume ${PWD}:/app node:20 yarn --cwd /app install
docker run --tty --interactive --rm --volume ${PWD}:/app node:20 yarn --cwd /app coding-standards-check
```

### Code analysis

``` shell
docker run --tty --interactive --rm --env COMPOSER=composer-plugin.json --volume ${PWD}:/app itkdev/php8.1-fpm:latest composer install
docker run --tty --interactive --rm --env COMPOSER=composer-plugin.json --volume ${PWD}:/app itkdev/php8.1-fpm:latest composer code-analysis
```
