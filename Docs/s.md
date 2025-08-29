server {
        root /var/www/indirimgo/public;
        index index.php index.html index.htm index.nginx-debian.html;
        server_name indirimgo.tr www.indirimgo.tr;

        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Content-Type-Options "nosniff";
        add_header X-XSS-Protection "1; mode=block";

        charset utf-8;

        location / {
                try_files $uri $uri/ /index.php?$query_string;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }

        error_page 404 /index.php;


        location ~ \.php$ {
                fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
                fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
                include fastcgi_params;
        }




    location ~ /\.(?!well-known).* {
                 deny all;
        }




    listen 443 ssl; # managed by Certbot
    ssl_certificate /etc/letsencrypt/live/indirimgo.tr/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/indirimgo.tr/privkey.pem; # managed by Certbot
    include /etc/letsencrypt/options-ssl-nginx.conf; # managed by Certbot
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem; # managed by Certbot


}
server {
if ($host = www.indirimgo.tr) {
return 301 https://$host$request_uri;
} # managed by Certbot


    if ($host = indirimgo.tr) {[New Text Document.txt](../../../../../Users/karma/OneDrive/Desktop/New%20Text%20Document.txt)
        return 301 https://$host$request_uri;
    } # managed by Certbot


        server_name indirimgo.tr www.indirimgo.tr;
    listen 80;
    return 404; # managed by Certbot




}
