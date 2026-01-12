# Solución al Error: Host 'localhost' is not allowed to connect to this MariaDB server

Este error ocurre porque el usuario `root` de MySQL/MariaDB ha perdido los permisos para conectarse desde `localhost`. Esto es un problema de configuración del servidor XAMPP, no del código de Laravel.

Sigue estos pasos para solucionarlo:

1.  **Detener MySQL**
    *   Abre el **Panel de Control de XAMPP**.
    *   Detén el servicio **MySQL** (haz clic en "Stop").

2.  **Iniciar MySQL en modo seguro**
    *   En el Panel de XAMPP, haz clic en el botón **"Shell"** (a la derecha).
    *   En la ventana negra que aparece, escribe el siguiente comando y presiona Enter:
        ```cmd
        mysqld --skip-grant-tables
        ```
    *   *Nota: Esta ventana se quedará "congelada". No la cierres. Minimízala.*

3.  **Restaurar Permisos**
    *   Vuelve al Panel de XAMPP y abre **otra** ventana de **"Shell"**.
    *   Escribe `mysql` y presiona Enter. Deberías entrar a la consola de MariaDB.
    *   Copia y pega los siguientes comandos SQL, uno por uno:

    ```sql
    FLUSH PRIVILEGES;
    ```
    
    ```sql
    CREATE USER IF NOT EXISTS 'root'@'localhost' IDENTIFIED BY '';
    ```
    *(Si dice que ya existe, ignora el error y sigue)*

    ```sql
    GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
    ```

    ```sql
    FLUSH PRIVILEGES;
    ```

    ```sql
    EXIT;
    ```

4.  **Reiniciar MySQL**
    *   Cierra las dos ventanas del Shell.
    *   En el Panel de XAMPP, si MySQL sigue en verde (iniciado), dale a "Stop" y luego a "Start" de nuevo para iniciarlo normalmente.

5.  **Verificar**
    *   Intenta entrar a phpMyAdmin de nuevo.
    *   Si funciona, ejecuta en tu terminal de VS Code:
        ```powershell
        php artisan migrate
        ```
