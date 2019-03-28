# Components

## Definition
Components are classes usable for any type of applications, not only e-commerce projects(unlike [Application model](../model/introduction-to-model-architecture.md)),
for example helper classes for working with Doctrine, HttpFoundation, Router etc.

You can find them in component folder, but we plan to decouple these classes into separate package.
Components cannot be dependent on any other classes from [shopsys/framework](https://github.com/shopsys/framework) package except on the other components.
