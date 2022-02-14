# MailUp Backend Challenge

## Configuración

En el directorio raíz crear un archivo con nombre `.env` con el mismo contenido que el archivo `.env.example`.

En `.env` además de `APP_URL` y `DB_*` setear la URL base para la documentación de la API (la misma que para `APP_URL`):

    L5_SWAGGER_BASE_PATH={URL_BASE}

Los subdirectorios dentro del directorio `/storage` deben tener permisos de escritura.

Luego desde la consola de comandos ejecutar en este orden:

    composer install
    php artisan key:generate
    php artisan migrate --seed
    php artisan l5-swagger:generate

### Ejercicio 1: Construir una API REST

La documentación de la API se puede consultar ingresando desde un navegador a `URL_BASE/api/documentation`.

### Ejercicio 2: Consumir una API REST

Ingresando desde un navegador web a la URL principal del proyecto se accede a una pantalla con un botón `Importar fotos`, al hacer click en el mismo
se recuperan los registros de `https://jsonplaceholder.typicode.com/photos` y se almacenan en la tabla `photos` de la base de datos.
