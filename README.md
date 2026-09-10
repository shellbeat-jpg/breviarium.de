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

```text
hidden/cron1.sh ... hidden/cron7.sh
    -> hidden/temp_1.php ... hidden/temp_7.php
    -> hidden/includes/functions.php:getOffersFromPriceAndNode()
    -> Amazon ECS/API
    -> writes:
       - products
       - products_description
       - products_to_categories
       - amazonCreator
       - amazonOffers

inc/xtc_eurobuch.inc.php (Eurobuch aggregator)
    -> marketplace offers (incl. AbeBooks/Buchfreund/others)
    -> tags/mappings (Platform, EurobuchID, AbeBooks id)
    -> amazonOffers

hidden/abebooks.php + hidden/getAbeBooksFeed.php
    -> AbeBooks side path
    -> amazonOffers

hidden/getOffersFromBuchfreund.php (and related Buchfreund scripts)
    -> Buchfreund side path / staging
    -> amazonOffers

hidden/copy.php (maintenance)
    -> hidden/makeXselling.php
hidden/makeXselling.php
    -> products_xsell
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
