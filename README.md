# Los Pokemon más poderosos.

Visualización de los Pokemón más poderosos.

## Requisitos Previos

- MySQL
- PHP
- React
- Apache

## Preparación del Backend

1. Crea la base de datos (se recomienda cotejamiento utf8_bin) con el nombre que desee (se recomienda pokemon).
2. En `backend/database` se encuentra el archivo `structure.sql` importalo o ejecuta su contenido en tu gestor de base de datos.
3. En `backend/conexion.php` encontrarás la clase de nombre Database, modifica las credenciales a como las requieras para una conexión exitosa.
4. Abre tu navegador y visita `http://localhost/nombre/backend/importAPI.php` para importar los datos de `https://pokeapi.co/` a la base de datos recientemente creada y configurada. Esto puede tardar 7-10 minutos aproximadamente, en caso de que por motivos externos dicha importación tarde más puede configurar `set_time_limit(?)` en `backend/importAPI.php`.
5. En caso de que el punto anterior no haya sido posible de completar por motivos externos, en backend\database se encuentra un comprimido `data.zip` el cual contiene la información obtenida de la Pokeapi con anterioridad, importala desde tu gestor de base de datos (omitiendo la revisión de claves foraneas para evitar interrupciones).

## Preparación del Frontend

1. En `frontend/src/constants.js` se encuentra la variable API_URL, modifica la URL con la ruta en la cual has clonado este repositorio, por ejemplo `http://localhost/nombre/backend/api.php`.

## Uso

1. Ingresa desde la consola a la carpeta frontend y ejecuta: `npm run dev`, esta te retornará una URL como `http://localhost:5173/`.
2. Abre tu navegador y visita la URL retornada.
