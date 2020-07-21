<p align="left">
    <a href="https://github.com/magebitcom/vsf-amasty-faq-indexer"><img src="https://img.shields.io/github/v/tag/magebitcom/vsf-amasty-faq-indexer" /></a>
    <a href="https://packagist.org/packages/magebit/amasty-faq-indexer"><img src="https://img.shields.io/packagist/v/magebit/amasty-faq-indexer" /></a>
</p>

# Amasty FAQ indexer for Vue Storefront
This repository is a Magento 2 dependency for [Amasty FAQ module for VSF](https://github.com/magebitcom/vsf-amasty-faq)

## Requirements
- [Amasty FAQ](https://amasty.com/faq-and-product-questions-for-magento-2.html) v2.7 and above
- [magebit/vsbridge-static-content-procesor](https://github.com/magebitcom/static-content-processor) - See Installation section for details

## Installation

- Via composer: `composer require magebit/amasty-faq-indexer`
- Manually
    - Create a `Magebit` directory inside `app/code`
    - Clone this repository: `git clone git@github.com:magebitcom/vsf-amasty-faq-indexer.git`
    - This module also requires [magebit/vsbridge-static-content-procesor](https://github.com/magebitcom/static-content-processor)
    which you can install with composer `composer require magebit/vsbridge-static-content-procesor` or going to the repository and following manual installation steps there

## URL rewrites
By default, all indexed content and images will be with magento store urls. If you want to convert links and run images through VSF-API, you'll have to configure `Stores - Configuration - VueStorefront - Indexer - Static Content Processor` and check the "Enable URL Rewrites for Amasty FAQ" option.

## Contributing
Found a bug, have a feature suggestion or just want to help in general?
Contributions are very welcome! Check out the [list of active issues](https://github.com/magebitcom/vsf-amasty-faq-indexer/issues) or submit one yourself.

If you're making a bug report, please include as much details as you can and preferably steps to repreduce the issue.
When creating Pull Requests, don't for get to list your changes in the [CHANGELOG](/CHANGELOG.md) and [README](/README.md) files.

---

![Magebit](https://magebit.com/img/magebit-logo-2x.png)

*Have questions or need help? Contact us at info@magebit.com*



