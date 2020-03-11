# Chameleon System GraphQL Bundle

This bundle adds a graphql API to chameleon system

Installation:

Add this repository to your composer.json:

```json
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/bestform/ChameleonSystemGraphQLBundle.git"
        }
    ]
```

Then require the dev version like this:

`composer require chameleon-system/graphql-bundle@dev-master`

Add this to your `routing.yml` to host the api at `/api`:

```yaml
chameleon_system_graphql:
    resource: "@ChameleonSystemGraphQLBundle/Resources/config/routing.yml"
    type: yaml
```

Add this to your `routing_dev.yml` to add graphiQL to your dev environment:
```yaml
chameleon_system_graphql_dev:
    resource: "@ChameleonSystemGraphQLBundle/Resources/config/routing_dev.yml"
    type: yaml
```

Add the two needed Bundles to the production list of your `AppKernel`:

```php
<?php
            new ChameleonSystem\GraphQLBundle\ChameleonSystemGraphQLBundle(),
            new Overblog\GraphQLBundle\OverblogGraphQLBundle(),
```

Add the graphiQL Bundles to the dev list of your `AppKernel`:
```php
<?php

            $bundles[] = new Overblog\GraphiQLBundle\OverblogGraphiQLBundle();

```
