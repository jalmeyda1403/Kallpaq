# 🧩 Sistema de Administración del Sistema de Gestión ISO 9001 e ISO 37001

Este es un **software web desarrollado en Laravel 10** con **PHP 8.1+**, diseñado para implementar la **Administración del Sistema de Gestión ISO 9001 e ISO 37001** dentro de una organización.  
El sistema permite gestionar los componentes clave del sistema de gestión de calidad y antisoborno, promoviendo la mejora continua, el control documental, la trazabilidad, y la gestión de riesgos y obligaciones.

---

## ⚙️ Tecnologías y Arquitectura

El sistema está construido con una arquitectura moderna que combina:

- **Framework Backend:** Laravel 10 (PHP 8.1+)
- **Frontend:** Vue.js 3, Vite, Bootstrap, TailwindCSS, AdminLTE
- **Componentes dinámicos:** Livewire 3
- **Gestión de estado (Vue):** Pinia
- **Enrutamiento (Vue):** Vue Router
- **Autenticación y Autorización:** Laravel Auth, Spatie/laravel-permission
- **PDF Generation:** barryvdh/laravel-dompdf
- **Routing JS:** tightenco/ziggy
- **ORM:** Eloquent (Migrations, Seeders)

---

## 🧭 Módulos del Sistema

El software consta de los siguientes módulos principales:

### 1. Documentación por Proceso
Acceso libre para consulta de la documentación del sistema (mapa de procesos, procedimientos, instructivos, formatos, entre otros).

### 2. Gestión de Requerimientos
Permite registrar, asignar, evaluar y dar seguimiento a requerimientos internos.  
**Roles involucrados:** Administrador, Especialistas, Propietarios de procesos y Facilitadores.

### 3. Gestión por Procesos
Administra la caracterización y seguimiento de los procesos institucionales.  
**Roles:** Administrador y Especialistas.

### 4. Gestión de la Mejora
Gestiona no conformidades, acciones correctivas, oportunidades de mejora y propuestas internas.  
**Roles:** Administrador, Especialistas, Propietarios de procesos y Facilitadores.

### 5. Gestión de Obligaciones
Permite el registro, evaluación y seguimiento del cumplimiento de obligaciones normativas y contractuales.

### 6. Gestión de Riesgos
Administra la identificación, evaluación, tratamiento y monitoreo de riesgos asociados a los procesos institucionales.

### 7. Administración
Incluye las siguientes funcionalidades:
- **Gestión de Usuarios:** creación, asignación de roles y control de accesos.  
- **Gestión de Facilitadores:** administración de usuarios con rol de apoyo técnico o metodológico.  
- **Parámetros:** configuración general del sistema (periodos, tipos, niveles, umbrales, etc.).

---

## 🚀 Instalación y Ejecución

### 🔹 Backend (PHP/Laravel)

1. **Instalar dependencias de PHP:**
   ```bash
   composer install

---

## 📁 Estructura del Proyecto

```
D:\Kallpaq\
├───.editorconfig
├───.env.example
├───.gitattributes
├───.gitignore
├───artisan
├───commit_message.txt
├───composer.json
├───composer.lock
├───Funcionalidades.docx
├───Funcionalidades.pdf
├───GEMINI.md
├───package-lock.json
├───package.json
├───phpunit.xml
├───postcss.config.js
├───README.md
├───tailwind.config.js
├───vite.config.js
├───webpack.mix.cjs
├───.git\...
├───app\
│   ├───Console\
│   │   ├───Kernel.php
│   │   └───Commands\
│   ├───Enums\
│   │   ├───EstadoDocumento.php
│   │   └───SistemasGestion.php
│   ├───Exceptions\
│   │   └───Handler.php
│   ├───Helpers\
│   │   ├───RequerimientoHelper.php
│   │   └───SemaforoHelper.php
│   ├───Http\
│   │   ├───Kernel.php
│   │   ├───Controllers\
│   │   ├───Middleware\
│   │   └───Requests\
│   ├───Models\
│   │   ├───Accion.php
│   │   ├───AccionMovimientos.php
│   │   ├───AreaCompliance.php
│   │   ├───Auditor.php
│   │   ├───Causa.php
│   │   ├───Configuracion.php
│   │   ├───ContextoAnalisis.php
│   │   ├───ContextoDeterminacion.php
│   │   ├───ContextoExterno.php
│   │   ├───ContextoInterno.php
│   │   ├───DiagramaContexto.php
│   │   ├───Documento.php
│   │   ├───DocumentoAlerta.php
│   │   ├───DocumentoDependencia.php
│   │   ├───DocumentoMovimiento.php
│   │   ├───DocumentoRelacionado.php
│   │   ├───DocumentoVersion.php
│   │   ├───Especialista.php
│   │   ├───Expectativa.php
│   │   ├───Factor.php
│   │   ├───Hallazgo.php
│   │   ├───HallazgoEvaluacion.php
│   │   ├───HallazgoMovimientos.php
│   │   ├───HallazgoProceso.php
│   │   ├───Indicador.php
│   │   ├───IndicadorHistorico.php
│   │   ├───IndicadorSeguimiento.php
│   │   ├───Inventario.php
│   │   ├───Obligacion.php
│   │   ├───OUO.php
│   │   ├───OuoUser.php
│   │   ├───OuoUserMovimiento.php
│   │   ├───ParteInteresada.php
│   │   ├───PlanificacionPEI.php
│   │   ├───PlanificacionSIG.php
│   │   ├───Proceso.php
│   │   ├───ProcesoOuo.php
│   │   ├───ProgramaAuditoria.php
│   │   ├───Requerimiento.php
│   │   ├───RequerimientoAvance.php
│   │   ├───RequerimientoEvaluacion.php
│   │   ├───RequerimientoMovimiento.php
│   │   ├───Requisito.php
│   │   ├───Riesgo.php
│   │   ├───Salida.php
│   │   ├───Sipoc.php
│   │   ├───SubAreaCompliance.php
│   │   ├───Tag.php
│   │   ├───TipoDocumento.php
│   │   └───User.php
│   ├───Notifications\
│   │   ├───AccionAlertaNotificacion.php
│   │   ├───ActionApproved.php
│   │   ├───ActionApprovedNotificacion.php
│   │   └───ResetPasswordNotification.php
│   ├───Observers\
│   │   ├───AccionObserver.php
│   │   ├───DocumentoObserver.php
│   │   ├───DocumentoVersionObserver.php
│   │   └───HallazgoObserver.php
│   ├───Providers\
│   └───View\
├───bootstrap\
│   ├───app.php
│   └───cache\
├───certificados\
│   ├───certificate.crt
│   └───private.key
├───config\
│   ├───adminlte.php
│   ├───app.php
│   ├───auth.php
│   ├───broadcasting.php
│   ├───cache.php
│   ├───cors.php
│   ├───database.php
│   ├───dompdf.php
│   ├───filesystems.php
│   ├───hashing.php
│   ├───logging.php
│   ├───mail.php
│   ├───opciones.php
│   ├───permission.php
│   ├───queue.php
│   ├───sanctum.php
│   ├───services.php
│   ├───session.php
│   └───view.php
├───database\
│   ├───.gitignore
│   ├───Diccionario_Causas.txt
│   ├───factories\
│   ├───migrations\
│   └───seeders\
├───lang\
│   └───vendor\
├───node_modules\...
├───public\
│   ├───.htaccess
│   ├───favicon.ico
│   ├───index.php
│   ├───mix-manifest.json
│   ├───robots.txt
│   ├───build\...
│   ├───images\
│   ├───js\
│   ├───pruebas\
│   ├───vendor\
│   └───webfonts\
├───resources\
│   ├───css\
│   ├───js\
│   ├───sass\
│   └───views\
├───routes\
│   ├───api.php
│   ├───auth.php
│   ├───channels.php
│   ├───console.php
│   └───web.php
├───scriptbd\
│   ├───kallpaq-07-11-25.sql
│   ├───kallpaq-10-11-25.sql
│   ├───kallpaq-11-11-25.sql
│   ├───kallpaq-12-11-25.sql
│   └───kallpaq.sql
├───storage\
│   ├───app\
│   ├───framework\
│   └───logs\
├───tests\
│   ├───CreatesApplication.php
│   ├───TestCase.php
│   ├───Feature\
│   └───Unit\
└───vendor\...
```
