# Dumping and Importing the Database
Sometimes you may need to dump (i.e. export) and import the application database.
The typical use case is creating and restoring a database backup or transferring database from one machine to another.

In Shopsys Framework, database dumps consist of `public` schema only.
This schema contains all the application data.
There are other database objects inside `pg_catalog` schema (like collations or extensions) but those are not considered part of the application database and therefore are not included in database dumps.  

## Dumping (exporting) database 
The following command will create a SQL file with database dump:
```
php bin/console shopsys:database:dump dump.sql
```

## Importing database into the current database
If you want to import the database into an existing application database, you first need wipe all the data in the current database:
```
phing db-wipe-public-schema
```
**Warning: This command wipes everything in `public` database schema (i.e. you will lose all application data)!**

Then you can import the dump:
```
psql --quiet --username=database_user target_database_name < dump.sql
```

Replace `database_user` and `target_database_name` with the correct values (from your `app/config/parameters.yml`).
The command will prompt you for the user's password.

## Importing database into a new database
First, edit your `app/config/parameters.yml` and set the new database name.

*Note: If you are not in the* DEVELOPMENT *environment you will have to clear the cache via `phing clean` for the changes to take effect.*

After that you can create the new database including the required content of `pg_catalog` schema by executing:
```
phing db-create
```

Then you can import the dump:
```
psql --quiet --username=database_user target_database_name < dump.sql
```

Replace `database_user` and `target_database_name` with the correct values (from your `app/config/parameters.yml`).
The command will prompt you for the user's password.
