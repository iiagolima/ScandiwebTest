# Mage2 Module ScandiwebTest MultistoreMetatag

    ``scandiwebtest/module-multistoremetatag``

 - [Description](#markdown-header-description)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Description
The client has a multi-site setup with some CMS pages that are shared across different
websites. The problem that they are having is that this is causing duplicate content issues and
affecting their SEO rankings.
To counter this we will create a new module that will do the following:
- Added a block to the head;
- The block should be able to read the CMS page’s id and to check if the page is used on
multiple store views/websites;
- If so it should add a hreflang meta tag to the head;
- If the meta tag is displayed - it should display language of the store, like “en-gb”, “en-us”,
etc. As metatag should have specific values for each country;

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/ScandiwebTest`
 - Enable the module by running `bin/magento module:enable ScandiwebTest_MultistoreMetatag`
 - Apply database updates by running `bin/magento setup:upgrade`\*
 - Flush the cache by running `bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require scandiwebtest/module-multistoremetatag`
 - enable the module by running `bin/magento module:enable ScandiwebTest_MultistoreMetatag`
 - apply database updates by running `bin/magento setup:upgrade`\*
 - Flush the cache by running `bin/magento cache:flush`

## Specifications
- Added a block to the head;
- The block should be able to read the CMS page’s id and to check if the page is used on
multiple store views/websites;
- If so it should add a hreflang meta tag to the head;
- If the meta tag is displayed - it should display language of the store, like “en-gb”, “en-us”,
etc. As metatag should have specific values for each country;

- The structure of the meta tag is as follows
    - `<link rel="alternate" hreflang="' . $storeLanguage . '" href="' . $baseUrl . $cmsPageUrl . '" />`
