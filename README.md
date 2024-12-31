


<p align="center">
    <a href="https://github.com/MoropM/createLicenses" target="_blank"><img src="https://raw.githubusercontent.com/MoropM/createLicenses/refs/heads/master/generate_licenses.webp" width="400"></a>
</p>

## Generador de licencias

Proyecto creado para generar licencias de uso de dominios personales.
Genera una licencia (clave encriptada de 25 caracteres) y un token de licencia a partir de la fecha de inicio y la fecha de finalización, enlazado a un dominio personal.
Este proyecto es solo de ejemplo, no es una herramienta oficial o recomendada para el uso en producción. 

El proyecto esta generado con [Laravel](https://laravel.com)

## Intalar

- Clone el proyecto con:
```bash	
git clone https://github.com/MoropM/createLicenses.git
```
- Instalar dependencias con:
```bash
composer install
npm install

# Compilar el proyecto
npm run prod  o  npm run dev
```
- Crear el archivo de configuración de Laravel con:
```bash
cp .env.example .env
```
- Editar el archivo de configuración de Laravel con:
```bash
nano .env

#Cree las siguientes claves en el archivo .env
PUB_KEY="Agregue una clave pública aquí"
PRV_KEY="Agregue una clave privada aquí"
```
- Ejecutar el servidor de Laravel con:
```bash
php artisan serve
```
- Abrir el navegador en http://127.0.0.1:8000/

## Licencia

Proyecto de código abierto bajo la licencia [MIT license](https://opensource.org/licenses/MIT). 
