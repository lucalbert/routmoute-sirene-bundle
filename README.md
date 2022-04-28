# RoutmouteSireneBundle

Bundle to use INSEE Sirene API with Symfony 6

## Manual Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 1: Create configuration file

Create configuration file `config/packages/routmoute_sirene.yaml` and modify scopes if you want

```yaml
// config/packages/routmoute_sirene.yaml

routmoute_sirene:
    consumer_key: '%env(ROUTMOUTE_SIRENE_CONSUMER_KEY)%'
    consumer_secret: '%env(ROUTMOUTE_SIRENE_CONSUMER_SECRET)%'
```

## Configuration

### Step 1: Create your INSEE Application

- Go to <https://api.insee.fr>
- Create a New Application
- Copy `consumer-key` and `consumer-secret` for next step

### Step 2: Create your env variables

Add this environments vars in your `.env` file.

```
ROUTMOUTE_SIRENE_CONSUMER_KEY=YourConsumerKey
ROUTMOUTE_SIRENE_CONSUMER_SECRET=YourConsumerSecret
```

## Usage

Example usage in Controller:

```php
<?php
namespace App\Controller;

use Routmoute\Bundle\RoutmouteSireneBundle\Service\RoutmouteSireneApiService;

class MyController extends AbstractController
{
    public function index(RoutmouteSireneApiService $sireneAPI)
    {
        // search company by siret
        $companyInfos = $sireneAPI->siret("<siret>");

        // search company by siren
        $companyInfos = $sireneAPI->siren("<siren>");

        /*
        search company by etablissement infos
            Array of search (required)
            [
                "city" => "libelleCommuneEtablissement",
                "cp" => "codePostalEtablissement",
                "company" => "denominationUniteLegale",
                "sigle" => "sigleUniteLegale",
                "ape" => "activitePrincipaleUniteLegale",
                "cj" => "categorieJuridiqueUniteLegale"
            ]

            orderBy (default "siren")
            "siret" or "siren"

            Int page (default 1)

            Int number of results by page (default 20)
        */
        $companyInfos = $sireneAPI->searchEtablissement([
            "city" => "PARIS"
        ], "siren", 1, 10);
    }
}
```

## Parameters

### `consumer_key`

_Required_
The `consumer_key` provided by insee

### `consumer_secret`

_Required_
The `consumer_secret` provided by insee
