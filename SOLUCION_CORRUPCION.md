# Solución Error: "Read page with wrong checksum" (Aria Engine)

Este error indica que un archivo interno de la base de datos de XAMPP se ha corrompido (probablemente por un apagado inesperado).

Sigue estos pasos para repararlo:

1.  **Cerrar todo**
    *   Cierra la terminal donde tenías ejecutando `mysqld`.
    *   En el Panel de XAMPP, asegúrate de que MySQL esté **detenido** (botón "Stop").
    *   Si no se detiene, cierra XAMPP completamente y vuelve a abrirlo.

2.  **Borrar archivo de control corrupto**
    *   Abre el explorador de archivos y ve a la carpeta donde tienes instalado XAMPP.
    *   Generalmente es: **`C:\xampp\mysql\data`**
    *   Busca el archivo llamado **`aria_log_control`**.
    *   **Elimínalo** (o cámbiale el nombre a `aria_log_control_OLD` por seguridad).
    *   *Nota: NO borres ninguna carpeta ni otros archivos, solo `aria_log_control`.*

3.  **Reiniciar y Probar**
    *   Vuelve al Panel de XAMPP.
    *   Dale a **"Start"** en MySQL.
    *   XAMPP debería regenerar el archivo automáticamente y arrancar bien.

4.  **Reintentar los permisos**
    *   Si MySQL arranca correctamente (se pone en verde), intenta entrar a PHPMyAdmin.
    *   Si vuelves a tener el error de "Host 'localhost' is not allowed", repite los pasos del archivo anterior (`SOLUCION_MYSQL.md`) desde el **Paso 1**.
