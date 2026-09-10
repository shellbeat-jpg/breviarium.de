# breviarium.de reverse engineering

This repository is the base for a reverse engineering effort of the platform breviarium.de.

The platform collected offers from several antique book marketplaces and redirected visitors to the source page, including affiliate links.

It is built on the XTC Modified shop system (2011), now known as Modified Ecommerce.

## What the repository contains

- `db.sql` with reconstructed table definitions
- Amazon import jobs in `hidden/temp_1.php` through `hidden/temp_7.php`
- Eurobuch aggregation logic in `inc/xtc_eurobuch.inc.php` and `hidden/includes/functions.php`
- AbeBooks helpers in `hidden/abebooks.php` and `hidden/getAbeBooksFeed.php`
- Buchfreund helpers in `hidden/getOffersFromBuchfreund.php` and related scripts

## Daily DB update workflow

The daily update pipeline is cron-driven and uses recursive hidden endpoints.

```mermaid
flowchart TD
  C[hidden/cron1.sh ... hidden/cron7.sh] --> T[hidden/temp_1.php ... hidden/temp_7.php]
  T --> S[hidden/arrSteps.php]
  T --> G[hidden/includes/functions.php:getOffersFromPriceAndNode]
  G --> API[api/aws_signed_request.php + api/amazon_api_class.php]
  G --> P[products]
  G --> PD[products_description]
  G --> PTC[products_to_categories]
  G --> AC[amazonCreator]
  G --> AO[amazonOffers]
  G --> CD[categories_data*]

  E[inc/xtc_eurobuch.inc.php + hidden/includes/functions.php Eurobuch handlers] --> AO
  AB[hidden/abebooks.php + hidden/getAbeBooksFeed.php] --> AO
  AB --> PEAN[products.EAN]
  BF[hidden/getOffersFromBuchfreund.php + hidden/checkBuchfreundItemsFromISBN.php + hidden/getRssFeed.php] --> BFT[buchfreund]
  BFT --> AO

  MX[hidden/makeXselling.php] --> XS[products_xsell]
  AO --> VIEW[includes/classes/product.php (EurobuchID = 0 default offer view)]
```

```mermaid
flowchart LR
  AMZ[Amazon path: hidden/temp_1.php ... hidden/temp_7.php + getOffersFromPriceAndNode] -->|Platform='Amazon'\nEurobuchID=0| AO[amazonOffers]

  EUR[Eurobuch path: inc/xtc_eurobuch.inc.php + hidden/includes/functions.php Eurobuch handlers] -->|Platform=marketplace\nEurobuchID>0| AO

  ABE[hidden/abebooks.php + hidden/getAbeBooksFeed.php] -->|id_abebooks enrichment| AO

  BFR[hidden/getOffersFromBuchfreund.php + hidden/checkBuchfreundItemsFromISBN.php + hidden/getRssFeed.php] --> BF[buchfreund]
  BF -->|matched/imported offers| AO

  AO -->|id_amazon link| P[products]
  AO --> Q[includes/classes/product.php query (EurobuchID = 0)]
```

### Amazon import

Cron wrappers (`hidden/cron1.sh` through `hidden/cron7.sh`) call:

- `hidden/temp_1.php`
- `hidden/temp_2.php`
- `hidden/temp_3.php`
- `hidden/temp_4.php`
- `hidden/temp_5.php`
- `hidden/temp_6.php`
- `hidden/temp_7.php`

These scripts scan category/price windows based on `hidden/arrSteps.php` and call `hidden/includes/functions.php:getOffersFromPriceAndNode()`.

### Marketplace aggregation

Marketplace offers are stored in `amazonOffers` and are tagged with source metadata:

- `Platform` — marketplace name
- `EurobuchID` — Eurobuch source identifier
- `id_abebooks` — AbeBooks book id when available

Eurobuch queries multiple marketplaces in one feed, including Booklooker, buch.de, Buchfreund, AbeBooks, Alibris, and ZVAB.

### Table usage

The main tables involved in the import workflow are:

- `products`
- `products_description`
- `products_to_categories`
- `products_images`
- `products_attributes`
- `products_xsell`
- `amazonOffers`
- `amazonCreator`
- `buchfreund`
- `categories_data*`

## Notes

Half of the original database is missing. The reconstructed schema is in `db.sql`.
