# Database anonymizer

[![Build Status](https://travis-ci.com/webnet-fr/database-anonymizer.svg?branch=master)](https://travis-ci.com/webnet-fr/database-anonymizer)
[![codecov](https://codecov.io/gh/webnet-fr/database-anonymizer/branch/master/graph/badge.svg)](https://codecov.io/gh/webnet-fr/database-anonymizer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webnet-fr/database-anonymizer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webnet-fr/database-anonymizer)


### Why ?

[General Data Protection Regulation] (GDPR) imposes strict rules in domain of
information storage and treatment. You must not treat the user's personal data 
unless there is a strong necessity. In case you want to dump a production database
in order to use it during development you cannot store or use peronal data in 
dumped database anymore. You must delete or anonymize personal information before
importing production database in your developpment setting.


### How ?

Launch one command provided by our **database anonymizer** before dumping a 
production database and it will replace personal information with random but 
meaningful data. The good point is that you specify fields to anonymize and
how they will be anonimized:

```
webnet_fr_database_anonymizer:  # required part of configuration
  tables:
    users:                      # table name
      primary_key: [id]         # indicate primary key
      fields:
        email:                  # field's name to anonymize
          generator: email      # chose the one of dozens generators
          unique: ~             # any option to pass to generator
        first_name:             # another field to anonymize
          generator: first_name # generator
```

`primary_key` entry is optional and can be inferred automatically. You can 
indicate a composite primary key or any column with unique non-null value.


### What to do ?


### What generators are available ?


### No PHP in your environment ?

Then take advantage of Docker.

0. Install [Docker].

1. Place a [docker/Dockerfile] in empty folder. Delete unnecessary extension 
installation (MySQL, PostgreSQL, SQL Seriver) to seed up docker build.

2. Create anonymizer configuration in, say, `config.yaml`.

3. Build an image.

```
docker build -t webnetfr/anonymizer .
```

4. Run anonymisation.

```
docker run --volume <absolute_path_to_local_config>:<absolute_path_to_config_in_container> \
    webnetfr/anonymizer \
    php vendor/bin/database-anonymizer --no-interaction --url <database url> <path_to_config_in_container>
```

Where:

- `<absolute_path_to_local_config>`
- `<absolute_path_to_config_in_container>` is a path for your configuraion
  in container accessible by anonymizer. I suggest you to always use `/var/www/anonymizer/config.yaml`
- `<database url>` is URL to your database (e.g. `mysql://user:password@host:port/name`).
  Check out the command options if you prefer to pass `host`, `port`, `user`, `password` 
  values in separate options.
- `<path_to_config_in_container>` is the same as `<absolute_path_to_config_in_container>`
but you can indicate the path relative to `/var/www/anonymizer`. That said you 
can simply put `config.yaml` if you used `/var/www/anonymizer/config.yaml` in
`<absolute_path_to_config_in_container>`.
 
Imagine you downloaded [docker/Dockerfile] in empty folder and created `conf.yml` 
near to it. Your command may be:

```
docker run --volume $(pwd)/conf.yaml:/var/www/anonymizer/config.yaml \
    webnetfr/anonymizer \
    php vendor/bin/database-anonymizer -n -Umysql://root:pass@localhost/db config.yaml
```

*Tip*: check out the variety of different options Docker provide you with. 
For example you may add `--net=host` option to share your machine's network 
with container.

*Tip*: you can run and connect to container with these command :
```
docker run --volume $(pwd)/conf.yaml:/var/www/anonymizer/config.yaml -it \
    webnetfr/anonymizer bash
```

[Docker]: https://www.docker.com
[General Data Protection Regulation]: https://en.wikipedia.org/wiki/General_Data_Protection_Regulation
[docker/Dockerfile]: docker/Dockerfile
