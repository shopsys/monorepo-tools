# Configuring Jenkins for continuous integration
Continuous Integration (CI) brings to us a lot of options for automatizations some parts of software development process.
Automatic start of build and tests after push? Yes, why not. Need for inspection of the built application in the state of
`xxx-new-feature` branch without having to build on your local station? No problem.

Alternative way is to use [Continuous Integration Using Kubernetes](/docs/kubernetes/continuous-integration-using-kubernetes.md).
Kubernetes will provide you with more features and options, but it's more difficult to setup.

Jenkins is a really powerful tool on his own, but to be able to make everything really automatic and effective, we will need
to make few other tools co-op with Jenkins.

This cookbook describes how to properly set up your Jenkins, to be helpful, automatic and effective.

This cookbook supposes that you already got installed:
* [Jenkins](https://jenkins.io/)
* [Jenkins-autojobs](https://github.com/gvalkov/jenkins-autojobs)
* [Docker](https://www.docker.com/)
* [Docker Compose](https://docs.docker.com/compose/)
* [Git](https://git-scm.com/)
* [Nginx](https://nginx.org/en/)

After completing this cookbook you should know:
- how to configure Jenkins to be able to build multiple branches of Shopsys Framework running in docker
- how jenkins autojobs works
- how to make Jenkins works automatic

## Before we start
We need to make some little changes onto our machine to be able to configure jenkins right.

Log in into a terminal on your machine.

### Setting nginx
Our build adds nginx configuration into a folder so we need nginx to load them, this can be done by editing `/etc/nginx/nginx.conf`.

Add this line into your `nginx.conf`:
```
include /etc/nginx/conf.d/*.conf;
```

This will load every configuration files from `/etc/nginx/conf.d/` folder, in which we will create our new configurations.

#### Allow jenkins to restart nginx service
Every branch adds its piece of configuration into nginx. 
All builds are run as user jenkins, but jenkins does not
have permissions to restart services, so we add a piece of code into our `/etc/sudoers` file.

This file allows us to make exceptions in permission world and allow users to execute something that normally wouldn't be possible.
In general, we could just allow jenkins to be able to run everything same as root, but we want to keep it safe as possible.

Editing this file is not safe, so before editing this file and allowing users to run something, think about it twice before doing so.

To the end of `/etc/sudoers` file we add this code:

```
jenkins ALL=(ALL) NOPASSWD: /usr/sbin/nginx
```

This allows user jenkins to execute nginx operations without requiring a password from him.

#### Prepare nginx proxy configuration file
Our branches will be runned in a container, so we cant use default configuration of nginx, but use nginx to proxy requests into containers.

So we create nginx template and save it into some publicly accessible folder. For this cookbook purposes, I will save it into
`/var/jenkins-templates/nginx-template.conf`.

```
upstream {{JOB_NAME}}-upstream {

        server 127.0.0.1:{{PORT_WEB}};

}

server {

        listen 80;
        server_name ~^(\d+\.)?{{JOB_NAME}}\.your-ci-server-address\.com$;
        ## regular expression for digits here allow us to build multi domain branches

        location / {
                proxy_pass http://{{JOB_NAME}}-upstream;
                proxy_redirect off;
                proxy_set_header Host $host;
                proxy_set_header X-Real-IP $remote_addr;
                proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
                proxy_set_header X-Forwarded-Host $host;   
        }

}

```
This file contains variables defined as `{{variable}}`, these will be rewritten in build, since every branch needs their own nginx proxy.

Basically this file resend requests into container where is another nginx configuration which handles the rest for our selves.

### Credentials
While we will be fetching project from some repository, you need to access this repository somehow.

We recommend you to use ssh for it due to security and fact that ssh does not need any interaction, so it is more appropriate for usage like this.
I don't want to write here full description on how to generate ssh keys, I think
that [this article](https://help.github.com/articles/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent/) summarize it really well.

Only thing that needs to be change is directory of ssh keys, you must remember that everything executed in jenkins is done by user jenkins.
So you need to save ssh keys saved in jenkins home directory. If you struggle with where this home directory is, you can execute: 

```
less /etc/passwd
```

this will output every user in system, you are looking for jenkins user.

Home directory is defined in many distributions different, so I would google this one.

Second thing is that these ssh keys must own jenkins, to make things clear we can just change the owner of this files by `chown`.

My jenkins home directory is `/var/lib/jenkins` so for me command would look like this:

```
sudo chown -R jenkins:jenkins /var/lib/jenkins/.ssh
```

### Permissions
So everything in `/var/lib/jenkins` is owned by jenkins, but we need to make our user which is root in `php-fpm` container to be able to
create, modify and execute files. Thankfully, we can use `setfacl` command to create multiple permission rules onto jenkins folder.

First, we need Jenkins to be able to write into nginx folder since we are modifying the configuration of nginx in the build:
```
setfacl -R -m user:jenkins:rwx /etc/nginx/conf.d
setfacl -dR -m user:jenkins:rwx /etc/nginx/conf.d
```

Next we need to make able for users `root` and `nginx` since they both need to have access to files in application folder:

```
setfacl -R -m user:root:rwx /var/lib/jenkins/workspace
setfacl -dR -m user:root:rwx /var/lib/jenkins/workspace
```

```
setfacl -R -m user:nginx:rwx /var/lib/jenkins/workspace
setfacl -dR -m user:nginx:rwx /var/lib/jenkins/workspace
```


## Setting configuration template
Templates are used for defining way that all branches will be used.

So in jenkins administration, we create a new job, select the freestyle project and name it `template`.

Set up git configuration to clone your Shopsys Framework project.

`template` job should have checked "Delete workspace before build starts" option, this assure that branch will be built from scratch.
If you want to test upgradeability of your project you can left this option unchecked.

Following shell scripts needs to be written into `execute shell` text area in `build` section.

### Setting docker-compose
First create `docker-compose.yml` from `docker-compose.yml.dist` in `docker/conf` directory.

```
cp -f $WORKSPACE/docker/conf/docker-compose.yml.dist $WORKSPACE/docker-compose.yml
```

Running multiple projects in docker on same machine requires to change docker-compose to configure every project to have 
unique ports and container name, see [docker-troubleshooting: Multiple projects ran in docker](../docker/docker-troubleshooting.md#multiple-projects---long-term-solution).

We need to make this automatic on our CI. This can be done by handling 2 problems, ports and container names.
#### Handling ports
For every new branch, we need to make sure that every container in this branch will get their unique ports.

With this problen can help us `netstat`, `netstat` can be installed with `net-tools` package, for example
on CentOS i can install it by:

```
yum install net-tools
```

`Netstat` outputs list of every service or process that occupy some port on our machine.

To get unique ports, we can execute something like this:

```
# Command netstat will show us which ports are currently in use
NETSTAT_LIST=$(netstat -at --numeric-ports);

# Use ports 80xx for "webserver", 44xx for "selenium-server" and 11xx for "adminer", 16xx for redis and 357xx for livereload javascript that is used by nginx
PORT_BASE_WEB=8000;
PORT_BASE_SELENIUM=4400;
PORT_BASE_ADMINER=1100;
PORT_BASE_REDIS_ADMIN=1600;
PORT_BASE_LIVERELOAD_JAVASCRIPT=35729;
MAX_PORT_INCREMENT=99;

# Increment the xx part of the ports and check their availability
for PORT_INCREMENT in $(seq 0 $MAX_PORT_INCREMENT); do
	PORT_WEB=$((PORT_BASE_WEB+PORT_INCREMENT))
	PORT_SELENIUM=$((PORT_BASE_SELENIUM+PORT_INCREMENT));
	PORT_ADMINER=$((PORT_BASE_ADMINER+PORT_INCREMENT));
	PORT_REDIS_ADMIN=$((PORT_BASE_REDIS_ADMIN+PORT_INCREMENT));
	PORT_LIVERELOAD_JAVASCRIPT=$((PORT_BASE_LIVERELOAD_JAVASCRIPT+PORT_INCREMENT));

	# If netstat output doesn't contain any of checked ports we can use them
	if [ -z $(grep ":\($PORT_WEB\|$PORT_SELENIUM\|$PORT_ADMINER\|$PORT_REDIS_ADMIN\|$PORT_LIVERELOAD_JAVASCRIPT\)\ " <<< "$NETSTAT_LIST") ]; then
		break;
	fi
	
	if [ "$PORT_INCREMENT" == "$MAX_PORT_INCREMENT" ]; then
		echo "No combination of available ports for this build found.";
		exit 1;.
	fi
done;
```

This shell script is searching for available ports for each of our containers.
Now we got defined our ports, lets set it into configuration files.

First, set it into docker compose:
```
# Rewrite all publicly exposed ports to the available ones we found earlier
# e.g. '- "8000:8080"' => '- "8003:8080"'
sed -i "s/\- \"$PORT_BASE_WEB\:*/\- \"$PORT_WEB:/" $WORKSPACE/docker-compose.yml
sed -i "s/\- \"$PORT_BASE_SELENIUM\:*/\- \"$PORT_SELENIUM:/" $WORKSPACE/docker-compose.yml
sed -i "s/\- \"$PORT_BASE_ADMINER\:*/\- \"$PORT_ADMINER:/" $WORKSPACE/docker-compose.yml
sed -i "s/\- \"$PORT_BASE_REDIS_ADMIN\:*/\- \"$PORT_REDIS_ADMIN:/" $WORKSPACE/docker-compose.yml
sed -i "s/\- \"$PORT_BASE_LIVERELOAD_JAVASCRIPT\:*/\- \"$PORT_LIVERELOAD_JAVASCRIPT:/" $WORKSPACE/docker-compose.yml
```

Now we need to setup proxy for our job build. 
We need to set `$JOB_NAME` as part of the server name and proxy it into earlier defined container port.
Then we need to copy our earlier created nginx template into `/etc/nginx/conf.d/` folder and set proxy:
```
cp -f /var/jenkins-templates/nginx-template.conf /etc/nginx/conf.d/$JOB_NAME.conf

# Replace $JOB_NAME and $PORT_WEB in the nginx configuration
sed -i "s/{{JOB_NAME}}*/$JOB_NAME/" /etc/nginx/conf.d/$JOB_NAME.conf
sed -i "s/{{PORT_WEB}}*/$PORT_WEB/" /etc/nginx/conf.d/$JOB_NAME.conf
```

Now we need to reload nginx to load this new configurations, which we are able to do so by adding jenkins into sudoers earlier:

```
sudo nginx -s reload
```

#### Change mounting of postgres-data folder
We need to mount postgres data locally so we make data persistent, as default postgres data is mounted into project folder,
but this folder has not correct permission rights. We need to mount this folder into `/var` folder.

First, create postgres data folder using ssh in your machine:
```
mkdir /var/postgres-data
```

Then add this piece of line into `execute shell` text area in template configuration:
```
sed -i "s/\.\/project-base\/var\/postgres-data*/\/var\/postgres-data\/$JOB_NAME/" $WORKSPACE/docker-compose.yml
```

#### Handling container names
Now we got correctly set up ports for our branches, but the build would still fail because of duplicity of container names,
so lets take care of that.

Jenkins has some variables that we can use for this, for example, `$JOB_NAME` contains name of job, which is in our case
branch name, so we can use sed and put `$JOB_NAME` before every container name.
eg. `'container_name: shopsys-framework-webserver' => 'container_name: job-name-shopsys-framework-webserver'`

```
sed -i "s/container_name:\s*\b/container_name: $JOB_NAME-/" $WORKSPACE/docker-compose.yml
```

### Setting application for build
This section shows configuration of jenkins, which will allow build of application without interaction with user.

#### Create parameters.yml
Our `parameters.yml.dist` is already set for running application in docker as default so we just need to create `parameters.yml` file from dist file:
```
cp $WORKSPACE/project-base/app/config/parameters.yml.dist $WORKSPACE/project-base/app/config/parameters.yml
cp $WORKSPACE/project-base/app/config/parameters_test.yml.dist $WORKSPACE/project-base/app/config/parameters_test.yml
```

#### Set domains
Now we just create domain file, in this case, we use branch name for domain name, and we add domain number into beginning of URL,
that way domain names are related with the git branches, this makes jenkins more organized.
    
```
# Copy domains_urls.yml from the template
cp $WORKSPACE/project-base/app/config/domains_urls.yml.dist $WORKSPACE/project-base/app/config/domains_urls.yml

# Fetch all domain IDs
DOMAIN_IDS=$(cat $WORKSPACE/project-base/app/config/domains_urls.yml|grep -Po 'id: ([0-9]+)$'|sed 's/id: \([0-9]\+\)/\1/')

# Modify public URLs to $DOMAIN_ID.$JOB_NAME.your-server-name.com ($DOMAIN_ID is ommited for first domain)
for DOMAIN_ID in $DOMAIN_IDS; do
  if [ "$DOMAIN_ID" == "1" ]; then
    # 1st domain has URL without number prefix
    sed -i "/id: 1/,/url:/{s/url:.*/url: http:\/\/$JOB_NAME.your-server-name.com/}" $WORKSPACE/project-base/app/config/domains_urls.yml
  else
    # 2nd and subsequent domains have URLs with DOMAIN_ID prefix
    sed -i "/id: $DOMAIN_ID/,/url:/{s/url:.*/url: http:\/\/$DOMAIN_ID.$JOB_NAME.your-server-name.com/}" $WORKSPACE/project-base/app/config/domains_urls.yml
  fi
done
```

#### Create environment file
During composer install process there also needs to be created environment file.
We use our CI to test application in `PRODUCTION` mode but you can always change it to `DEVELOPMENT`. 
Development mode show symfony debug tool bar.

```
touch $WORKSPACE/PRODUCTION
```

#### Composer cache and token
We need to use composer for installation of application, but there are few pitfalls. Firstly, composer uses cache for already
downloaded packages, but this cache is worthless since it is saved only into container. This way all of newly started containers
will not have cache available and because of that, all builds will take way too long to build for no reason. 
Secondly, our application does not contain composer lock so your machine will execute too many requests onto packagist server
and composer will prompt you to generate token from github. 
Again, in default, you would need to set token for each instance individually.

You can prevent all of these problems and make your builds fast and efficient by mounting composer folder onto local computer,
so all containers can use one cache and use your set token.

Create temporary `docker-compose`:
```
cp $WORKSPACE/docker-compose.yml $WORKSPACE/docker-compose.yml.new
```

Use sed to insert new mounting volumes into php-fpm container using regular expression.
Sed does not handle `\n` well, so we change format of new lines using `tr` and change it back at the end
Output it into new docker-compose.yml:
```
cat $WORKSPACE/docker-compose.yml.new | tr '\n' '\r' | sed -r 's#(php-fpm:(\r[^\r]+)+volumes:)(\s+- )#\1\3~/.composer:/home/www-data/.composer\3#' | tr '\r' '\n' >> $WORKSPACE/docker-compose.yml
```

Delete temporary `docker-compose.yml.new`:
```
rm $WORKSPACE/docker-compose.yml.new
```

### Build the application in containers
Since we use mounted volumes, we need to make sure that UID and GID of the user running Jenkins and the user running inside the container match.
Otherwise, the user running Jenkins would be unable to change or remove files created in the container and vice versa.

We can use build arguments `www_data_uid` and `www_data_gid` to match the ids before we build the containers.
To change the argument we will use `sed` again.
```
# Match UID and GID of user in host machine with the user "www-data" in php-fpm container
sed -i "s/www_data_uid: 1000/www_data_uid: $(id -u)/" $WORKSPACE/docker-compose.yml
sed -i "s/www_data_gid: 1000/www_data_gid: $(id -g)/" $WORKSPACE/docker-compose.yml
```

Now we can build our images and create containers:

```
/usr/local/bin/docker-compose build
/usr/local/bin/docker-compose up --force-recreate -d
```

Install the application:
```
/usr/bin/docker exec $JOB_NAME-shopsys-framework-php-fpm composer install -o
/usr/bin/docker exec $JOB_NAME-shopsys-framework-php-fpm php phing db-create test-db-create build-demo-ci
```

Our template is done, now we just need to create actual jobs from this template.

### Special Jobs
These jobs makes jenkins more automatic and helps with keeping jenkins clean and updated.

These jobs can automatically create jobs using git branches, or properly delete them if they does not exist anymore.

#### Jenkins autojobs
This special job ensures automatic updating of jobs in Jenkins according to the state of the git repository. 
Job uses a template, that is created in the previous section of this cookbook.

As first we need to configure the `autojobs-config.yml`, which contains configuration for `Jenkins autojobs`.

Create `autojobs-config.yml` file into `/var/jenkins-templates/`:

```
touch /var/jenkins-templates/autojobs-config.yml 
```

Great, now we need to open this file in some editor to be able to write into it:

```
jenkins: 'http://your-server-name.com:8080' 
username: None
password: None
```

These credentials are used to access your jenkins address, you can set username and password if your jenkins is secured.
In our solution, we use jenkins only internally and hidden behind VPN so we dont need to have it secured.

```
repo: 'git@github.com:your/repository.git'
```

Fill your credentials to your project repository.
 
```
template: 'template'
``` 
 
This defines where tool can find template for creation of new jobs, earlier we created our template and named it `template` so now we use it.

This will copy configuration of `template` into every newly created job.

```
overwrite: true
```

This option controls state of `template` file, if it gets modified, it will copy `template` configuration into every already created job
that has old configuration.

```
build-on-create: true
enable: true
```

Keep it on true.
`build-on-create` makes sure to trigger a build when the job is created.

`enable` option just keeps jobs enabled so they can be built.

``` 
ignore:
  - 'refs/pull/.*'
```
These configuration can filter branches that you dont want to build, for example, github creates branch for every pull request that is created,
this is great, but we don't need every created pull request to be build in jenkins.

Using `ignore` parameter we can tell that we don't want them to be build.

```
cleanup: true
```

If `cleanup` parameter is set to true. Jenkins autojobs deletes jobs without any related branch in git repository.

Now we got our jenkins-autojobs configured, lets use it.

First, we need to save this file into some folder where jenkins has access.
Lets use old good `/var/jenkins-templates` which we know that jenkins has access to.

Create new job in Jenkins administration.
In configuration of job use option `Build periodically` in `Build triggers` and set it to for example `H/5 * * * *`.
This defines how often we want to start this job, `H/5` defines that we want it every 5 minutes.

The configuration of job is pretty simple:

```
jenkins-makejobs-git /var/jenkins-templates/autojobs-config.yml
```

This just use jenkins-autojobs tool with specified `autojobs-config`.

#### Clear old workspaces
Okay, so `cleanup` parameter is useful, but it does not do all the work for us. It doesn't delete workspaces of branches,
and it cannot delete docker containers for us.

To properly delete jobs that does not have any usage, we must create new tool.

Create new job, name it for example `clear-old-workspaces` and set `Poll SCM` parameter in `Build triggers` section. 
We recommend to use this tool only at night, deleting of workspaces and containers can be
pretty difficult operation so you don't want it to run every 5 minutes, just because you don't usually delete branches so often.

The configuration of this job is this:
```
#!/bin/sh

WORKSPACES=$(ls $JENKINS_HOME/workspace/);
JOBS=$(ls $JENKINS_HOME/jobs/);

for WORKSPACE in $WORKSPACES; do
  DELETE=1;
  for JOB in $JOBS; do
    if [[ $JOB == $WORKSPACE ]]; then
      DELETE=0;
    fi
  done


  if [[ $DELETE -eq 1 ]]; then
    echo "Stopping containers, removing nginx configuration and deleting workspace: $WORKSPACE"

    cd $JENKINS_HOME/workspace/$WORKSPACE
    /usr/local/bin/docker-compose down
    
    rm -f /etc/nginx/conf.d/$WORKSPACE.conf
    
    sudo /opt/bin/wipe_workspace.sh $WORKSPACE
  fi
done
``` 
This script delete each workspace, for which is already missing appropriate job (old job is removed by another process). 
At the same time, containers and volumes associated with removed workspaces are removed.

Script also deletes nginx configuration file and use `wipe_workspace.sh` to delete directories.
File looks like this:

```
echo "Wiping workspace \"$1\"..."
rm -rf /var/lib/jenkins/workspace/$1
rm -rf /var/postgres-data/$1
```

Create this file into `/opt/bin` directory.

Make the file executable:

```
chmod 700 /opt/bin/wipe_workspace.sh
```

This shell script needs to be run as root, because of permission problems in php-fpm container.
We need to edit `/etc/sudoers` file again, add this line into it:

```
jenkins ALL=(ALL) NOPASSWD: /opt/bin/wipe_workspace.sh
```

Done, our special jobs are created and jenkins should be ready to run.

Now, we need to make few things on our machine to make things really work. Log through ssh into your machine.

First of all, we need to start `docker daemon socket` which needs to be done manually as root once on a machine:
```
sudo dockerd
```

Second thing is that we need to set our composer token. Go to jenkins administration and run `Jenkins autojobs` tool, that will
start first builds which will fail, on `composer install -o` and it will prompt you to start it manually.

So select one of your jobs names and get into `container` in ssh. For example i will use master, but it does not really matter 
which job will you choose:

```
docker exec -it master-shopsys-framework-php-fpm composer install -o
```

This will prompt you to set `composer token` in github, click on a link and set it. Now we got it set up for all branches,
since our composer folder is mounted on localhost.

### Done
Now just start `Jenkins autojobs` tool again.

## Troubleshooting
There are some limitations in number of networks that Docker can create. 
Basically, you can encounter some problems if you have bigger amount of branches on one machine. Solution of this problems is out of scope of this tutorial.

Please keep in mind that this is hardware heavy configuration, we recommend you to use at least 64GB of RAM and at least few hundreds GB of disk memory.
If you decided to use this solution, please make sure that you create job to clean old workspaces. Remember that every container is 
something like operating system and if you underestimate cleaning of containers and workspaces, it could fill your disk memory really quick.

To be sure about cleaning more of unnecessary docker files, you can create job that would just execute `docker system prune`
sometimes, this command clears unnecessary docker files.

Some of the issues can be overcome via [Continuous Integration Using Kubernetes](/docs/kubernetes/continuous-integration-using-kubernetes.md).

## Conclusion
We just did pretty big job, we just configured jenkins that can automatically create jobs by git branches,
we are able to configure this jobs using `template`, that can build instances of your project in docker.

We created special job that can clean all jobs if they are useless.
