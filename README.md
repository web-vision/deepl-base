# TYPO3 Extension `DeepL Base`

|                  | URL                                                        |
|------------------|------------------------------------------------------------|
| **Repository:**  | https://github.com/web-vision/deepl-base                   |
| **Read online:** | https://docs.typo3.org/p/web-vision/deepl-base/main/en-us/ |
| **TER:**         | https://extensions.typo3.org/extension/deepl_base/         |

## Description

This package is a TYPO Extension providing some shared things required
by multiple deepl translate or write related extensions, which should
work together but must work independent of each other, requiring this
shared base extension as common ground.

> [!NOTE]
> This extension does not provide anything useful as direct usage,
> and makes no sense to install it standalone. Should only be a dependency
> for other extensions.

## Compatibility

| Branch | Version       | TYPO3     | PHP                                     |
|--------|---------------|-----------|-----------------------------------------|
| main   | 2.0.x-dev     | v13       | 8.2, 8.3, 8.4, 8.5 (depending on TYPO3) |
| 1      | ^1, 1.0.x-dev | v12 + v13 | 8.1, 8.2, 8.3, 8.4 (depending on TYPO3) |

## Installation

Install with your flavour:

* [TER](https://extensions.typo3.org/extension/deepl_base/)
* Extension Manager
* composer

We prefer composer installation:

```bash
composer require 'web-vision/deepl-base':'^2'
```

> [!NOTE]
> Until first release you may need to ensure allowing dev versions
> but preferring stable releases which requires:

```shell
composer config minimum-stability "dev" \
&& composer config "prefer-stable" true \
&& composer require 'web-vision/deepl-base':'2.*.*@dev'
```

## Documentation

> [!NOTE]
> For the start, the documentation for developers and integrators is contained
> here in the README.md file and will be converted into rendered documentation
> at a later point.

### PageLayout module localization model - Translation Modes

### ViewHelper

#### InjectVariablesViewHelper

`InjectVariablesViewHelper` can be placed in fluid templates and
requires to define a speaking identifier used to dispatch the PSR-14
`ModifyInjectVariablesViewHelperEvent` event, which can be used to
set or modify variables either in the global current template scope
or for children rendering scope.

##### Example usage in fluid template

```xhtml
<html
    data-namespace-typo3-fluid="true"
    xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
    xmlns:deeplbase="http://typo3.org/ns/WebVision/Deepl/Base/ViewHelpers"
>
<deeplbase:injectVariables identifier="custom-template-variable-inject">
    Render {globalOrLocalVariableProviderVariable} only available in children
    context.
</deeplbase:injectVariables>
Render {globalOnlyVariableProviderVariable} only ignoring local variable provider
changes.
</html>
```

##### Explicit usage from this extension

```xhtml
<html
    data-namespace-typo3-fluid="true"
    xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
    xmlns:deeplbase="http://typo3.org/ns/WebVision/Deepl/Base/ViewHelpers"
>
<deeplbase:injectVariables identifier="languageTranslationDropdown">
    <f:for each="{translationPartials}" as="translationPartial">
        <f:render partial="{translationPartial}" arguments="{_all}"/>
    </f:for>
</deeplbase:injectVariables>
</html>
```

###### What does it do?

This part renders partials registered by an EventListener. With this identifier
an extension could provide its own translation dropdown for the Backend PageView.
The extension must be self-aware registering a partial, which is callable by Fluid.

A working example is provided at `Classes/EventListener/DefaultTranslationDropdownEventListener.php`
and `Resources/Private/Backend/Partials/Translation/DefaultTranslationDropdown.html`.

##### ModifyInjectVariablesViewHelperEvent

* `getIdentifier(): string`: identifier used within the fluid template and
  should
* `getGlobalVariableProvider(): VariableProviderInterface`: provides the
  fluid variable container of the current context. Modification will be
  available after the ViewHelper and within the children context unless
  overridden within the `getLocalVariableProvider`.
* `getLocalVariableProvider(): VariableProviderInterface`: provides the
  fluid variable container with children context only variables, overriding
  global variables. Local variable does not change the variables in the
  template after the ViewHelper call.

### Modified Backend Templates

> [!NOTE]
> Modifed backend templates are listed here describing the modification, for
> example, if one or more [InjectVariableViewhelper](#injectvariablesviewhelper)
> has been placed along with the identifier and use-case.

## Create a release (maintainers only)

Prerequisites:

* git binary
* ssh key allowed to push new branches to the repository
* GitHub command line tool `gh` installed and configured with user having permission to create pull requests.

**Prepare release locally**

> Set `RELEASE_BRANCH` to branch release should happen, for example: 'main'.
> Set `RELEASE_VERSION` to release version working on, for example: '1.0.0'.

```shell
echo '>> Prepare release pull-request' ; \
  RELEASE_BRANCH='main' ; \
  RELEASE_VERSION='1.0.0' ; \
  git checkout main && \
  git fetch --all && \
  git pull --rebase && \
  git checkout ${RELEASE_BRANCH} && \
  git pull --rebase && \
  git checkout -b prepare-release-${RELEASE_VERSION} && \
  composer require --dev "typo3/tailor" && \
  ./.Build/bin/tailor set-version ${RELEASE_VERSION} && \
  composer remove --dev "typo3/tailor" && \
  git add . && \
  git commit -m "[TASK] Prepare release ${RELEASE_VERSION}" && \
  git push --set-upstream origin prepare-release-${RELEASE_VERSION} && \
  gh pr create --fill-verbose --base ${RELEASE_BRANCH} --title "[TASK] Prepare release for ${RELEASE_VERSION} on ${RELEASE_BRANCH}" && \
  git checkout main && \
  git branch -D prepare-release-${RELEASE_VERSION}
```

Check pull-request and the pipeline run.

**Merge approved pull-request and push version tag**

> Set `RELEASE_PR_NUMBER` with the pull-request number of the preparation pull-request.
> Set `RELEASE_BRANCH` to branch release should happen, for example: 'main' (same as in previous step).
> Set `RELEASE_VERSION` to release version working on, for example: `0.1.4` (same as in previous step).

```shell
RELEASE_BRANCH='main' ; \
RELEASE_VERSION='1.0.0' ; \
RELEASE_PR_NUMBER='123' ; \
  git checkout main && \
  git fetch --all && \
  git pull --rebase && \
  gh pr checkout ${RELEASE_PR_NUMBER} && \
  gh pr merge -rd ${RELEASE_PR_NUMBER} && \
  git tag ${RELEASE_VERSION} && \
  git push --tags
```

This triggers the `on push tags` workflow (`publish.yml`) which creates the upload package,
creates the GitHub release and also uploads the release to the TYPO3 Extension Repository.
