# QWEN.md - Kallpaq Project

## Información General del Proyecto

Kallpaq es un sistema de administración web desarrollado con Laravel, un framework PHP. El sistema implementa el Sistema de Gestión ISO 9001 e ISO 37001, permitiendo gestionar componentes clave del sistema de gestión de calidad y antisoborno, promoviendo la mejora continua, el control documental, la trazabilidad, y la gestión de riesgos y obligaciones.

Nombre del sistema: Kallpaq (posiblemente relacionado con "kallpa" que significa almacén/bodega en quechua, en el contexto de gestión de inventarios/documentos)

### Módulos del sistema
- Documentación por Proceso
- Gestión de Requerimientos
- Gestión por Procesos
- Gestión de la Mejora
- Gestión de Obligaciones
- Gestión de Riesgos
- Administración (Gestión de Usuarios, Facilitadores y Parámetros)

### Tecnologías y Arquitectura
- **Framework Backend:** Laravel 10 (PHP 8.1+)
- **Frontend:** Vue.js 3, Vite, Bootstrap, AdminLTE3
- **Gestión de estado (Vue):** Pinia
- **Enrutamiento (Vue):** Vue Router
- **UI Components:** PrimeVue (especialmente para DataTables)
- **Autenticación y Autorización:** Laravel Auth, Spatie/laravel-permission
- **Generación de PDF:** barryvdh/laravel-dompdf
- **Enrutamiento JS:** tightenco/ziggy
- **ORM:** Eloquent (Migrations, Seeders)

Además de tecnologías mencionadas en el documento original:
- Chart.js (para visualización de datos)
- MySQL (base de datos)

## Instalación y Ejecución

### Requisitos
- PHP 8.1+
- Composer
- Node.js y npm
- MySQL

### Instrucciones de instalación
1. Clonar el repositorio
2. Ejecutar `composer install` para instalar dependencias de PHP
3. Ejecutar `npm install` para instalar dependencias de JavaScript
4. Crear una base de datos para el proyecto
5. Copiar `.env.example` a `.env` y configurar los datos de la base de datos
6. Ejecutar `php artisan key:generate` para generar una clave de aplicación
7. Ejecutar `php artisan migrate` para ejecutar migraciones de base de datos
8. Ejecutar `php artisan db:seed` si necesita poblar la base de datos con datos iniciales
9. Ejecutar `npm run dev` para desarrollo o `npm run build` para construir los activos en producción
10. Utilizar `php artisan serve` para iniciar el servidor de desarrollo de Laravel

### Comandos de desarrollo
- `php artisan serve` - Iniciar servidor de desarrollo
- `npm run dev` - Iniciar servidor de desarrollo de Vite
- `npm run build` - Construir activos para producción
- `php artisan migrate` - Ejecutar migraciones de base de datos
- `php artisan db:seed` - Poblar la base de datos
- `php artisan make:controller <nombre>` - Crear un nuevo controlador
- `php artisan make:model <nombre>` - Crear un nuevo modelo
- `php artisan make:migration <nombre>` - Crear una nueva migración

### Pruebas
- Ejecutar `php artisan test` o `./vendor/bin/phpunit` para ejecutar las pruebas

## Estructura de Directorios
```
D:\Kallpaq\
├── app/                  # Archivos principales de la aplicación
│   ├── Console/          # Comandos de consola
│   ├── Enums/            # Enumeraciones
│   ├── Exceptions/       # Manejo de excepciones
│   ├── Helpers/          # Funciones de ayuda
│   ├── Http/             # Controladores, middleware, etc.
│   ├── Livewire/         # Componentes Livewire
│   ├── Models/           # Modelos de Eloquent
│   ├── Notifications/    # Notificaciones
│   ├── Observers/        # Observadores de modelos
│   ├── Providers/        # Proveedores de servicios
│   ├── Services/         # Lógica de negocio
│   └── View/             # Componentes y vistas
├── bootstrap/            # Archivos de inicialización del framework
├── certificados/         # Certificados
├── config/               # Archivos de configuración
├── database/             # Migraciones, semillas y factories
│   ├── factories/        # Factories para modelos
│   ├── migrations/       # Migraciones de base de datos
│   └── seeders/          # Seeders para datos iniciales
├── documentacion/        # Documentación del proyecto
├── lang/                 # Archivos de traducción
├── public/               # Archivos accesibles web
├── resources/            # Vistas, activos y archivos de idioma
│   ├── css/              # Archivos CSS
│   ├── js/               # Archivos JavaScript/Vue
│   ├── sass/             # Archivos SASS
│   └── views/            # Vistas Blade
├── routes/               # Definiciones de rutas (web.php, api.php, auth.php)
├── scriptbd/             # Scripts de base de datos
├── scripts/              # Scripts auxiliares
├── storage/              # Plantillas compiladas, logs y subidas de archivos
├── tests/                # Archivos de pruebas
├── .editorconfig         # Configuración de editor
├── .env.example          # Ejemplo de archivo de entorno
├── .gitattributes        # Atributos de Git
├── .gitignore            # Archivos excluidos por Git
├── artisan               # Herramienta CLI de Laravel
├── build_error_2.log     # Registro de errores de compilación
├── build_error_3.log     # Registro de errores de compilación
├── build_error.log       # Registro de errores de compilación
├── build_log.txt         # Registro de compilación
├── commit_message.txt    # Mensaje de commit
├── composer.json         # Dependencias PHP
├── composer.lock         # Versiones exactas de dependencias PHP
├── debug_full.html       # Archivo de debug HTML
├── debug_output.txt      # Archivo de debug texto
├── Funcionalidades.docx  # Documento de funcionalidades
├── Funcionalidades.pdf   # PDF de funcionalidades
├── GEMINI.md             # Documento de GEMINI
├── implementation_plan.md # Plan de implementación
├── package-lock.json     # Versiones exactas de dependencias JavaScript
├── package.json          # Dependencias JavaScript
├── phpunit.xml           # Configuración de pruebas phpunit
├── postcss.config.js     # Configuración de PostCSS
├── QWEN.md               # Documento actual (este archivo)
├── README.md             # Documento de inicio
├── tailwind.config.js    # Configuración de Tailwind CSS
├── vite.config.js        # Configuración de Vite
└── webpack.mix.cjs       # Configuración de Webpack Mix
```

## Rutas del Sistema
Las rutas del sistema están definidas en el directorio `routes/`:
- `web.php`: Rutas para la interfaz web y acceso autenticado (requerimientos, hallazgos, documentos, etc.)
- `api.php`: Rutas para la API REST con autenticación Sanctum
- `auth.php`: Rutas relacionadas con autenticación

## Convenciones de Desarrollo

El proyecto sigue las convenciones y estándares de codificación de Laravel:

- Los controladores deben estar en `app/Http/Controllers/`
- Los modelos deben estar en `app/` o `app/Models/`
- Las vistas están en `resources/views/`
- Las rutas se definen en el directorio `routes/`
- Los archivos de configuración están en el directorio `config/`
- Las migraciones de base de datos están en `database/migrations/`
- Los activos frontend se gestionan a través de Vite y están ubicados en `resources/js/` y `resources/css/`

El proyecto utiliza Vite para la construcción de activos y AdminLTE3 con Bootstrap para el estilo. La gestión de estado se realiza con Pinia y se utilizan componentes Vue.js para la interactividad. PrimeVue se usa principalmente para DataTables y otros componentes UI.

## Notas Especiales

- El proyecto incluye Laravel Sanctum para autenticación API y Laravel UI para autenticación web tradicional
- Utiliza spatie/laravel-permission para permisos basados en roles
- La generación de PDF se maneja con laravel-dompdf
- El proyecto utiliza principalmente Vue.js con Pinia para el desarrollo frontend, reemplazando Livewire
- El sistema gestiona ISO 9001 e ISO 37001 según información en GEMINI.md
- Las consultas con el agente Qwen deben realizarse siempre en español
- Las rutas se trabajan principalmente en web.php y no en la API
- Debes dirigirte conmigo en español