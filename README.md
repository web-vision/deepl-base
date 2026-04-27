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
composer require 'web-vision/deepl-base':'2.*.*@dev'
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

```bash
echo '>> Create release based on configuration' ; \
  RELEASE_BRANCH='main' ; \
  RELEASE_VERSION="2.0.1"
  DEV_VERSION="2.0.2" ; \
  echo ">> Checkout branches" && \
  git checkout main && \
  git fetch --all && \
  git pull --rebase && \
  git checkout ${RELEASE_BRANCH} && \
  git pull --rebase && \
  echo ">> Create release ${RELEASE_VERSION}" && \
  git checkout -b release-${RELEASE_VERSION} && \
  sed -i "s/^COMPOSER_ROOT_VERSION.*/COMPOSER_ROOT_VERSION=\"${RELEASE_VERSION}\"/" Build/Scripts/runTests.sh && \
  sed -i "s/^  RELEASE_VERSION.*/  RELEASE_VERSION=\"${RELEASE_VERSION}\"/" README.md && \
  sed -i "s/^  DEV_VERSION.*/  DEV_VERSION=\"${DEV_VERSION}\"/" README.md && \
  tailor set-version ${RELEASE_VERSION} && \
  composer config "extra"."typo3/cms"."version" "${RELEASE_VERSION}" && \
  echo "${RELEASE_VERSION}" > VERSION && \
  git add . && \
  git commit -m "[RELEASE] ${RELEASE_VERSION}" && \
  git push --set-upstream origin release-${RELEASE_VERSION} && \
  gh pr create --fill --base ${RELEASE_BRANCH} --title "[RELEASE] ${RELEASE_VERSION}" && \
  sleep 10 && \
  gh pr checks --watch --interval 2 && \
  sleep 10 && \
  gh pr merge -rd --admin && \
  git remote prune origin && \
  git tag ${RELEASE_VERSION} && \
  git push origin ${RELEASE_VERSION} && \
  echo ">> Post-release - set dev version: ${DEV_VRESION}-dev" && \
  git checkout -b set-version-${DEV_VERSION} && \
  sed -i "s/^COMPOSER_ROOT_VERSION.*/COMPOSER_ROOT_VERSION=\"${DEV_VERSION}-dev\"/" Build/Scripts/runTests.sh && \
  tailor set-version ${DEV_VERSION} && \
  composer config "extra"."typo3/cms"."version" "${DEV_VERSION}-dev" && \
  echo "${DEV_VERSION}-dev" > VERSION && \
  git add . && \
  git commit -m "[TASK] Set dev version ${DEV_VERSION}" && \
  git push --set-upstream origin set-dev-version-${DEV_VERSION} && \
  gh pr create --fill --base ${RELEASE_BRANCH} --title "[TASK] Set dev version \"${DEV_VERSION}-dev\"" && \
  sleep 10 && \
  gh pr checks --watch --interval 2 && \
  sleep 10 && \
  gh pr merge -rd --admin && \
  git remote prune origin
```

