# Database anonymizer

[![Build Status](https://travis-ci.com/webnet-fr/database-anonymizer.svg?branch=master)](https://travis-ci.com/webnet-fr/database-anonymizer)
[![codecov](https://codecov.io/gh/webnet-fr/database-anonymizer/branch/master/graph/badge.svg)](https://codecov.io/gh/webnet-fr/database-anonymizer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webnet-fr/database-anonymizer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webnet-fr/database-anonymizer)


### Why ?

[General Data Protection Regulation] (GDPR) imposes strict rules in the domain of
information storage and treatment. You must not treat the users' personal data 
unless there is a strong necessity. In case you want to dump a production database
in order to use it during development you cannot store or use peronal data in 
a dumped database anymore. You must delete or anonymize personal information before
importing a production database in your developpment setting.


### How ?

Launch a command provided by our **database anonymizer** and it will replace 
personal information with random but meaningful data:

```bash
php bin/database-anonymizer webnet-fr:anonymizer:anonymize <config.yaml> -U<database url>
```

- Path to <config.yaml> is required. Check out the next section to find out how
to write a configuration.
- Numerous options to define a database connection are available:
    - `--url=<url>` or `-U<url>` to define a database connection string. It is 
    a very convenient option because it alone is capable to define your 
    database connection.
    - `--type=<type>` or `-t<type>` to define a driver to use (`mysql`, `mysqli`, 
    `pdo_pgsql`, `sqlsrv`).
    - `--host=<type>` or `-H<type>` to define a database host.
    - `--port=<port>` or `-P<port>` to define a port of the database server.
    - `--database=<name>` or `-d<name>` to define a port of the database server.
    - `--user=<username>` or `-u<username>` to define a username to access 
    the database server.
    - `--password=<pass>` or `-p<pass>` to define a password to access the 
    database server.

### How to configure the fields to anonymize ?

The good point is that you can specify the fields to anonymize and how they will 
be anonymized:

```
webnet_fr_database_anonymizer:  # required part of configuration
  tables:
    users:                      # table name
      primary_key: [id]         # indicate primary key
      fields:
        email:                  # field's name to anonymize
          generator: faker      # chose a generator
          formatter: email      # chose one of dozens of the faker's formatters
          unique: ~             # assure that the random value will be unique
        name:                   # another field to anonymize
          generator: faker      # generator
          formatter: name       # formatter
          arguments: ['female'] # specify the arguemnts to pass to the formatter
```

`primary_key` entry is optional and can be inferred automatically. You can 
indicate a composite primary key or any column with a unique non-null value.


### Let anonymizer guess the configuration

While the configuration of all your database tables can be tedious we provide
you with a guesser. The guesser command enable you to construct automatically 
the configuration:

```bash
php bin/database-anonymizer webnet-fr:anonymizer:guess-config -f<file.yaml> -U<database url>
```

The guesser verifies all columns in all tables in your database searching for
columns possibly containing sensitive personal data like first name, birth date,
social security number, etc.

You can pass the following arguments and options to the guess command:

- `--file=<file.yaml>` or `-F=<file.yaml>` to write configuration to a file.
Otherwise the configuration will pop out to your console.
- `-U<url>`, `-t<type>`, `-H<type>`, `-P<port>`, `-d<name>`, `-u<username>`, 
`-p<pass>` options are at your disposal to specify a database connection.


### How to install ?

Two options are provided:

1. If you develop a PHP project you are welcome to add a dependency 
(maybe with `--dev` option):
  
```bash
composer require webnet-fr/database-anonymizer
```

2. [Use Docker](#launch-anonymizer-in-a-docker-container) if you don't use PHP or for any other reason.


### What generators are available ?

Out of the box two types of generators are available :

1. [Constant] generator :

```yaml
webnet_fr_database_anonymizer:
    tables:
        <table name>:
            fields:
                password:
                    generator: constant # specify "constant" generator
                    value: pass123      # all rows will be set to "pass123"
```

2. [Faker]'s generators. This tool makes use of `fzaninotto/faker` library.
Anonymizer lets you use all formatters provided by Faker. We invite you check
them out. Here is couple of examples :

```yaml
webnet_fr_database_anonymizer:
    tables:
        <table name>:
            fields:

                # Set "birthdate" field to a random date in a range from -100 to -18 years.
                birthdate:
                    generator: faker
                    formatter: dateTimeBetween          
                    arguments: ['-100 years', '-18 year']
                    date_format: Y-m-d
                    optional: 0.4

                # Set "numero_ss" field to a random number of the french sécurité sociale.
                # Pay attention that "nir" formatter is available only with french locale. 
                numero_ss:
                    generator: faker
                    formatter: nir
                    locale: fr_FR

                # Set "tax_code" field to a random tax code for russian company.
                # Pay attention that "kpp" formatter is available only with russian locale. 
                tax_code:
                    generator: faker
                    formatter: kpp
                    locale: ru_RU
                    unique: ~
```

For each faker generator you can specify these options :
- `formatter` - any available formatter in any available provider. E.g. `randomDigit`,
`name`, `email`, `cpr` (for `da_DK` locale only).
- `locale` - any available locale in Faker. Pay attention that certain formatters
exist exclusively for certain locales. E.g. `cs_CZ`, `da_DK`, `ru_RU`.
- `unique` - assures that each generated value is unique in the scope of current
field. This is useful for generating usenames. Beware of overflow exceptions.
- `optional` - with a certain chance a generated value will be null. When you
set `optional: 0.4` you have 40% chance of random meaningful value and 60% chance 
of null.
- `date_format` - if a generated value is `DateTime` object you must specify a
format. This is true for these formatters: `dateTimeBetween`, `dateTimeInInterval`, 
`dateTimeThisYear`, etc. E.g `Y-m-d`, `Y-m-d H:i:s` or any valid format for 
[php date() function].


### Launch anonymizer in a docker container

Then take advantage of Docker.

0. Install [Docker].

1. Place the [docker/Dockerfile] in an empty folder. Delete unnecessary extension 
installation (MySQL, PostgreSQL, SQL Seriver) to speed up the docker build.

2. Create the anonymizer configuration in, say, `config.yaml`.

3. Build an image.

```
docker build -t webnetfr/anonymizer .
```

4. Run anonymization.

```
docker run --volume <absolute_path_to_local_config>:<absolute_path_to_config_in_container> \
    webnetfr/anonymizer \
    php vendor/bin/database-anonymizer --no-interaction --url <database url> <path_to_config_in_container>
```

Where:

- `<absolute_path_to_local_config>`
- `<absolute_path_to_config_in_container>` is a path for your configuraion
  in the container accessible by anonymizer. I suggest you to always use `/var/www/anonymizer/config.yaml`
- `<database url>` is the URL to your database (e.g. `mysql://user:password@host:port/name`).
  Check out the command options if you prefer to pass the `host`, `port`, `user`, `password` 
  values in separate options.
- `<path_to_config_in_container>` is the same as `<absolute_path_to_config_in_container>`
but you can indicate the path relative to `/var/www/anonymizer`. That said you 
can simply put `config.yaml` if you used `/var/www/anonymizer/config.yaml` in
`<absolute_path_to_config_in_container>`.
 
Imagine you downloaded the [docker/Dockerfile] into an empty folder and created 
`conf.yml` next to it. Your command may be:

```
docker run --volume $(pwd)/conf.yaml:/var/www/anonymizer/config.yaml \
    webnetfr/anonymizer \
    php vendor/bin/database-anonymizer -n -Umysql://root:pass@localhost/db config.yaml
```

*Tip*: check out the variety of different options Docker provides you with.
For example you may add the `--net=host` option to share your machine's network 
with the container.

*Tip*: you can run and connect to the container with this command :
```
docker run --volume $(pwd)/conf.yaml:/var/www/anonymizer/config.yaml -it \
    webnetfr/anonymizer bash
```

[General Data Protection Regulation]: https://en.wikipedia.org/wiki/General_Data_Protection_Regulation
[Constant]: src/Generator/Constant.php
[Faker]: https://github.com/fzaninotto/Faker
[php date() function]: https://www.php.net/manual/fr/function.date.php
[Docker]: https://www.docker.com
[docker/Dockerfile]: docker/Dockerfile
