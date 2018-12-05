# Introduction to Less
This document describes the behavior of CSS pre-processor [LESS](http://lesscss.org/).

## Files import
In Shopsys Framework we implement `Less` by dividing styles into many `Less components`. Each component has its own file. Filename and component name are the same. We use `@import` command for joining all components contents into one compiled `CSS` file.

Here we can see syntax of `@import` command.
```less
@import 'path/to/directory/component-filename.less';
```

### Usage of @import command
The best way how to import all related files is to create one file, for example, named `main.less`. This file will contain only `@import` commands. Keep in mind where you place this file. Imported path depends on where this file is placed.

#### Unexpected behavior
There is one thing which you should keep in mind. When you try to import a file which does not exist in the given filepath, the compiler will try to find missing file in root directories of files, where is used `@import`.

Let us show this at the example. Assuming you have folder structure and files as it is shown below.
```
│── root-main.less
│── some-component.less
└─── B
    └── b-main.less
```

```less
/** B/b-main.less */

@import "some-component.less";
```

```less
/* some-component.less */

.some-component {
    color: red;
}
```

```less
/* root-main.less */

@import "B/b-main.less";
```

Result CSS of this example will be.
```less
.some-component {
    color: red;
}
```
As an explanation of this behavior, given in the example above, is that compiler is trying to find a file `some-component.less` firstly in the folder where is placed `b-main.less`, then in the directory of `root-main.less`. When it could not find required file in any directory, then it will throw an error during compiling.

#### Example 1 - Importing files from a current folder and its subfolders
Let us have for this example following folder structure.
```
└── common
   └─── core
   |   └── variables.less
   └─── layout
   |   └── header.less
   │── helpers.less
   └── main.less
```

For importing of all files in folder `common` there will exist file `main.less` with the following code.
```less
/* Import helper classes from current directory */
@import 'helpers.less';

/* Import all global variables from subdirectory core */
@import 'core/variables.less';

/* Import styles defined for header */
@import 'layout/header.less';
```

#### Example 2 - Importing files from another directory
Let us have for this example following folder structure.
```
└─── common
|   └─── core
|   |   └── variables.less
|   └─── layout
|   |   └── header.less
|   │── helpers.less
|   └── main.less
└─── domain2
    └─── core
    |   └── variables.less
    └─── layout
    |   └── footer.less
    └── main.less
```
We can see two files named `main.less` located in two different folders.
- `common/main.less` will import only that type of files which defines common styles for all domains
- `domain2/main.less` will import files which extend, add or modify styles for *domain2*

Now we want to extend styles for *domain2* by changing default colors defined in `core/variables.less` and add styles for a footer.
Code below shows up how would `domain2/main.less` look like.
```less
@import '../common/main.less';

/* In order to extend, create or modify behavior of CSS
 * styles defined in directory common we have to import
 * styles related to domain2 after importing main.less
 * from directory common.
 */
@import 'core/variable.less';

@import 'layout/footer.less';
```
