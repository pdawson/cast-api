# Cast
## Configuration Manager for NGINX

> NOTE: This project is heavily WIP and not yet released

### Installation

The server where cast is hosted is generally setup in the same way as a regular Laravel application.

The servers that are to be managed by cast do need some additional setup (the `cast` user; acts as a bridge between the API and the server itself).

#### Setup the API

Installation of the API is the same as any other laravel application.

```shell script
cd /var/www
git clone <this repo> cast
cd cast

composer install
php artisan key:generate

cp .env.example .env
vi .env

php artisan migrate
php artisan db:seed
```

#### Server Setup

##### Setting up the Cast User

Create a new user on the server called `cast` (configurable in `CAST_USER` environment property).
```shell script
sudo adduser cast
sudo usermod -aG www-data cast
sudo chfn -o umask=022 cast
```

Ensure the `www-data` group can edit files in `/etc/nginx`
```shell script
sudo chmod g+s /etc/nginx
sudo setfacl -Rdm group:www-data:rwx /etc/nginx
```

Enable the cast user special sudo privileges (to restart/reload nginx, test nginx configuration and run certbot for hosts).
```shell script
sudo visudo
```

Add the following under 'User privilege specification'. (The NGINX / Certbot paths may need updating depending on your system configuration).
```shell script
cast    ALL=NOPASSWD: /bin/systemctl restart nginx.service
cast    ALL=NOPASSWD: /bin/systemctl reload nginx.service
cast    ALL=NOPASSWD: /usr/sbin/nginx
cast    ALL=NOPASSWD: /usr/bin/certbot
```

---

Update the `CAST_PRIVATE_KEY` environment property to the location on the server (where cast is hosted) that contains the ssh key file.

> For the initial setup of cast; create this key in a location on the server with `ssh-keygen -t rsa`. This will be added to all servers that cast manages and allows login for the cast user

---

Add the public key (from the key created for cast above) to the authorized keys file on the server

```shell script
vi ~/.ssh/authorized_keys
ssh-rsa {{YOUR_KEY}} cast.host.local
```

This allows cast to login as the cast user through SSH.

#### Setup the Nuxt Frontend

The Nuxt Frontend is in a separate repository; installation instructions for that can be found there.

[GitHub Repository](https://github.com/pdawson/cast-frontend)
