# Upgrade to v8.0.0

This guide contains instructions to upgrade to v8.0.0.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]

### Application
- constructors of `FrameworkBundle\Model\Mail\Mailer` and `FrameworkBundle\Component\Cron\CronFacade` classes were changed so if you extend them change them accordingly: ([#875](https://github.com/shopsys/shopsys/pull/875)).
    - `CronFacade::__construct(Logger $logger, CronConfig $cronConfig, CronModuleFacade $cronModuleFacade, Mailer $mailer)`
    - `Mailer::__construct(Swift_Mailer $swiftMailer, Swift_Transport $realSwiftTransport)`
    - find all usages of the constructors and fix them
- `EntityNameResolver` was added into constructor of these classes: ([#918](https://github.com/shopsys/shopsys/pull/918))
    - CronModuleFactory
    - PersistentReferenceFactory
    - ImageFactory
    - FriendlyUrlFactory
    - SettingValueFactory
    - UploadedFileFactory
    - AdministratorGridLimitFactory
    - EnabledModuleFactory
    - ProductCategoryDomainFactory
    - ProductVisibilityFactory
    - ScriptFactory
    - SliderItemFactory

    In case of extending one of these classes, you should add an `EntityNameResolver` to a constructor and use it in a `create()` method to resolve correct class to return.
- run `php phing standards-fix` so all nullable values will be now defined using nullability (?) symbol ([#1010](https://github.com/shopsys/shopsys/pull/1010))
- replace `IvoryCKEditorBundle` with `FOSCKEditorBundle` ([#1072](https://github.com/shopsys/shopsys/pull/1072))
    - replace the registration of the bundle in `app/AppKernel`
        ```diff
        - new Ivory\CKEditorBundle\IvoryCKEditorBundle(), // has to be loaded after FrameworkBundle and TwigBundle
        + new FOS\CKEditorBundle\FOSCKEditorBundle(), // has to be loaded after FrameworkBundle and TwigBundle
        ```
    - rename `app/config/packages/ivory_ck_editor.yml` to `app/config/packages/fos_ck_editor.yml` and change the root key in its content
        ```diff
        - ivory_ck_editor:
        + fos_ck_editor:
        ```
    - change the package in `composer.json`
        ```diff
        - "egeloen/ckeditor-bundle": "^4.0.6",
        + "friendsofsymfony/ckeditor-bundle": "^2.1",
        ```
    - update all usages of the old bundle in
        - extended twig templates like
            ```diff
            - {% use '@IvoryCKEditor/Form/ckeditor_widget.html.twig' with ckeditor_widget as base_ckeditor_widget %}
            + {% use '@FOSCKEditor/Form/ckeditor_widget.html.twig' with ckeditor_widget as base_ckeditor_widget %}
            ```
        - javascripts like
            ```diff
            - if (element.type === Shopsys.constant('\\Ivory\\CKEditorBundle\\Form\\Type\\CKEditorType::class')) {
            + if (element.type === Shopsys.constant('\\FOS\\CKEditorBundle\\Form\\Type\\CKEditorType::class')) {
            ```
        - configuration files like
            ```diff
            Shopsys\FrameworkBundle\Form\WysiwygTypeExtension:
                tags:
            -       - { name: form.type_extension, extended_type: Ivory\CKEditorBundle\Form\Type\CKEditorType }
            +       - { name: form.type_extension, extended_type: FOS\CKEditorBundle\Form\Type\CKEditorType }
            ```
        - php code like
            ```diff
            namespace Shopsys\FrameworkBundle\Form\Admin\Transport;

            - use Ivory\CKEditorBundle\Form\Type\CKEditorType;
            + use FOS\CKEditorBundle\Form\Type\CKEditorType;
            ```

### Tools
- improve `build-dev.xml` to use test prefix for elasticsearch in tests ([#933](https://github.com/shopsys/shopsys/pull/933))
   - add `test-product-search-recreate-structure,test-product-search-export-products` to the end of `test-db-demo` phing target in your `build-dev.xml`
   - add new phing targets:
        ```xml
        <target name="test-product-search-recreate-structure" depends="test-product-search-delete-structure,test-product-search-create-structure" description="Recreates structure for searching via elasticsearch in test environment (deletes existing structure and creates new one)." />
        ```
        and
        ```xml
        <target name="test-product-search-create-structure" description="Creates structure for searching via elasticsearch for test environment.">
            <exec executable="${path.php.executable}" passthru="true" checkreturn="true">
                <arg value="${path.bin-console}" />
                <arg value="--env=test" />
                <arg value="shopsys:product-search:create-structure" />
            </exec>
        </target>

        <target name="test-product-search-delete-structure" description="Deletes structure for searching via elasticsearch for test environment.">
            <exec executable="${path.php.executable}" passthru="true" checkreturn="true">
                <arg value="${path.bin-console}" />
                <arg value="--env=test" />
                <arg value="shopsys:product-search:delete-structure" />
            </exec>
        </target>

        <target name="test-product-search-export-products" description="Exports all products for searching via elasticsearch for test environment.">
            <exec executable="${path.php.executable}" passthru="true" checkreturn="true">
                <arg value="${path.bin-console}" />
                <arg value="--env=test" />
                <arg value="shopsys:product-search:export-products" />
            </exec>
        </target>
        ```

[shopsys/framework]: https://github.com/shopsys/framework
