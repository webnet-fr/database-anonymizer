# Database anonymizer

[![Build Status](https://travis-ci.com/webnet-fr/database-anonymizer.svg?branch=master)](https://travis-ci.com/webnet-fr/database-anonymizer)
[![codecov](https://codecov.io/gh/webnet-fr/database-anonymizer/branch/master/graph/badge.svg)](https://codecov.io/gh/webnet-fr/database-anonymizer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webnet-fr/database-anonymizer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webnet-fr/database-anonymizer)

### Use Docker

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
 
Imagine you downloaded [Dockerfile] in empty folder and created `conf.yml` near to it.
Your command may be:

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

[docker/Dockerfile]: docker/Dockerfile
