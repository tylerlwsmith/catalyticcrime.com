# CatalyticCrime.com

**Note: I time-boxed development & deployment of an MVP for this project to just over two weeks, then I almost immediately abandoned it because I couldn't source crime data without violating a website's terms of service (more on that below). Now I'm open-sourcing this project in more-or-less the condition it was when I deployed it, warts and all. This repo does not include the original development commit history: it is a snapshot of the deployed project. The only _major_ change is the project now includes this README file, which was written at the time of releasing the code as open source.**

---

This project was built over the course of two weeks as a proof-of-concept for tracking catalytic converter crimes in Bakersfield, CA. I follow the Kern County Scanner Club Facebook group, and one of the most commonly reported crimes on the page is catalytic converter theft. These posts are often accompanied by pictures or videos of the crimes. The idea behind this project was creating a centralized platform where users could submit public reports about the crimes and attach pictures & videos they may have of the incident. Data from user-submitted reports would be combined with police department data so the site could show various insights about catalytic converter crime in Bakersfield.

In the two weeks that I worked on this project, I completed the following:

- I containerized the app with separate containers for the PHP web app, the nginx web server, the Node.js dev server, the PostgreSQL database, a testing PostgreSQL database, and MailHog for checking emails in development.
- I set up auth and account creation with Laravel Breeze.
- I configured React and TypeScript, through I ended up swapping out React for Laravel Livewire.
- I set up a form for users to create and edit reports using Laravel Livewire that provides server-powered reactivity on the front-end.
- I set up an admin-approval process for new reports, where admins are notified of new reports via email then can approve them using the web app.
- I created a paginated reports index page for reports and single report page.
- I created a sparce-but-functional UI using Tailwind CSS and its form and typography plugins.
- I set up DigitalOcean Spaces for S3-compatible object storage and used it for storing user uploads.
- I set up CI/CD with Jenkins deploying to a $5/mo DigitalOcean droplet via Docker Compose (_which is currently broken since I moved the repo when I open-sourced this_).

You can see the site running in production (currently without user submitted reports) at [catalyticcrime.com](https://catalyticcrime.com).

## Wonkiness with PHP container

To keep the web app container fast, I've installed Composer's dependencies in `/srv/vendor` (which is not mounted to the host machine during development). This way the container doesn't need to make so many round trips between Docker Desktop's Linux VM and the host for development. This adds some complications when adding new Composer dependencies, but you can read more about that process below. You can also read [my full blog post about this setup on dev.to](https://dev.to/tylerlwsmith/speed-up-laravel-in-docker-by-moving-vendor-directory-19b9).

## Running locally

To run locally, clone the project then `cd` into the project directory. Run the following commands:

```sh
cp .env.example .env
docker-compose build
docker-compose run --rm -e COMPOSER_VENDOR_DIR=/srv/app/vendor webapp composer install
docker-compose run --rm webapp php artisan migrate
docker-compose run --rm devserver npm install
```

Next, run the following command and copy its output from the console into the project's root `.env` file as the value for `APP_KEY`.

```sh
docker-compose run --rm webapp php artisan key:generate --show
```

With this initial set up complete, you can bring up the app with the following command:

```sh
docker-compose up
```

With the app now running, you'll need to create your first admin user. The easiest way to do this is to create a user with the site's standard "Sign Up" form _immediately_ after starting the app for the first time, then use Tinker to edit the account permissions.

After creating an account through the front-end, run the following command:

```sh
docker-compose exec webapp php artisan tinker
```

In the Tinker shell, run the following commands:

```php
$user = User::first();
$user->is_admin = true;
$user->save();
```

Finally, you can visit the website at http://0.0.0.0:8080.

## Troubleshooting the local installation

If you're having a **permissions issue** with the `laravel.log` file or the `cache` directory, manually set the permission in the running container (it seems to persist through rebuilds and restarts). The `Dockerfile` _should_ handle the permissions, but it didn't work when I installed on one of my laptops. _This needs a better fix but I'm no longer working on this project._ 

```sh
docker-compose exec webapp chown -R www-data:www-data /srv/app/storage
docker-compose exec webapp chown -R www-data:www-data /srv/app/bootstrap
```

If you see `@livewireStyles` or `@livewireScripts` rendering as strings in the browser, make an edit to `layout.blade.php`, save, refresh the browser, undo the change and save again. That will clear the blade cache. It's a hacky solution, but it works.

## Installing new dependencies

To install a new **Node package**, run the following command while Docker is running:

```
docker-compose exec devserver npm install [package-name]
```

Installing a new **Composer package** is a little more involved. The PHP container is a little wonky because of the non-standard `vendor/` directory location, as described above.

To install a new dependency, you'll need to go through multiple steps while Docker is running:

```sh
# Install in the container first
docker-compose exec webapp composer require [package-name]

# Then install in the directory shared with your host
docker-compose exec -e COMPOSER_VENDOR_DIR=/srv/app/vendor webapp composer require [package-name]
```

At this point, it's probably a good idea to bring down your containers, rebuild them, and start Docker again.

```sh
docker-compose down
docker-compose build
docker-compose up
```

## CI/CD Pipeline

**Because I put this in a new repository when I open sourced it, the pipeline is currently not operational. I'm probably not going to fix it since I'm no longer working on this project.**

The CI/CD pipeline was built with Jenkins, and relies on the [Email Extension](https://plugins.jenkins.io/email-ext/) plugin. Out of an abundance of me trying to finish the project in the time that I alloted, I also depend on `.env` and `.env.production` files being on the Jenkins server at `${env.JENKINS_HOME}/env_files/catalyticcrime.com/`. This isn't a great practice, and if I had continued developing this project I would swap out the whole pipeline with GitHub actions.

## Data acquisition

To get this project to deliver value without first building a large user base that was actively submitting reports, I had hoped to seed the site with publicly available catalytic converter crime data. This ended up being a fruitless endeavor.

### CrimeMapping.com

CrimeMapping.com has [Bakersfield catalytic converter theft data](https://www.crimemapping.com/map/agency/19) readily available on its website, but there's no export feature. Scraping the ["print" page](https://www.crimemapping.com/Print?dteFrom=10-1-2021&dteTo=10-31-2021&attr=[%2214%22]&ext={%22type%22:%22extent%22,%22xmin%22:-13307636.710159209,%22ymin%22:4189123.318966664,%22xmax%22:-13190993.804996189,%22ymax%22:4240183.253861093,%22spatialReference%22:{%22wkid%22:102100},%22cache%22:{%22_parts%22:[{%22extent%22:{%22type%22:%22extent%22,%22xmin%22:-13307636.710159209,%22ymin%22:4189123.318966664,%22xmax%22:-13190993.804996189,%22ymax%22:4240183.253861093,%22spatialReference%22:{%22wkid%22:102100}},%22frameIds%22:[0]}]}}&tmpfilt={%22PreviousID%22:%224%22,%22PreviousNumDays%22:28,%22PreviousName%22:%22Previous%204%20Weeks%22,%22FilterType%22:%22Previous%22,%22ExplicitStartDate%22:%2220211004%22,%22ExplicitEndDate%22:%2220211031%22}&agfilt=[]&bmpid=1&disacpt=false) using a tool like Puppeteer or Selenium _looks_ like it's possible, but [the site's terms of service](https://www.crimemapping.com/Home/TermsAndConditions) explicitly prohibit scraping. At the time of writing, the "print" page requires a user to interact with the map before it will display any meanigful data, which feels like it was added with the intention of preventing automatic scraping.

Crime Mapping's [about page](https://www.crimemapping.com/about) states, "Our goal is to assist police departments in reducing crime through a better-informed citizenry." I'm suspicious of this claim. The aggregate data is not available to researchers, data scientists, sociologists or journalists to perform comprehensive analysis on in any meaningful way. Instead, the data is only viewable through a clunky UI with primitive filters that will display no more than 1,000 results at a time.

Does CrimeMapping.com reduce crime through a better informed citizenry? It feels unlikely. Is the Crime Mapping software sold to well-intentioned cities that are trying to do the right thing for a substantial amount of money? It feels likely.

### Freedom of Information Act request

Next, I tried to get catalytic converter theft data directly from the Bakersfield Police Department. I sent an email to a community relations specialist at the BPD, who was incredibly helpful and opened a Freedom of Information Act (FOIA) request on my behalf. The city's FOIA representative was friendly, and a few weeks after my request was opened I was provided an Excel file with dates of the incidents and their incident IDs. However, the data I was provided did not have any kind of geographical data like ZIP, nor did it contain vehicle types.

Without this data–and with the multi-week turnaround time for FOIA requests–I decided that it would be prohibitively difficult to provide value on the site without dedicating an enormous amount of time and energy to building a userbase, so I stopped development of the project.

### Vehicle make/model/year data

I'm deriving the make and model list from [fueleconomy.gov's data](https://www.fueleconomy.gov/feg/download.shtml) based on a [recommendation from Reddit](https://www.reddit.com/r/datasets/comments/57t2xj/vehicle_make_model_specification_dataset/).

## Why did I build this?

My brother's catalytic converter was stolen in August, 2021 in the midst of a global parts shortage. This left my brother without a usable car for over a month. Between this incident and seeing how common this catalytic converter theft was online, I built this out of frustration hoping that it could help.
