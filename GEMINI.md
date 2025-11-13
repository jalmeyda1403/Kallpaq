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
