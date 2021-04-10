# Gokada parcel delevery mockup

This is an implementation of the fullstack task provided by gokada and implemented by [dino16m](https://github.com/dino16m)

This app contains an api backend written in laravel and a font end written in vuejs:


## Installation

### Backend

 - The `.env.example` file in the home directory of the project should be copied into a file named `.env` 
- The backend requires an apiKey from a google maps project to run and this should be filled in the `GOOGLE_MAPS_KEY` variable in the env file
- Database migration is run as `php artisan migrate` on the shell
- the urls for the api are in the `routes/api.php` file

### Frontend
- The frontend exists in the main home directory under the file `gokada-client`
- A .env file exists in the `gokada-client` directory where you set information like the backend url and the location tolerance in metres
- The front end client is installed by executing `npm install`
- The front end development build is run by executing `npm run serve`
- The front end can be build for distribution by running `npm run build`
