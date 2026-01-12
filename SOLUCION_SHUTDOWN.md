# Solución Error: "MySQL shutdown unexpectedly"

Este error suele pasar porque el proceso de MySQL se quedó "colgado" en segundo plano o hay archivos de log en conflicto.

### Paso 1: Matar procesos trabados
A veces XAMPP dice que está apagado, pero MySQL sigue corriendo oculto y bloquea el puerto.

1.  Abre el **Administrador de Tareas** de Windows (Ctrl + Shift + Esc).
2.  Ve a la pestaña **"Detalles"**.
3.  Busca **`mysqld.exe`**.
    *   Si lo encuentras: clic derecho -> **Finalizar tarea**.
4.  Busca cualquier otro proceso que diga `mysql`. Finalízalos también.

### Paso 2: Limpieza de Logs (Segunda opción)
Si el paso 1 no funcionó y el error persiste, probaremos una limpieza un poco más profunda (sin borrar tus datos).

1.  Ve a la carpeta **`C:\xampp\mysql\data`**.
2.  Busca archivo **`ibdata1`** -> **¡NO LO BORRES!** (Ahí están tus tablas).
3.  Busca estos archivos y **elimínalos**:
    *   `aria_log_control` (si se volvió a crear)
    *   `ib_logfile0`
    *   `ib_logfile1`
    *   `multi-master.info` (si existe)
4.  Intenta iniciar MySQL en XAMPP de nuevo.

### Paso 3: Revisar Puerto
Si sigue fallando, asegúrate de que Skype o VMWare no estén usando el puerto 3306.
*   En XAMPP, dale al botón **"Netstat"** y mira si el puerto 3306 está ocupado por otro programa.

### Resumen
Si logras que arranque en verde, vuelve a intentar entrar a `localhost/phpmyadmin`.
Si entras, ejecuta:
```powershell
php artisan migrate
```
