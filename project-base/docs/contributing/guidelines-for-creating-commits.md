# Guidelines for Creating Commits
We care about a clean and understandable history of changes as it, among other advantages, makes cherry-picking and merging to your Core much easier.

Underestimating the importance of maintaining a clean git history can lead to many problems, for example, it could be more difficult to understand changes in source code and their context.

## What makes a good commit
* **It is atomic** - it contains only related modifications (similar to the Single Responsibility Principle).
* **It is a functional unit** - it contains all related modifications and tests and it should not break anything (that means, all tests pass). When you follow this rule, you can be sure that your application is functional at any revision point. 

## Commit messages
We have agreed on a unified form of commit messages and have written down a few rules, so understanding of commit context is much easier.
This way we are able to understand how the specific commit changed the application without even looking into source code changes.

### Common rules
* Commit messages should be short and brief. However, if you need to include some details in the commit message, you should write a short summary on the first line, leave one blank line and then write the more detailed explanation, usually in form of a list.
* If you have a lot of information to share, you should write a short summary of the modification on the first line, then write a more detailed description below. Always try to include all relevant notes.

```
administrator is now not allowed to put h1 headings to wysiwyg

- done because of SEO - there should be always only one h1 tag
- allowed to use all default format tags except h1
- see http://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-format_tags
```

* Message should contain the information **what** changed and **why**.
* If your commit message is too long it might be a good idea to split it into several commits.
* First letter of a commit message is in lowercase, except when the first word is a proper name (eg. name of a class).
* When a commit is related only to a specific part (admin, docs, design) or only to one class or file (composer.json, services.yml), it should be prefixed with that name.
* Present tense is used for describing the change of application behavior. It is helpful to use words to define time, such as "now", in order to describe the current state. Otherwise, the message could be misunderstood as a description of an error that was fixed.

```
admin: product list now displays name instead of ID
```

* Past tense is used for describing the specific change made in the code, such as renaming, adding classes, and simple modifications.

```
docs: added rule about title capitalization in Guidelines for Writing Documentation
```

```
OrderFlowFacade: removed unused uses
```

* Never start the message with the phrase "fix:". Again, it prevents developers from getting confused between description of the fixed error and description of the current state.
* Method or function name should be always followed by parentheses.
* Property or variable name should be always prefixed with a dollar sign.
* When merging a modification from the SSFW internal backlog, we use our identifier of user story or bug in the beginning of merge commit message.

```
[US-2022] OrderFlow is not autowired anymore
```

```
[BG-1598] category translations are now correctly saved on category edit`
```

### Rules for specific use cases
#### Simple modification
* e.g. fixing a typo or incorrect annotation, renaming local variable.
* Since these modifications do not influence application behavior, you should use a short and simple messages.

```
typo
```

```
annotation fix
```

```
renamed variable
```

#### Renaming methods and properties
* Commit message should contain name of affected class and it should be obvious what was the previous state and what is the current state.

```
ProductFacade: renamed method bar() to baz()
```

```
ProductFacade: renamed property $name to $title
```

```
ProductFacade: renamed getBy*() to getProductBy*()
```

#### Adding classes, properties or tests
* These usually need more information and should contain the reason why you added them.
```
Product: added property $weight

- needed to make transport availability dependent on the total weight of the products in cart
```
