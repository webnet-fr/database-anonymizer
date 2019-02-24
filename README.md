# Database anonymizer

[![Build Status](https://travis-ci.com/webnet-fr/database-anonymizer.svg?branch=master)](https://travis-ci.com/webnet-fr/database-anonymizer)
[![codecov](https://codecov.io/gh/webnet-fr/database-anonymizer/branch/master/graph/badge.svg)](https://codecov.io/gh/webnet-fr/database-anonymizer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webnet-fr/database-anonymizer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webnet-fr/database-anonymizer)

### Use Docker

Place [docker/Dockerfile] in empty folder and create anonymizer configuration 
in, say, `config.yaml`.

Build an image :
```
docker build -t webnetfr/anonymizer .
```

Run :
```
docker run --volume $(pwd)/config.yaml:/var/www/anonymizer/config.yaml \
    webnetfr/anonymizer \
    php vendor/bin/database-anonymizer --url=<database url> config.yaml
```

[docker/Dockerfile]: docker/Dockerfile
