<?php

class SomeClass
{
    /**
     * Finds all the migrations in all registered bundles using MigrationsLocator.
     * Passed parameters $directory and $namespace are ignored because of multiple sources of migrations.
     *
     * @param string $directory the passed $directory parameter is ignored
     * @param string|null $namespace the passed $namespace parameter is ignored
     * @return string[] an array of class names that were found with the version as keys
     */
    public function findMigrations($directory, $namespace = null)
    {
    }
}
