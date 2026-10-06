# Agent instructions

Instructions for coding agents working in this repository. `CLAUDE.md`
imports this file. Read [CONTRIBUTING.md](CONTRIBUTING.md) first: its rules
apply to you in full. This file adds what an agent needs on top of it.

## Know which line you are on

This file belongs to the branch `main`: deepl-base 2.x, TYPO3 13.4 and 14.3,
PHP 8.2 to 8.5. **The branch you work on decides the rules.** For work on `1`
(1.x, TYPO3 12.4 and 13.4), switch to that branch and follow its own
`AGENTS.md` and `CONTRIBUTING.md`, never this one. A backport is written for
the target branch, it is not a copy of the change on `main`.

Check before you start:

```bash
git branch --show-current
git status -sb
```

## Rules

- **Never write to a remote** (push, pull request, issue, comment, review,
  merge) unless the maintainer asks for exactly that.
- **Never credit a tool or a model** in commits, pull requests, issues, code
  comments or documentation. No `Co-authored-by` for it, no "Generated
  with" line. The human who submits the change is its author.
- Scratch files, plans, reports and downloads go into `.agent/` (git
  ignored), never into the tracked tree and never into `/tmp`.
- Verify by running, not by recalling: the suites below, `git`, `composer`.
  Say what you ran and what you did not run.
- An issue reference (`DPL-123`, `#12`) is written only when it is known to
  exist. Do not invent one.

## Running the test harness as an agent

- **Set `CI=true`.** Without a terminal, `runTests.sh` fails with "the input
  device is not a TTY" unless `CI` is `true`:

  ```bash
  export CI=true
  Build/Scripts/runTests.sh -b docker -t 13 -s composerUpdate
  Build/Scripts/runTests.sh -b docker -t 13 -s cgl -n
  Build/Scripts/runTests.sh -b docker -t 13 -s phpstan
  Build/Scripts/runTests.sh -b docker -t 13 -s unit
  Build/Scripts/runTests.sh -b docker -t 13 -s functional
  ```

- **One TYPO3 version at a time.** `.Build/` holds the installation of the
  last `composerUpdate`. Run all suites of v13, then `composerUpdate -t 14`
  and the suites of v14. Never run two `runTests.sh` calls of this checkout
  in parallel.
- `composerUpdate` rewrites `composer.json` while it runs and restores it
  afterwards (`composer.json.orig`). Do not interrupt it, and never commit a
  `composer.json` changed by it.
- `-s cgl` changes files, `-s cgl -n` only checks. Run the check before you
  commit, the fix only on purpose.
- Pass test filters behind `--`: `-s unit -- --filter LocalizationModeTest`.
- `-s buildCoreOverrideJavaScriptFiles` clones the whole TYPO3 repository
  into `Build/buildsystem/` and builds its JavaScript. Run it only when a
  TypeScript source in `Build/Overrides/` changed, and never edit the
  compiled files in `Resources/Public/JavaScript/Core13/` by hand.
- Done means: the suites of the changed area green on **both** v13 and v14,
  `cgl -n` and `phpstan` green on both, `renderDocumentation` when
  `Documentation/` changed.

## Code you are likely to touch

- `Classes/` is loaded on both TYPO3 versions, `Core13/Classes/` on v13 only.
  Code that only v13 needs goes into `Core13/`, never behind a version check
  in a class.
- The localization modes, their events and the v13 wizard are deprecated
  and removed in 3.0.0. Fix bugs there, do not extend them.
- deepltranslate-core, deepltranslate-glossary and deepl-write build on this
  extension: the events in `Classes/Event/`, `LocalizationMode`,
  `LocalizationModesCollection`, the listener identifiers `deepl-base/*`
  they order themselves against, the `deeplbase:injectVariables` slots
  (`languageTranslationDropdown`, `languageColumnButtons`) and
  `DeeplBaseSvgIconProvider`. Changing them breaks those extensions. Say so
  in the result and do not change them without being asked.
- The templates in `Resources/Private/Core13/Backend/` override templates of
  TYPO3 itself (`Configuration/page.tsconfig`). Compare with the template of
  the installed TYPO3 version before you change one.

## Commits and pull requests

- One commit per pull request, following the commit rules in CONTRIBUTING.md.
  Amend and force push (`--force-with-lease`) when asked to update a pull
  request, do not add fix-up commits.
- Rebase onto `origin/main`, never merge it in.
- When a change also needs a pull request in another DeepL extension, say so,
  and name the order in which they have to be merged: this extension first.
