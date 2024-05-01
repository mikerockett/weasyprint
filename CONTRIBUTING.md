# Contributing to WeasyPrint for Laravel

If you’d like to make a contribution to WeasyPrint for Laravel, you’re more than welcome to [submit a merge request](https://gitlab.com/mikerockett/weasyprint/-/merge_requests/new) against the `main` or current-release branch:

1. If you are introducing a **non-breaking** change, target the `V.x` branch, where `V` is the latest major version of the package. If accepted and does not break any other versions either, it will also be merged into the applicable branches for those versions.
2. If you are introducing a **breaking** change of any kind, target the `main` branch. The change will be released in a new major version when accepted, and will not be added to older versions.

Your request should be as detailed as possible, unless it’s a trivial change.

#### Tests

Should it be required, please make sure that any impacted tests are updated, or new tests are created.

1. If you are introducing a new feature, you will more than likely need to create a new test case where each piece of functionality the new feature introduces may be tested.
2. Otherwise, if you are enhancing an existing feature by adding new functionality, you may add the appropriate test method to the applicable test case.

Then run the tests before opening your merge request:

```shell
$ composer run test
```

#### Formatting

This package uses `johnbacon/stout` to auto-format code. Before committing your code, please run a format over all dirty files:

```shell
$ composer run format
```

#### Commit Messages

Your commit message should be clear and concise. If you’re fixing a bug, start the message with `bugfix:`. If it’s a feature: `feature:`. If it’s a chore, like formatting code: `chore:`.

If you’d simply like to report a bug or request a feature, simply [open an issue](https://gitlab.com/mikerockett/weasyprint/issues).
