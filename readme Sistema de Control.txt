readme
# Sistema de Control - Autoescuela Pro

Software de gestión integral diseñado para la administración de alumnos, instructores y flota vehicular. Desarrollado como solución técnica para la práctica final del curso.

##Requisitos de Entorno
* **Servidor:** Apache (Entorno Linux recomendado).
* **Base de Datos:** MySQL / MariaDB.
* **Intérprete:** PHP 7.4 o versiones superiores.

##Guía de Configuración
1.  Descargar el contenido en el directorio de trabajo: `/var/www/html/autoescuela`.
2.  Desde el gestor de base de datos, inicializar el esquema `autoescuela`.
3.  Ejecutar el archivo `script_creacion.sql` y, posteriormente, `carga_datos.sql`.
4.  Vincular el sistema editando las credenciales en el archivo `db.php`.

## Funcionalidades Implementadas
* **Módulo Maestro:** Gestión completa (CRUD) de Clientes, Profesores y Vehículos.
* **Sistema de Bajas:** Implementación de borrado lógico para mantener integridad referencial.
* **Agenda Inteligente:** Asignación de turnos con validación de recursos.
* **Reglas de Negocio:**
    * Control de carga docente: Máximo 4 horas por profesor.
    * Límite de usuario: Solo 1 clase práctica por cliente al día.
