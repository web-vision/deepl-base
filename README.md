# TYPO3 Extension `DeepL Base`

|                  | URL                                                                     |
|------------------|-------------------------------------------------------------------------|
| **Repository:**  | https://github.com/fgtclb/academic-bite-jobs                            |
| **Read online:** | https://docs.typo3.org/p/fgtclb/academic/academic-bite-jobs/main/en-us/ |
| **TER:**         | https://extensions.typo3.org/extension/academic_bite_jobs/              |

## Description

This package is a TYPO Extension providing some shared things required
by multiple deepl translate or write related extensions, which should
work together but must working independent of each other requiring this
shared base extension as common ground.

> [!NOTE]
> This extension does not provide anything use-full as direct usage,
> and make no sense to install it solo. Should only be a dependency
> for other extensions.

## Compatibility

| Branch | Version   | TYPO3     | PHP                                     |
|--------|-----------|-----------|-----------------------------------------|
| main   | 1.0.x-dev | v12 + v13 | 8.1, 8.2, 8.3, 8.4 (depending on TYPO3) |
| 1      | ^1        | v12 + v13 | 8.1, 8.2, 8.3, 8.4 (depending on TYPO3) |

## Installation

Install with your flavour:

* [TER](https://extensions.typo3.org/extension/deepl_base/)
* Extension Manager
* composer

We prefer composer installation:

```bash
composer require 'web-vision/deepl-base':'1.*.*@dev'
```

> [!NOTE]
> Until first release you may need to ensure allowing dev versions
> but preferring stable releases which requires:

```shell
composer config minimum-stability "dev" \
&& composer config "prefer-stable" true
```
