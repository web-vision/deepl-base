# Contributing to deepl-base 1.x

This is the branch `1`: deepl-base 1.x for TYPO3 12.4 and 13.4. It receives
bug fixes and security fixes. New development happens on `main` (2.x), whose
`CONTRIBUTING.md` describes the extension as it is developed today. This file
describes the rules of this branch.

deepl-base is the shared base of the DeepL extensions of web-vision and is
**not public API for other extensions**.

## Table of contents

- [Issues and security](#issues-and-security)
- [Branches](#branches)
- [What lives here](#what-lives-here)
- [Getting started](#getting-started)
- [Tests and checks](#tests-and-checks)
- [Code rules on this branch](#code-rules-on-this-branch)
- [TYPO3 JavaScript overrides](#typo3-javascript-overrides)
- [Documentation](#documentation)
- [Commit messages](#commit-messages)
- [Pull requests](#pull-requests)

## Issues and security

- Bugs are GitHub issues, with the templates offered when you open one. Say
  that you use 1.x, and which TYPO3 version.
- **Security issues are never reported publicly.** See
  [SECURITY.md](SECURITY.md). 1.x receives security fixes until the date
  named there.
- The maintainers track their work in an internal tracker, project `DPL`.
  That is why commits refer to `DPL-123`. You do not need access to it.

## Branches

| Branch | Version | TYPO3        | PHP        | State                         |
|--------|---------|--------------|------------|-------------------------------|
| `main` | 2.x     | 13.4, 14.3   | 8.2 to 8.5 | development                   |
| `1`    | 1.x     | 12.4, 13.4   | 8.1 to 8.4 | bug fixes and security fixes  |

- A fix is made on `main` first, when `main` has the bug too, and then
  brought to this branch in a second pull request with the same title. Name
  its branch after the one of `main` with the suffix `-1`
  (`bugfix/my-fix-1`).
- A fix that only concerns 1.x is a pull request against `1` directly.
- **A backport is written for this branch.** The code of `main` uses a
  structure and PHP features that 1.x does not have. Adapt the change to the
  rules below, do not copy it.

## What lives here

- **The localization wizard of the page module**, replaced on TYPO3 v12 and
  v13 by one whose modes come from events, so several extensions add their
  own (`GetLocalizationModesEvent`, `LocalizationProcessPrepareDataHandlerCommandMapEvent`,
  `LocalizationMode`, `LocalizationModesCollection`,
  `Controller/Backend/LocalizationController`).
- **The translation dropdown of the page module**, filled by listeners of
  `ModifyInjectVariablesViewHelperEvent` through the
  `deeplbase:injectVariables` ViewHelper and the template overrides in
  `Resources/Private/Core12/Backend/` and `Resources/Private/Core13/Backend/`.
- **`DeeplBaseSvgIconProvider`**, a colour mode aware SVG icon provider.

## Getting started

You need git, bash and docker or podman. Everything else runs in containers
through `Build/Scripts/runTests.sh`, the same script the CI uses.

```bash
git clone git@github.com:web-vision/deepl-base.git
cd deepl-base
git switch 1
Build/Scripts/runTests.sh -h                        # all options and suites
Build/Scripts/runTests.sh -t 12 -s composerUpdate   # install for TYPO3 v12
Build/Scripts/runTests.sh -t 12 -s unit
```

- `-t` selects the TYPO3 version (`12`, default, or `13`). The installation
  in `.Build/` exists once: run `-s composerUpdate` with the same `-t` before
  the suites of that version, and never two versions at the same time.
- `-p` selects the PHP version. The default on this branch is 8.1, which
  TYPO3 v13 does not support: run v13 with `-p 8.2`.
- `-b docker` or `-b podman` selects the container binary. Without it,
  podman is used when it is installed.

## Tests and checks

A pull request is merged when these are green for TYPO3 v12 and v13:

| Check                         | Command                                                  |
|-------------------------------|----------------------------------------------------------|
| Coding style (check only)     | `Build/Scripts/runTests.sh -t 12 -s cgl -n`              |
| PHPStan                       | `Build/Scripts/runTests.sh -t 12 -s phpstan`             |
| PHP lint                      | `Build/Scripts/runTests.sh -t 12 -s lintPhp`             |
| Unit tests                    | `Build/Scripts/runTests.sh -t 12 -s unit`                |
| Unit tests, random order      | `Build/Scripts/runTests.sh -t 12 -s unitRandom`          |
| Functional tests              | `Build/Scripts/runTests.sh -t 12 -s functional`          |
| Functional tests, other DBMS  | `... -s functional -d mariadb` (also `mysql`, `postgres`) |
| Exception codes unique        | `Build/Scripts/runTests.sh -s checkExceptionCodes`       |
| Test method names             | `Build/Scripts/runTests.sh -s checkTestMethodsPrefix`    |

The same with `-t 13 -p 8.2` after `-s composerUpdate -t 13 -p 8.2`.

A bug fix comes with a test that fails without it.

## Code rules on this branch

The rules of `main` apply where TYPO3 v12 and PHP 8.1 allow them. These are
the differences:

- **PHP 8.1.** No `readonly` classes, no disjunctive normal form types, no
  typed class constants, no `#[\Override]`. `readonly` promoted properties
  are fine.
- **Event listeners are registered in `Configuration/Services.yaml`** with
  the tag `event.listener` and their identifier. TYPO3 v12 does not know
  `#[AsEventListener]`. There is no `Configuration/Services.php`.
- **No `Core12/` or `Core13/` class directories.** All classes are in
  `Classes/` and serve both versions. What differs lives in resources and
  configuration: templates in `Resources/Private/Core12/` and `Core13/`,
  selected by the `[typo3.branch == ...]` conditions in
  `Configuration/page.tsconfig`, JavaScript in `Resources/Public/JavaScript/Core12/`
  and `Core13/`, selected in `Configuration/JavaScriptModules.php`. A change to
  a template or a script is made for both versions.
- Listener identifiers (`deepl-base/determine-default-typo3-localization-modes`,
  `deepl-base/process-default-typo3-localization-modes`,
  `deepl-base/default-translation`) are referenced by other extensions in
  `after:`. Keep them.
- Coding style is PSR-2 with the PHP 7.4 migration rules
  (`Build/php-cs-fixer/php-cs-rules.php`), not the PER-CS of `main`. PHPStan
  runs on level 8 with a baseline per TYPO3 version in `Build/phpstan/Core12`
  and `Core13`. A change does not add to the baselines.
- Every exception gets a unique code, the Unix timestamp of the moment you
  write it. Tests use the `#[Test]` attribute.

## TYPO3 JavaScript overrides

The files in `Resources/Public/JavaScript/Core12/` and `Core13/` replace
JavaScript modules of TYPO3. They are **compiled**, never edit them by hand.
The sources are TypeScript in `Build/Overrides/Core12/Sources/` and
`Build/Overrides/Core13/Sources/`, the `.orig-<version>` file next to a source
is the unchanged TYPO3 file. `Build/Scripts/runTests.sh -t 12 -s
buildCoreOverrideJavaScriptFiles` (and `-t 13`) clones TYPO3 into
`Build/buildsystem/` (git-ignored), builds the sources against `v12.4.0` or
`v13.4.0` and copies the result back. Commit the changed source and the
compiled result together.

## Documentation

This branch has no `Documentation/` folder and no changelog. `README.md` is
its documentation. Change it when a fix changes what it describes.

## Commit messages

The [TYPO3 Core commit message rules](https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html),
the same as on `main`:

```
[BUGFIX] DPL-240: Remove .Build/vendor before composer update

Explain why the change is needed and what it does. Wrap the body at 72
characters.
```

A backport keeps the subject and the body of the commit on `main` and adapts
the body where the 1.x change differs.

## Pull requests

- One pull request carries one commit, its title the subject of the commit.
  Review changes are amended and force pushed.
- Rebase onto the current `1`. The branch is merged with "rebase and merge".
- Required: one approving review and the checks `code quality with core v12
  (8.1)`, `all tests with core v12 (8.1)`, `all tests with core v12 (8.4)`,
  `code quality with core v13 (8.2)`, `all tests with core v13 (8.2)`,
  `all tests with core v13 (8.5)`.
- deepltranslate-core 5.x, deepltranslate-glossary 5.x and deepl-write 1.x
  build on this branch. A change to an event, a value object, a listener
  identifier or the icon provider needs a matching change there.
