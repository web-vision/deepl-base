# Contributing to deepl-base

Thank you for helping. This file explains how a change gets into this
extension: which branch, how to test it, how to write the commit and what a
pull request needs to be merged.

deepl-base is the shared base of the DeepL extensions of web-vision. Its
documentation says it plainly: it is **not public API for other
extensions**. It follows semantic versioning as far as it can, but a minor
version may still break something. Keep that in mind before you build on it.

## Table of contents

- [Issues and security](#issues-and-security)
- [Branches](#branches)
- [What lives here](#what-lives-here)
- [Getting started](#getting-started)
- [Tests and checks](#tests-and-checks)
- [Code rules](#code-rules)
- [TYPO3 JavaScript overrides](#typo3-javascript-overrides)
- [Documentation and changelog](#documentation-and-changelog)
- [Commit messages](#commit-messages)
- [Pull requests](#pull-requests)
- [Other DeepL extensions](#other-deepl-extensions)

## Issues and security

- Bugs and feature requests are GitHub issues, with the templates offered
  when you open one. Include the TYPO3 version, the version of this extension
  and the steps to reproduce.
- **Security issues are never reported publicly.** See
  [SECURITY.md](SECURITY.md).
- The maintainers track their work in an internal tracker, project `DPL`.
  That is why commits and pull requests refer to `DPL-123`. You do not need
  access to it.

## Branches

| Branch | Version | TYPO3        | PHP        | State                         |
|--------|---------|--------------|------------|-------------------------------|
| `main` | 2.x     | 13.4, 14.3   | 8.2 to 8.5 | development of the next 2.x   |
| `1`    | 1.x     | 12.4, 13.4   | 8.1 to 8.4 | bug fixes and security fixes  |

- Open a pull request against `main`. A fix that is needed in 1.x as well is
  a second pull request against `1`, made after the first one, from a branch
  with the suffix `-1` (`bugfix/my-fix` and `bugfix/my-fix-1`), with the same
  title. The maintainers can do that second one for you.
- The branch `1` has its own `CONTRIBUTING.md`. Read that one for a change
  there: the supported versions, the structure and some code rules differ.

## What lives here

- **The localization wizard of the page module on TYPO3 v13**, replaced by
  one whose modes come from events, so several extensions add their own
  (`GetLocalizationModesEvent`, `LocalizationProcessPrepareDataHandlerCommandMapEvent`,
  `LocalizationMode`, `LocalizationModesCollection`, the controller and the
  listeners in `Core13/`). This part is **deprecated and removed in 3.0.0**.
  TYPO3 v14 has its own localization handlers and does not need it.
  Bug fixes are welcome, new features for it are not.
- **The translation dropdown of the page module on TYPO3 v13**, filled by
  listeners of `ModifyInjectVariablesViewHelperEvent` through the
  `deeplbase:injectVariables` ViewHelper and the template overrides in
  `Resources/Private/Core13/Backend/`.
- **`DeeplBaseSvgIconProvider`**, a colour mode aware SVG icon provider for
  all TYPO3 versions.

## Getting started

You need git, bash and docker or podman. Everything else runs in containers
through `Build/Scripts/runTests.sh`, the same script the CI uses.

```bash
git clone git@github.com:web-vision/deepl-base.git
cd deepl-base
Build/Scripts/runTests.sh -h                        # all options and suites
Build/Scripts/runTests.sh -t 13 -s composerUpdate   # install for TYPO3 v13
Build/Scripts/runTests.sh -t 13 -s unit
```

- `-t` selects the TYPO3 version (`13`, default, or `14`). The installation
  in `.Build/` exists once: run `-s composerUpdate` with the same `-t` before
  the suites of that version, and never two versions at the same time.
- `-b docker` or `-b podman` selects the container binary. Without it,
  podman is used when it is installed.
- `-p` selects the PHP version (default 8.2).

## Tests and checks

A pull request is merged when these are green for TYPO3 v13 and v14. Run them
locally before you push:

| Check                         | Command                                              |
|-------------------------------|------------------------------------------------------|
| Coding style (check only)     | `Build/Scripts/runTests.sh -t 13 -s cgl -n`          |
| Coding style (fix)            | `Build/Scripts/runTests.sh -t 13 -s cgl`             |
| PHPStan                       | `Build/Scripts/runTests.sh -t 13 -s phpstan`         |
| PHP lint                      | `Build/Scripts/runTests.sh -t 13 -s lintPhp`         |
| Unit tests                    | `Build/Scripts/runTests.sh -t 13 -s unit`            |
| Unit tests, random order      | `Build/Scripts/runTests.sh -t 13 -s unitRandom`      |
| Functional tests              | `Build/Scripts/runTests.sh -t 13 -s functional`      |
| Functional tests, other DBMS  | `... -s functional -d mariadb` (also `mysql`, `postgres`) |
| Exception codes unique        | `Build/Scripts/runTests.sh -s checkExceptionCodes`   |
| Test method names             | `Build/Scripts/runTests.sh -s checkTestMethodsPrefix`|
| UTF-8 without BOM             | `Build/Scripts/runTests.sh -s checkBom`              |
| Documentation renders         | `Build/Scripts/runTests.sh -s renderDocumentation`   |

The same with `-t 14` after `-s composerUpdate -t 14`.

- Most of what this extension does only exists on TYPO3 v13. Its tests live
  in `Tests/Functional/Core13/` and run with `-t 13`.
- A bug fix comes with a test that fails without it. A new feature comes
  with tests.

## Code rules

- `declare(strict_types=1);` in every PHP file, classes `final` unless they
  are meant to be extended, dependencies as `readonly` promoted constructor
  properties.
- Services are stateless. They carry no data from one call to the next.
- Dependency injection through Symfony attributes (`#[AsEventListener]`,
  `#[Autoconfigure]`, ...). `Services.yaml` keeps the defaults and the
  resource. `Services.php` loads `Core13/Classes/` on TYPO3 v13 only.
- **No new TYPO3 version checks inside classes.** Code for TYPO3 v13 only
  lives in `Core13/Classes/` (namespace `WebVision\Deepl\Base\Core13\`),
  templates in `Resources/Private/Core13/`, JavaScript in
  `Resources/Public/JavaScript/Core13/`. Configuration that differs is
  selected by the major version in the configuration file itself
  (`Configuration/JavaScriptModules.php`, `Configuration/Backend/AjaxRoutes.php`,
  the `[typo3.branch == "13.4"]` condition in `Configuration/page.tsconfig`).
  `DeeplBaseSvgIconProvider` still checks the version itself, do not add more
  of that.
- Listener identifiers (`deepl-base/determine-default-typo3-localization-modes`,
  `deepl-base/process-default-typo3-localization-modes`,
  `deepl-base/default-translation`) are referenced by other extensions in
  `after:`. Keep them.
- Every exception gets a unique code, the Unix timestamp of the moment you
  write it.
- Test methods use the `#[Test]` attribute and do not start with `test`.
- Coding style is PER-CS 1.0 (`Build/php-cs-fixer/php-cs-rules.php`), PHPStan
  runs on level 8 with a baseline per TYPO3 version in `Build/phpstan/`. A
  change does not add to the baselines.

## TYPO3 JavaScript overrides

`Resources/Public/JavaScript/Core13/localization.js` and
`localization/provider-list.js` replace the modules of the same name of TYPO3
v13 (`Configuration/JavaScriptModules.php`). They are **compiled**, never
edit them by hand:

- The sources are TypeScript in `Build/Overrides/Core13/Sources/`, copies of
  the TYPO3 sources with the changes of this extension. The `.orig-13.4.0`
  file next to a source is the unchanged TYPO3 file, for comparing.
- `Build/Scripts/runTests.sh -t 13 -s buildCoreOverrideJavaScriptFiles`
  clones TYPO3 into `Build/buildsystem/core13/` (git-ignored), checks out
  `v13.4.0`, copies the sources in, builds them with the TYPO3 build and
  copies the result back to `Resources/Public/JavaScript/Core13/`. It needs
  network access and takes a while.
- Commit the changed source and the compiled result together.

## Documentation and changelog

- The documentation is reStructuredText in `Documentation/`, rendered with
  `-s renderDocumentation`. The rendered result is not committed.
- A feature, a breaking change, a deprecation or a fix an integrator notices
  gets a changelog entry in `Documentation/Changelog/<major.minor>/`, named
  like the TYPO3 Core changelog: `Feature-<Topic>.rst`, `Breaking-...`,
  `Deprecation-...`, `Important-...`. It is part of the same commit.

## Commit messages

The [TYPO3 Core commit message rules](https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html):

```
[BUGFIX] DPL-240: Remove .Build/vendor before composer update

Explain why the change is needed and what it does, not how the diff
looks. Wrap the body at 72 characters.
```

- Subject: a tag (`[FEATURE]`, `[BUGFIX]`, `[TASK]`, `[DOCS]`, `[SECURITY]`,
  `[!!!]` in front for a breaking change), the internal issue if there is one,
  imperative mood, at most 52 characters where possible.
- A GitHub issue goes into the footer (`Resolves: #12`) or at the end of
  the subject (`(#12)`).

## Pull requests

- **One pull request carries one commit.** The title of the pull request is
  the subject of the commit. Review changes are amended into the commit and
  force pushed, not added as new commits.
- Rebase onto the current `main` instead of merging it in. The branch is
  merged with "rebase and merge", the only method allowed.
- Required: one approving review and the checks `code quality with core v13
  (8.2)`, `all tests with core v13 (8.2)`, `all tests with core v13 (8.5)`,
  the same for v14, and `render documentation`.
- Name the branch `<type>/<topic>`, for example
  `bugfix/dpl-240-build-vendor`.

## Other DeepL extensions

deepltranslate-core and deepl-write use the localization modes and the
translation dropdown on TYPO3 v13, deepltranslate-core, -glossary and
deepl-write use the icon provider. A change to an event, a value object, a
listener identifier or the icon provider needs a matching change there. The
maintainers coordinate that: say in your pull request when you know a change
affects them.
