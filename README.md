# FileJet Pro Wordpress plugin

We welcome your feedback and we accept pull-requests.


# Installation

```
# build the image with PHP 7 and composer
docker build -t filejet-wordpress-plugin - < Dockerfile

# install vendors
docker run -it --rm -v "$PWD":/wordpress -w /wordpress filejet-wordpress-plugin composer install --no-dev --no-autoloader
```

