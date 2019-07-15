# [Upgrade from v7.3.0 to Unreleased](https://github.com/shopsys/shopsys/compare/v7.3.0...HEAD)

This guide contains instructions to upgrade from version v7.3.0 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]

### Tools

- lower your PHPStan level [#1199](https://github.com/shopsys/shopsys/pull/1199)
    - if you have upgraded your project from v7.2.x to v7.3.0 you have already done this step as a part of the previous upgrade
    - in your `build.xml` file, add a new property `phpstan.level` (for the properties to be loaded, they have to be above the import task)
        ```diff
              <property name="path.framework" value="${path.vendor}/shopsys/framework"/>

        -     <import file="${path.framework}/build.xml"/>
        -
              <property name="is-multidomain" value="true"/>
        +     <property name="phpstan.level" value="1"/>

        +     <import file="${path.framework}/build.xml"/>
        +
          </project>
        ```
    - remove ignored error patterns that are not matched in reported errors from `phpstan.neon` after running `php phing phpstan`

[shopsys/framework]: https://github.com/shopsys/framework
