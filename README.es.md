<div align="center">
  <br />
  <img src="https://raw.githubusercontent.com/victorhjsantiago/nredutech/main/public/images/nredutech.png" alt="Logo NREduTech" width="150" style="border-radius: 50%;">
  
  <h1 style="border-bottom: none; font-size: 2.5em; margin-bottom: 0;">NREduTech</h1>
  
  <strong style="font-size: 1.2em; color: #555;">
    Sistema de Gestión Académica y Programación de Recursos Didácticos
  </strong>
  
  <br />
  <br />

  <p style="font-size: 1.1em; max-width: 700px;">
    Una solución robusta y centralizada, desarrollada bajo la arquitectura <strong>Laravel MVC</strong>, destinada a la gestión integrada de escuelas, clases, recursos didácticos y programaciones para el <strong>Núcleo Regional de Educación (NRE)</strong>.
  </p>

  <p>
    <img src="https://img.shields.io/badge/estado-en%20desarrollo-yellow?style=for-the-badge" alt="Estado del Proyecto: En Desarrollo">
    <img src="https://img.shields.io/badge/PHP-8.4.11-777BB4?style=for-the-badge&logo=php" alt="Versión de PHP">
    <img src="https://img.shields.io/badge/Laravel-12.28.1-FF2D20?style=for-the-badge&logo=laravel" alt="Versión de Laravel">
    <img src="https://img.shields.io/badge/MariaDB-11.8.3-003545?style=for-the-badge&logo=mariadb" alt="Base de Datos">
  </p>

  <p>
    <a href="README.md">🇺🇸 English</a>
    ·
    <a href="README.pt-BR.md">🇧🇷 Português</a>
    ·
    <strong>🇪🇸 Español</strong>
  </p>
</div>

---

## 📖 Sobre el Proyecto

**NREduTech** es un Sistema de Gestión Académica (SGA) concebido para actuar como la plataforma central de administración del Núcleo Regional de Educación. La aplicación aborda el desafío de gestionar de forma eficiente la asignación de recursos pedagógicos, la programación de laboratorios y la organización de componentes curriculares entre múltiples instituciones educativas.

Desde el punto de vista académico, el proyecto es una implementación práctica de los principios del **Desarrollo de Software Orientado a Objetos (POO)** y de la arquitectura **Modelo-Vista-Controlador (MVC)**. Utiliza el framework Laravel para garantizar un desarrollo rápido, seguro y escalable, abstrayendo complejidades de bajo nivel y permitiendo un enfoque total en las reglas de negocio.

La plataforma está diseñada con un enfoque en distintos perfiles de usuario (Administradores, Directores y Profesores), ofreciendo *dashboards* y funcionalidades específicas para cada nivel de acceso. El sistema incorpora funcionalidades esenciales como la generación de informes complejos, un sistema de notificaciones proactivo y rutinas de copia de seguridad automatizadas, garantizando la integridad y la disponibilidad de los datos.

## ✨ Funcionalidades Principales

El sistema está modularizado para cubrir todas las necesidades de la gestión educativa:

* **👥 Gestión de Usuarios:** Control de acceso granular con tres niveles de permiso (Administrador, Director, Profesor).
* **🏫 Gestión de Escuelas y Municipios:** Registro y administración centralizada de las instituciones educativas y sus localidades.
* **👨‍🎓 Gestión de Clases:** Organización de clases vinculadas a cada escuela.
* **📂 Gestión de Asignaturas:** (Componentes Curriculares) Registro y asociación de las asignaturas impartidas.
* **📖 Gestión de Recursos Didácticos:** Catálogo de todos los recursos pedagógicos y tecnológicos disponibles para programar (ej.: laboratorios, proyectores, kits de robótica).
* **📅 Programación Inteligente:** Interfaz de calendario (basada en *FullCalendar*) para que los profesores reserven recursos para sus clases, con validación de disponibilidad.
* **📊 Informes Avanzados:** Generación de informes dinámicos sobre el uso de recursos, programaciones por escuela y más, con exportación a **PDF** y **Excel**.
* **🔔 Sistema de Notificaciones:** Alertas en tiempo real en la plataforma y envío de correos electrónicos para acciones críticas (ej.: confirmación de una programación).
* **🗃️ Copia de Seguridad y Restauración:** Funcionalidad robusta para crear *copias de seguridad* de la aplicación y de la base de datos, con programación automática y restauración.
* **♿ Accesibilidad:** Integración nativa con **VLibras** para garantizar la accesibilidad a personas con discapacidad.

---

## 🛠️ Requisitos y Reglas de Negocio

La lógica del sistema fue modelada para reflejar las jerarquías y procesos de un entorno educativo real.

### Reglas de Negocio Principales

* 🔑 **Aprobación de Usuarios:** Los profesores y directores pueden autorregistrarse, pero sus cuentas se crean con estado `pendiente`. Un `Administrador` debe aprobar manualmente el registro para que el usuario pueda acceder al sistema.
* 🚦 **Jerarquía de Permisos:**
    * **Administrador:** Tiene control total (CRUD) sobre todas las entidades: Escuelas, Municipios, Usuarios, Clases, Recursos y Asignaturas. Es el único perfil que puede realizar copias de seguridad y restauraciones del sistema.
    * **Director:** Tiene control (CRUD) sobre entidades *solo* de su propia escuela (Clases, Profesores, Recursos, Asignaturas). Puede visualizar informes referentes a su escuela.
    * **Profesor:** El enfoque está en la programación. Puede programar recursos para sus clases/asignaturas (Ofertas) y gestionar (CRUD) los recursos y asignaturas que él mismo registró.
* 🌍 **Propiedad de los Recursos:** Los Recursos y Asignaturas pueden ser "Globales" (pertenecen al NRE, `school_id = null`) y estar disponibles para todas las escuelas, o pertenecer a una escuela específica (visibles solo para los usuarios de esa escuela).
* ⏱️ **Conflicto de Programación:** El sistema impide activamente que un mismo recurso (`recurso_didatico_id`) sea reservado por dos personas en el mismo intervalo de tiempo (validación de superposición de `data_inicio` y `data_fim`).
* 🔗 **Integridad de los Datos:** El sistema utiliza restricciones de clave foránea (`FOREIGN KEY`) para garantizar la integridad referencial. No es posible eliminar una Escuela si tiene Clases o Usuarios vinculados; no es posible eliminar un Municipio si tiene Escuelas vinculadas.

### Reglas de Negocio (RN)
<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #0169b4; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">ID</th>
        <th style="padding: 12px 15px; text-align: left;">Actor(es) afectado(s)</th>
        <th style="padding: 12px 15px; text-align: left;">Descripción de la regla</th>
        <th style="padding: 12px 15px; text-align: left;">Justificación/origen</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-001</td>
        <td style="padding: 12px 15px;">Usuario (todos)</td>
        <td style="padding: 12px 15px;">Al actualizar el correo electrónico en el perfil, la cuenta del usuario debe marcarse como "no verificada", exigiendo una nueva confirmación.</td>
        <td style="padding: 12px 15px;">Garantizar la posesión y validez de la nueva dirección de correo electrónico.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-002</td>
        <td style="padding: 12px 15px;">Usuario (todos)</td>
        <td style="padding: 12px 15px;">Para eliminar su propia cuenta, el usuario debe confirmar su contraseña actual.</td>
        <td style="padding: 12px 15px;">Medida de seguridad para evitar la eliminación accidental o malintencionada.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-003</td>
        <td style="padding: 12px 15px;">Usuario (nuevo)</td>
        <td style="padding: 12px 15px;">Los campos de registro (nombre de usuario, correo electrónico, CPF, RG, etc.) deben ser únicos en el sistema.</td>
        <td style="padding: 12px 15px;">Garantizar la unicidad de cada usuario en la base de datos.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-004</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Solo los administradores pueden visualizar y gestionar usuarios de todas las escuelas.</td>
        <td style="padding: 12px 15px;">Centralización del control de acceso y la gestión de cuentas en el NRE.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-005</td>
        <td style="padding: 12px 15px;">Director, profesor</td>
        <td style="padding: 12px 15px;">Los directores y profesores solo pueden visualizar usuarios de su propia escuela.</td>
        <td style="padding: 12px 15px;">Garantizar el aislamiento de datos (privacidad) entre instituciones.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-006</td>
        <td style="padding: 12px 15px;">Director</td>
        <td style="padding: 12px 15px;">Los directores solo pueden crear usuarios (ej.: profesores) para su propia escuela.</td>
        <td style="padding: 12px 15px;">Delegación de la gestión de personal a nivel de escuela.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-007</td>
        <td style="padding: 12px 15px;">Director</td>
        <td style="padding: 12px 15px;">Los directores no pueden crear ni promover usuarios al nivel de "administrador".</td>
        <td style="padding: 12px 15px;">Mantener la jerarquía de permisos y la seguridad del sistema.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-008</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Un usuario administrador (o cualquier usuario) no puede eliminarse a sí mismo.</td>
        <td style="padding: 12px 15px;">Prevenir el bloqueo accidental del sistema.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-009</td>
        <td style="padding: 12px 15px;">Administrador, director</td>
        <td style="padding: 12px 15px;">El sistema debe impedir la eliminación de usuarios que tengan dependencias (recursos creados u ofertas).</td>
        <td style="padding: 12px 15px;">Garantizar la integridad referencial y el historial de acciones.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-010</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Solo los Administradores pueden gestionar (CRUD) municipios y escuelas.</td>
        <td style="padding: 12px 15px;">Centralización de la gestión de la infraestructura de unidades del NRE.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-011</td>
        <td style="padding: 12px 15px;">Administrador (al crear una escuela)</td>
        <td style="padding: 12px 15px;">Una Escuela debe estar obligatoriamente asociada a un municipio.</td>
        <td style="padding: 12px 15px;">Requisito de organización estructural del NRE.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-012</td>
        <td style="padding: 12px 15px;">Administrador (al crear una escuela)</td>
        <td style="padding: 12px 15px;">Los campos de nivel educativo y tipo de una escuela deben ser valores predefinidos (enum).</td>
        <td style="padding: 12px 15px;">Garantizar la estandarización y consistencia de los datos para los informes.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-013</td>
        <td style="padding: 12px 15px;">Director, profesor</td>
        <td style="padding: 12px 15px;">Los directores y profesores solo pueden gestionar (visualizar, crear, editar) clases de su propia escuela.</td>
        <td style="padding: 12px 15px;">Mantener el alcance de gestión restringido a la propia institución.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-014</td>
        <td style="padding: 12px 15px;">Usuario (al crear una clase)</td>
        <td style="padding: 12px 15px;">El año escolar debe ser un número entero dentro de un rango válido (ej.: 2000-2100).</td>
        <td style="padding: 12px 15px;">Garantizar la validez y consistencia de los datos del año escolar.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-015</td>
        <td style="padding: 12px 15px;">Usuario (al eliminar una clase)</td>
        <td style="padding: 12px 15px;">El sistema debe impedir la eliminación de clases que tengan ofertas de componentes.</td>
        <td style="padding: 12px 15px;">Proteger el historial de asignación de asignaturas y profesores.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-016</td>
        <td style="padding: 12px 15px;">Administrador, director, profesor</td>
        <td style="padding: 12px 15px;">Las asignaturas pueden ser "globales" o "específicas" (vinculadas a una escuela).</td>
        <td style="padding: 12px 15px;">Permitir componentes curriculares comunes a todas las escuelas y componentes únicos.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-017</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Solo los administradores pueden crear o editar asignaturas globales.</td>
        <td style="padding: 12px 15px;">Control centralizado sobre el currículo básico regional.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-018</td>
        <td style="padding: 12px 15px;">Director, profesor</td>
        <td style="padding: 12px 15px;">Los directores y profesores visualizan las asignaturas globales y las específicas de su escuela.</td>
        <td style="padding: 12px 15px;">Proporcionar acceso al currículo relevante para la institución.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-019</td>
        <td style="padding: 12px 15px;">Usuario (al eliminar una asignatura)</td>
        <td style="padding: 12px 15px;">El sistema debe impedir la eliminación de asignaturas que tengan ofertas vinculadas.</td>
        <td style="padding: 12px 15px;">Garantizar la integridad del historial de clases.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-020</td>
        <td style="padding: 12px 15px;">Profesor</td>
        <td style="padding: 12px 15px;">Los profesores solo pueden crear ofertas de componentes para sí mismos (y no para otros profesores).</td>
        <td style="padding: 12px 15px;">Garantizar que el profesor solo gestione sus propias asignaciones.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-021</td>
        <td style="padding: 12px 15px;">Usuario (al eliminar una oferta)</td>
        <td style="padding: 12px 15px;">El sistema debe impedir la eliminación de ofertas que tengan programaciones vinculadas.</td>
        <td style="padding: 12px 15px;">Proteger el historial de uso de recursos en las programaciones.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-022</td>
        <td style="padding: 12px 15px;">Usuario (al crear un recurso)</td>
        <td style="padding: 12px 15px;">La cantidad de un recurso debe ser un número entero igual o mayor que 1.</td>
        <td style="padding: 12px 15px;">Garantizar que el inventario de recursos tenga valores válidos.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-023</td>
        <td style="padding: 12px 15px;">Usuario (al eliminar un recurso)</td>
        <td style="padding: 12px 15px;">El sistema debe impedir la eliminación de recursos que tengan programaciones vinculadas.</td>
        <td style="padding: 12px 15px;">Garantizar la integridad del historial de programaciones.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-024</td>
        <td style="padding: 12px 15px;">Usuario (al crear una programación)</td>
        <td style="padding: 12px 15px;">La fecha/hora de fin de una programación debe ser, obligatoriamente, posterior a la fecha/hora de inicio.</td>
        <td style="padding: 12px 15px;">Garantizar la lógica temporal y la validez del período programado.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-025</td>
        <td style="padding: 12px 15px;">Usuario (al crear una programación)</td>
        <td style="padding: 12px 15px;">La fecha/hora de inicio debe ser, como mínimo, 10 minutos posterior al momento de la creación.</td>
        <td style="padding: 12px 15px;">Evitar programaciones retroactivas o instantáneas imposibles de cumplir.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-026</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">El sistema no debe permitir programar el mismo recurso en horarios superpuestos (conflictivos).</td>
        <td style="padding: 12px 15px;">Prevención de conflictos de asignación (doble reserva).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-027</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">No está permitido crear programaciones en horarios específicos (ej.: de madrugada, entre las 23:00 y las 06:00).</td>
        <td style="padding: 12px 15px;">Restricción de seguridad y adecuación al horario de funcionamiento.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-028</td>
        <td style="padding: 12px 15px;">Usuario (al cancelar una programación)</td>
        <td style="padding: 12px 15px;">Una programación no puede cancelarse con menos de 10 minutos de antelación a su inicio.</td>
        <td style="padding: 12px 15px;">Evitar cancelaciones de última hora que perjudiquen la asignación de recursos.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-029</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">La creación y cancelación de programaciones debe disparar notificaciones a los involucrados.</td>
        <td style="padding: 12px 15px;">Mantener a los usuarios informados sobre los cambios en el calendario.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-030</td>
        <td style="padding: 12px 15px;">Director</td>
        <td style="padding: 12px 15px;">Los informes generados por los directores deben contener solo datos de su propia escuela.</td>
        <td style="padding: 12px 15px;">Garantizar el aislamiento de datos y la privacidad entre instituciones.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-031</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Solo los administradores pueden acceder al área de configuración (copias de seguridad, etc.).</td>
        <td style="padding: 12px 15px;">Restringir el acceso a las funcionalidades críticas del sistema.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-032</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">El sistema debe notificar al administrador por correo electrónico cuando una copia de seguridad se complete con éxito.</td>
        <td style="padding: 12px 15px;">Proporcionar confirmación y seguimiento de tareas críticas.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-033</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">El sistema debe impedir la eliminación de municipios que tengan escuelas vinculadas.</td>
        <td style="padding: 12px 15px;">Garantizar la integridad referencial de la ubicación de las escuelas.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-034</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">El sistema debe impedir la eliminación de escuelas que tengan clases o usuarios vinculados.</td>
        <td style="padding: 12px 15px;">Proteger los datos asociados (clases, usuarios) de la institución.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-035</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">El sistema debe impedir la creación de ofertas duplicadas (misma asignatura, profesor y clase).</td>
        <td style="padding: 12px 15px;">Evitar la redundancia y la inconsistencia en los datos pedagógicos.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-036</td>
        <td style="padding: 12px 15px;">Administrador, director, profesor</td>
        <td style="padding: 12px 15px;">La edición de una asignatura está permitida solo a su creador, al director de la escuela o a un administrador.</td>
        <td style="padding: 12px 15px;">Control de quién puede modificar los datos de un componente curricular.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-037</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Solo los administradores pueden cambiar la escuela asociada a una asignatura (o convertirla en global).</td>
        <td style="padding: 12px 15px;">Control centralizado sobre la estructura curricular regional.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-038</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">Las nuevas asignaturas registradas por profesores o directores se inician con el estado "Pendiente".</td>
        <td style="padding: 12px 15px;">Garantizar el control y la estandarización del catálogo de componentes.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-039</td>
        <td style="padding: 12px 15px;">Usuario (al crear un recurso)</td>
        <td style="padding: 12px 15px;">Al registrar un recurso con una cantidad mayor que 1, el sistema debe ofrecer la opción de crear elementos individuales o un único lote.</td>
        <td style="padding: 12px 15px;">Facilitar el registro masivo de inventario (usabilidad).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-040</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">Los nuevos usuarios registrados (Registro Público) se inician con el estado "Pendiente" y deben ser aprobados.</td>
        <td style="padding: 12px 15px;">Medida de seguridad para validar a los nuevos usuarios antes de conceder el acceso.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-041</td>
        <td style="padding: 12px 15px;">Administrador, director</td>
        <td style="padding: 12px 15px;">Los directores solo pueden eliminar usuarios (que no sean administradores) de su propia escuela.</td>
        <td style="padding: 12px 15px;">Mantener la jerarquía de permisos y el alcance de gestión.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-042</td>
        <td style="padding: 12px 15px;">Administrador, director, profesor</td>
        <td style="padding: 12px 15px;">Una programación solo puede ser cancelada por su creador (profesor), por el director de la escuela o por un administrador.</td>
        <td style="padding: 12px 15px;">Definir la responsabilidad sobre la cancelación de reservas.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-043</td>
        <td style="padding: 12px 15px;">Administrador, director</td>
        <td style="padding: 12px 15px;">El acceso al módulo de informes está restringido a administradores y directores.</td>
        <td style="padding: 12px 15px;">Proteger el acceso a datos analíticos y consolidados.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-044</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Las acciones críticas (ejecutar copia de seguridad, descargar copia de seguridad, restaurar) requieren que el administrador confirme su contraseña actual.</td>
        <td style="padding: 12px 15px;">Medida de seguridad (autenticación reforzada) para operaciones sensibles.</td>
      </tr>
      <tr style="background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-045</td>
        <td style="padding: 12px 15px;">Usuario (al cambiar la contraseña)</td>
        <td style="padding: 12px 15px;">La contraseña del usuario debe tener un mínimo de 16 caracteres.</td>
        <td style="padding: 12px 15px;">Garantizar un nivel mínimo de complejidad y seguridad para las contraseñas.</td>
      </tr>
    </tbody>
  </table>
</div>

### Requisitos Funcionales (RF)
<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #0169b4; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">ID</th>
        <th style="padding: 12px 15px; text-align: left;">Módulo</th>
        <th style="padding: 12px 15px; text-align: left;">Nombre del requisito</th>
        <th style="padding: 12px 15px; text-align: left;">Descripción</th>
        <th style="padding: 12px 15px; text-align: left;">Prioridad</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-001</td>
        <td style="padding: 12px 15px;">Autenticación</td>
        <td style="padding: 12px 15px;">Registro de usuario (público)</td>
        <td style="padding: 12px 15px;">El sistema debe permitir que los usuarios (profesores, directores) se registren a través de un formulario público.</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-002</td>
        <td style="padding: 12px 15px;">Autenticación</td>
        <td style="padding: 12px 15px;">Inicio de sesión</td>
        <td style="padding: 12px 15px;">El sistema debe permitir que los usuarios registrados inicien sesión con correo electrónico y contraseña.</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-003</td>
        <td style="padding: 12px 15px;">Autenticación</td>
        <td style="padding: 12px 15px;">Recuperación de contraseña</td>
        <td style="padding: 12px 15px;">El sistema debe permitir que los usuarios recuperen sus contraseñas a través de un flujo de "Olvidé mi contraseña".</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-004</td>
        <td style="padding: 12px 15px;">Perfil</td>
        <td style="padding: 12px 15px;">Actualizar información del perfil</td>
        <td style="padding: 12px 15px;">El usuario debe poder visualizar y actualizar su información de perfil (nombre, correo electrónico, teléfono).</td>
        <td style="padding: 12px 15px;">Media</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-005</td>
        <td style="padding: 12px 15px;">Perfil</td>
        <td style="padding: 12px 15px;">Actualizar contraseña</td>
        <td style="padding: 12px 15px;">El usuario debe poder actualizar su contraseña, proporcionando la contraseña actual.</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-006</td>
        <td style="padding: 12px 15px;">Perfil</td>
        <td style="padding: 12px 15px;">Eliminar cuenta</td>
        <td style="padding: 12px 15px;">La cuenta de un usuario puede ser eliminada por él mismo o por un administrador/director.</td>
        <td style="padding: 12px 15px;">Media</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-007</td>
        <td style="padding: 12px 15px;">Gestión de usuarios</td>
        <td style="padding: 12px 15px;">CRUD de usuarios</td>
        <td style="padding: 12px 15px;">El sistema debe permitir el CRUD de usuarios.</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-008</td>
        <td style="padding: 12px 15px;">Gestión de usuarios</td>
        <td style="padding: 12px 15px;">Filtrar usuarios</td>
        <td style="padding: 12px 15px;">El sistema debe permitir filtrar la lista de usuarios (por nombre, correo electrónico, estado, tipo, CPF, RG, formación, etc.).</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-009</td>
        <td style="padding: 12px 15px;">Gestión escolar</td>
        <td style="padding: 12px 15px;">CRUD de municipios</td>
        <td style="padding: 12px 15px;">El sistema debe permitir el CRUD de municipios.</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-010</td>
        <td style="padding: 12px 15px;">Gestión escolar</td>
        <td style="padding: 12px 15px;">CRUD de escuelas</td>
        <td style="padding: 12px 15px;">El sistema debe permitir el CRUD de escuelas.</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-011</td>
        <td style="padding: 12px 15px;">Gestión de clases</td>
        <td style="padding: 12px 15px;">CRUD de clases</td>
        <td style="padding: 12px 15px;">El sistema debe permitir el CRUD de clases.</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-012</td>
        <td style="padding: 12px 15px;">Gestión de clases</td>
        <td style="padding: 12px 15px;">Detalle de clase (ofertas)</td>
        <td style="padding: 12px 15px;">El sistema debe permitir visualizar los detalles de una clase y sus ofertas.</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-013</td>
        <td style="padding: 12px 15px;">Asignaturas</td>
        <td style="padding: 12px 15px;">CRUD de componentes curriculares</td>
        <td style="padding: 12px 15px;">El sistema debe permitir el CRUD de componentes curriculares (asignaturas).</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-014</td>
        <td style="padding: 12px 15px;">Asignaturas</td>
        <td style="padding: 12px 15px;">Filtrar componentes</td>
        <td style="padding: 12px 15px;">El sistema debe permitir el filtrado de componentes (nombre/descripción, carga horaria, estado, escuela).</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-015</td>
        <td style="padding: 12px 15px;">Oferta de componentes</td>
        <td style="padding: 12px 15px;">CRUD de ofertas</td>
        <td style="padding: 12px 15px;">El sistema debe permitir el CRUD de ofertas.</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-016</td>
        <td style="padding: 12px 15px;">Recursos didácticos</td>
        <td style="padding: 12px 15px;">CRUD de recursos didácticos</td>
        <td style="padding: 12px 15px;">El sistema debe permitir el CRUD de recursos didácticos y laboratorios.</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-017</td>
        <td style="padding: 12px 15px;">Programación</td>
        <td style="padding: 12px 15px;">Gestionar programación</td>
        <td style="padding: 12px 15px;">El sistema debe permitir que los usuarios creen, visualicen y cancelen programaciones de recursos didácticos.</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-018</td>
        <td style="padding: 12px 15px;">Programación</td>
        <td style="padding: 12px 15px;">Calendario de programación</td>
        <td style="padding: 12px 15px;">El sistema debe mostrar las programaciones en una interfaz de calendario interactivo (FullCalendar).</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-019</td>
        <td style="padding: 12px 15px;">Informes</td>
        <td style="padding: 12px 15px;">Visualizar informes</td>
        <td style="padding: 12px 15px;">El sistema debe permitir la previsualización de informes analíticos con filtros avanzados y gráficos.</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-020</td>
        <td style="padding: 12px 15px;">Informes</td>
        <td style="padding: 12px 15px;">Exportar informes</td>
        <td style="padding: 12px 15px;">El sistema debe permitir la exportación de informes en múltiples formatos (PDF, XLSX, CSV, ODS, HTML).</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-021</td>
        <td style="padding: 12px 15px;">Configuración</td>
        <td style="padding: 12px 15px;">Gestión de copias de seguridad</td>
        <td style="padding: 12px 15px;">El sistema debe permitir la gestión de copias de seguridad (crear manualmente, descargar y eliminar).</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-022</td>
        <td style="padding: 12px 15px;">Configuración</td>
        <td style="padding: 12px 15px;">Restauración de copias de seguridad</td>
        <td style="padding: 12px 15px;">El sistema debe permitir la restauración de datos a partir de un archivo de copia de seguridad.</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-023</td>
        <td style="padding: 12px 15px;">Notificaciones</td>
        <td style="padding: 12px 15px;">Mostrar notificaciones</td>
        <td style="padding: 12px 15px;">El sistema debe mostrar notificaciones a los usuarios (vía interfaz y correo electrónico) sobre eventos relevantes.</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-024</td>
        <td style="padding: 12px 15px;">Notificaciones</td>
        <td style="padding: 12px 15px;">Marcar notificaciones como leídas</td>
        <td style="padding: 12px 15px;">El sistema debe marcar las notificaciones como leídas (automáticamente al visualizar la lista).</td>
        <td style="padding: 12px 15px;">Media</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-025</td>
        <td style="padding: 12px 15px;">Gestión de usuarios</td>
        <td style="padding: 12px 15px;">Aprobación de usuarios</td>
        <td style="padding: 12px 15px;">El sistema debe permitir que administradores y directores aprueben o rechacen/bloqueen nuevos registros pendientes.</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-026</td>
        <td style="padding: 12px 15px;">Asignaturas</td>
        <td style="padding: 12px 15px;">Aprobación de asignaturas</td>
        <td style="padding: 12px 15px;">El sistema debe permitir que los usuarios autorizados (administrador, director) aprueben o rechacen componentes con estado "Pendiente".</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-027</td>
        <td style="padding: 12px 15px;">Programación</td>
        <td style="padding: 12px 15px;">Consultar disponibilidad</td>
        <td style="padding: 12px 15px;">El sistema debe mostrar la disponibilidad de recursos (disponibles y programados) para un día específico.</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-028</td>
        <td style="padding: 12px 15px;">Recursos didácticos</td>
        <td style="padding: 12px 15px;">Registro por lotes</td>
        <td style="padding: 12px 15px;">El sistema debe permitir el registro de múltiples recursos individuales a partir de un único formulario (mediante una casilla de cantidad).</td>
        <td style="padding: 12px 15px;">Media</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-029</td>
        <td style="padding: 12px 15px;">Notificaciones</td>
        <td style="padding: 12px 15px;">Limpiar notificaciones</td>
        <td style="padding: 12px 15px;">El sistema debe permitir al usuario eliminar notificaciones individualmente o limpiar todo el historial.</td>
        <td style="padding: 12px 15px;">Media</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-030</td>
        <td style="padding: 12px 15px;">Programación</td>
        <td style="padding: 12px 15px;">Consultar disponibilidad diaria</td>
        <td style="padding: 12px 15px;">El sistema debe mostrar la disponibilidad de recursos (disponibles y programados) para un día específico seleccionado.</td>
        <td style="padding: 12px 15px;">Esencial</td>
      </tr>
      <tr style="background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-031</td>
        <td style="padding: 12px 15px;">Notificaciones</td>
        <td style="padding: 12px 15px;">Eliminar notificaciones</td>
        <td style="padding: 12px 15px;">El sistema debe permitir al usuario eliminar notificaciones (individualmente o mediante "Limpiar Todas").</td>
        <td style="padding: 12px 15px;">Media</td>
      </tr>
    </tbody>
  </table>
</div>

### Requisitos No Funcionales (RNF)
<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #0169b4; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">ID</th>
        <th style="padding: 12px 15px; text-align: left;">Atributo de calidad</th>
        <th style="padding: 12px 15px; text-align: left;">Descripción del requisito</th>
        <th style="padding: 12px 15px; text-align: left;">Métrica de verificación</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-001</td>
        <td style="padding: 12px 15px;">Seguridad (control de acceso)</td>
        <td style="padding: 12px 15px;">El sistema debe contar con un control de acceso robusto basado en roles (administrador, director, profesor).</td>
        <td style="padding: 12px 15px;">Pruebas de integración que validan que cada perfil solo accede a las rutas y datos permitidos (pruebas de estado HTTP 403).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RNF-002</td>
        <td style="padding: 12px 15px;">Seguridad (datos)</td>
        <td style="padding: 12px 15px;">Las contraseñas de los usuarios deben almacenarse con un hashing fuerte y moderno (Argon2id).</td>
        <td style="padding: 12px 15px;">Revisión de código y pruebas unitarias que verifican que el hash se genera correctamente.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-003</td>
        <td style="padding: 12px 15px;">Seguridad (datos)</td>
        <td style="padding: 12px 15px;">Los datos personales sensibles (como CPF y RG) deben almacenarse de forma cifrada (ej.: AES-256-CBC).</td>
        <td style="padding: 12px 15px;">Auditoría de la implementación y verificación manual de la base de datos para confirmar que los datos no están en texto plano.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RNF-004</td>
        <td style="padding: 12px 15px;">Seguridad (web)</td>
        <td style="padding: 12px 15px;">El sistema debe estar protegido contra ataques comunes (CSRF, XSS, SQL Injection).</td>
        <td style="padding: 12px 15px;">Revisión de código (uso de Eloquent ORM, Blade, middleware, CSRF) y ejecución de pruebas de penetración básicas.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-005</td>
        <td style="padding: 12px 15px;">Integridad</td>
        <td style="padding: 12px 15px;">El sistema debe garantizar la integridad referencial, impidiendo la eliminación de datos "padre" con registros "hijo" vinculados.</td>
        <td style="padding: 12px 15px;">Pruebas de integración (Feature Tests) que intentan eliminar registros con dependencias y validan la recepción del error.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RNF-006</td>
        <td style="padding: 12px 15px;">Confiabilidad (copia de seguridad)</td>
        <td style="padding: 12px 15px;">El sistema debe proporcionar mecanismos para la copia de seguridad (manual) y la restauración de la base de datos.</td>
        <td style="padding: 12px 15px;">Pruebas funcionales de la interfaz de "Copia de Seguridad y Restauración". Verificación de la creación de los archivos de copia de seguridad en el servidor.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-007</td>
        <td style="padding: 12px 15px;">Mantenibilidad (capacidad de prueba)</td>
        <td style="padding: 12px 15px;">El código debe ser comprobable, siguiendo estándares de pruebas unitarias y de integración (PHPUnit).</td>
        <td style="padding: 12px 15px;">Ejecución de la suite de pruebas y verificación de la cobertura de código.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RNF-008</td>
        <td style="padding: 12px 15px;">Localización</td>
        <td style="padding: 12px 15px;">El idioma principal del sistema debe estar configurado como portugués (Brasil).</td>
        <td style="padding: 12px 15px;">Verificación de los archivos de idioma y de la interfaz de usuario.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-009</td>
        <td style="padding: 12px 15px;">Plataforma (tecnología)</td>
        <td style="padding: 12px 15px;">El sistema debe desarrollarse con el framework Laravel (PHP), MariaDB, y herramientas frontend como Vite.js y Alpine.js.</td>
        <td style="padding: 12px 15px;">Verificación de los archivos de configuración del proyecto (ej.: composer.json, package.json).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RNF-010</td>
        <td style="padding: 12px 15px;">Rendimiento (interfaz)</td>
        <td style="padding: 12px 15px;">El módulo de programaciones debe usar AJAX (Axios) para cargar la disponibilidad de recursos sin recargar la página.</td>
        <td style="padding: 12px 15px;">Prueba funcional del calendario (hacer clic en un día) y verificación de que se realiza una solicitud.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-011</td>
        <td style="padding: 12px 15px;">Usabilidad (visualización de datos)</td>
        <td style="padding: 12px 15px;">El módulo de informes debe usar gráficos (ej.: Chart.js) para facilitar la interpretación.</td>
        <td style="padding: 12px 15px;">Prueba funcional de la página de informes y verificación de los gráficos.</td>
      </tr>
      <tr style="background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-012</td>
        <td style="padding: 12px 15px;">Usabilidad (interacción)</td>
        <td style="padding: 12px 15px;">El sistema debe usar modales (SweetAlert2) para acciones destructivas.</td>
        <td style="padding: 12px 15px;">Prueba funcional y verificación de que se muestra el modal de confirmación.</td>
      </tr>
    </tbody>
  </table>
</div>

---

## 💻 Entorno de Desarrollo

El proyecto se desarrolló con un conjunto de herramientas moderno, enfocado en la seguridad y la productividad, en un entorno híbrido.

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #444; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">Categoría</th>
        <th style="padding: 12px 15px; text-align: left;">Herramienta</th>
        <th style="padding: 12px 15px; text-align: left;">Versión</th>
        <th style="padding: 12px 15px; text-align: left;">Propósito</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">Sistema Operativo</td>
        <td style="padding: 12px 15px;"><strong>Windows 11 + WSL 2 (Ubuntu)</strong></td>
        <td style="padding: 12px 15px;">-</td>
        <td style="padding: 12px 15px;">Entorno de desarrollo híbrido, que combina la interfaz de Windows con una terminal Linux nativa (WSL) para mejorar el rendimiento.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">Sistema Operativo</td>
        <td style="padding: 12px 15px;"><strong>Kali GNU/Linux Rolling</strong></td>
        <td style="padding: 12px 15px;">2025.3</td>
        <td style="padding: 12px 15px;">Utilizado para pruebas de seguridad (Pentest) y validación de la robustez de la aplicación.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">Editor de Código</td>
        <td style="padding: 12px 15px;"><strong>Visual Studio Code</strong></td>
        <td style="padding: 12px 15px;">1.103.1</td>
        <td style="padding: 12px 15px;">Editor principal con extensiones para PHP, Laravel, Blade y Tailwind.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">Control de Versiones</td>
        <td style="padding: 12px 15px;"><strong>Git</strong></td>
        <td style="padding: 12px 15px;">2.50.1</td>
        <td style="padding: 12px 15px;">Gestión del código fuente y control de versiones.</td>
      </tr>
    </tbody>
  </table>
</div>

---

## 🚀 Stack Tecnológico y Justificación Académica

La selección de tecnologías (el *stack*) de NREduTech fue deliberada, con el objetivo de optimizar el rendimiento, la seguridad y la productividad del desarrollo.

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #444; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">Tecnología</th>
        <th style="padding: 12px 15px; text-align: left;">Versión</th>
        <th style="padding: 12px 15px; text-align: left;">¿Por qué fue elegida? (Ventajas frente a alternativas)</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>PHP</strong></td>
        <td style="padding: 12px 15px;">8.4.11</td>
        <td style="padding: 12px 15px;">
          <strong>Rendimiento y Modernidad:</strong> PHP 8.4 ofrece mejoras drásticas de rendimiento gracias al compilador <strong>JIT (Just-In-Time)</strong>. Sus características modernas (tipado estricto, Enums, Readonly Properties) lo hacen más robusto y menos propenso a errores.<br>
          <strong>Ventaja frente a Alternativas (Python/Node.js):</strong> La facilidad de despliegue (hosting) de PHP es incomparable. Su curva de aprendizaje es más rápida que la de frameworks como Django (Python), y su modelo multiproceso es más sencillo de gestionar para aplicaciones web tradicionales que el event loop de Node.js.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Laravel</strong></td>
        <td style="padding: 12px 15px;">12.28.1</td>
        <td style="padding: 12px 15px;">
          <strong>Ecosistema "Todo Incluido":</strong> Elegido por su ecosistema completo. El <strong>Eloquent ORM</strong> se considera más elegante y productivo que Doctrine (Symfony) o TypeORM (Node.js). El motor de plantillas <strong>Blade</strong> es simple y extensible. Herramientas integradas como `artisan` y la programación de tareas abstraen complejidades que en frameworks más "agnósticos" requerirían una implementación manual.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>MariaDB (Servidor/Cliente)</strong></td>
        <td style="padding: 12px 15px;">11.8.3 / 15.2</td>
        <td style="padding: 12px 15px;">
          <strong>Rendimiento Open-Source:</strong> Un fork de MySQL mantenido por la comunidad, centrado en el rendimiento y la apertura. Ofrece compatibilidad total con MySQL (y Eloquent), pero con optimizaciones de rendimiento (ej.: motores de almacenamiento como Aria) y un ciclo de nuevas funcionalidades más rápido. Es superior a MySQL en términos de licenciamiento y apertura, y a menudo supera a MySQL en el rendimiento de consultas complejas.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Tailwind CSS</strong></td>
        <td style="padding: 12px 15px;">3.x</td>
        <td style="padding: 12px 15px;">
          <strong>Productividad y Personalización:</strong> Superior a los frameworks basados en componentes (como Bootstrap). En lugar de ofrecer componentes prediseñados (ej.: `.card`) que deben sobrescribirse, Tailwind proporciona clases de utilidad de bajo nivel. Esto permite crear diseños 100% personalizados y responsivos sin "luchar" contra estilos predefinidos, resultando en un CSS final más reducido.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Vite.js</strong></td>
        <td style="padding: 12px 15px;">7.1.10</td>
        <td style="padding: 12px 15px;">
          <strong>Velocidad de Desarrollo:</strong> Sustituye a Webpack/Mix. Su principal ventaja es el <strong>Hot Module Replacement (HMR)</strong> casi instantáneo. Utiliza ESBuild (escrito en Go) para precompilar las dependencias, haciendo que el build y la actualización del servidor de desarrollo sean órdenes de magnitud más rápidos que con Webpack.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Node.js / NPM</strong></td>
        <td style="padding: 12px 15px;">20.19.2 / 9.2.0</td>
        <td style="padding: 12px 15px;">
          <strong>Ecosistema Frontend:</strong> Runtime de JavaScript esencial para el proceso de build del frontend (Vite, Tailwind). La versión 20.x es la LTS (Long-Term Support), lo que garantiza estabilidad. NPM se utiliza para la gestión de paquetes del frontend.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Composer</strong></td>
        <td style="padding: 12px 15px;">2.8.10</td>
        <td style="padding: 12px 15px;">
          <strong>Gestor de Dependencias de PHP:</strong> Estándar de facto, esencial para gestionar los paquetes de Laravel y sus dependencias (Spatie, Maatwebsite, etc.).
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Maatwebsite/Excel</strong></td>
        <td style="padding: 12px 15px;">3.1</td>
        <td style="padding: 12px 15px;">
          <strong>Exportación de Informes:</strong> Estándar de la comunidad Laravel para la exportación de datos. Abstrae la complejidad de PHPOffice/PhpSpreadsheet, permitiendo exportar vistas Blade o colecciones Eloquent directamente a XLSX, CSV, ODS o PDF.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Spatie/laravel-backup</strong></td>
        <td style="padding: 12px 15px;">8.x</td>
        <td style="padding: 12px 15px;">
          <strong>Confiabilidad de las Copias de Seguridad:</strong> Una solución superior a los scripts cron manuales, ya que gestiona todo el ciclo de vida de la copia de seguridad: programación, ejecución del dump de la base de datos, compresión, notificación por correo electrónico y limpieza de copias antiguas.
        </td>
      </tr>
    </tbody>
  </table>
</div>

---

## 🔒 Seguridad y Cifrado

La seguridad es un pilar central de NREduTech, implementando estándares modernos para la protección de datos.

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #444; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">Tema</th>
        <th style="padding: 12px 15px; text-align: left;">Implementación</th>
        <th style="padding: 12px 15px; text-align: left;">Justificación (¿Por qué es superior?)</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Hashing de Contraseñas</strong></td>
        <td style="padding: 12px 15px;"><strong>Argon2id</strong> (vía <code>config/hashing.php</code>)</td>
        <td style="padding: 12px 15px;">
          <strong>Resistencia a Hardware Especializado:</strong> Argon2id es el ganador de la <strong>Password Hashing Competition (2015)</strong> y el estándar recomendado por OWASP.
          <ul>
            <li><strong>Superior a Bcrypt:</strong> Bcrypt es resistente a los ataques de fuerza bruta, pero vulnerable a hardware especializado (GPUs).</li>
            <li><strong>Superior a scrypt:</strong> scrypt fue pionero en ser "memory-hard" (resistente a GPU), pero Argon2id es más robusto frente a una gama más amplia de ataques.</li>
            <li><strong>Superior a Argon2d/2i:</strong> La variante <strong>Argon2id</strong> es híbrida, combinando la resistencia a GPU de Argon2d con la resistencia a ataques de canal lateral (side-channel) de Argon2i, lo que la convierte en la opción más segura.</li>
          </ul>
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Cifrado de Sesión</strong></td>
        <td style="padding: 12px 15px;"><strong>AES-256-CBC</strong></td>
        <td style="padding: 12px 15px;">
          <strong>Estándar de la Industria:</strong> Utiliza cifrado simétrico fuerte para proteger los datos de sesión y las cookies de "recordarme". Esto impide que un atacante lea o falsifique el contenido de la sesión de un usuario, ya que no posee la clave secreta (<code>APP_KEY</code>) necesaria para descifrar los datos.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Protección de Formularios</strong></td>
        <td style="padding: 12px 15px;"><strong>Tokens CSRF</strong> (vía <code>@csrf</code> y Middleware)</td>
        <td style="padding: 12px 15px;">
          <strong>Prevención de Ataques:</strong> Garantiza que las solicitudes que modifican datos (<code>POST</code>, <code>PUT</code>, <code>DELETE</code>) solo puedan originarse dentro de la propia aplicación. Esto evita que un sitio malicioso externo engañe a un usuario autenticado para que realice acciones no deseadas (ej.: eliminar una programación).
        </td>
      </tr>
    </tbody>
  </table>
</div>

---

## 💡 Notas de Arquitectura y Curiosidades

* **Validación Desacoplada:** El proyecto hace un uso extensivo de *Form Requests* (ej.: `StoreUserRequest`, `StoreAppointmentRequest`). Esta es una *best practice* de Laravel que traslada toda la lógica de validación de datos fuera de los Controladores, haciéndolos más limpios, legibles y fáciles de probar.
* **Consultas Eficientes:** La funcionalidad de Informes (`ReportController`) utiliza *Model Scopes* (ej.: `scopeFiltroRecursos`, `scopeFiltroUsuarios`) definidos directamente en los Modelos. Esto hace que las consultas a la base de datos sean dinámicas, eficientes y reutilizables.
* **Seeders Listos para Producción:** El proyecto incluye *seeders* como `NreIratiSeeder`, que pueblan la base de datos con datos reales (municipios y escuelas del NRE de Irati), demostrando un enfoque en la implementación práctica.
* **Tiempo de Desarrollo:**
    * **Inicio:** 31/07/2025
    * **Finalización (v1.0):** 26/11/2025
    * **Total de Horas (aprox.):** 250 horas
    * **Total de días transcurridos:** 119 días

---

## 👨‍💻 Autor

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9;">
    <tr>
      <td style="padding: 20px; width: 100px; text-align: center;">
        <img src="https://avatars.githubusercontent.com/u/142981329?v=4" width="90" alt="Avatar de Victor" style="border-radius: 50%;">
      </td>
      <td style="padding: 20px; color: #333;">
        <strong style="font-size: 1.3em; color: #0169b4;">Victor Henrique Jesus Santiago</strong><br>
        Desarrollador Full Stack<br><br>
        📧 <a href="mailto:victorhenriquedejesussantiago@gmail.com" style="color: #0169b4; text-decoration: none;">victorhenriquedejesussantiago@gmail.com</a><br>
        👔 <a href="https://www.linkedin.com/in/victor-henrique-de-jesus-santiago/" style="color: #0169b4; text-decoration: none;">LinkedIn/victorhjsantiago</a><br>
        🐙 <a href="https://github.com/victorhjsantiago" style="color: #0169b4; text-decoration: none;">GitHub/victorhjsantiago</a>
      </td>
    </tr>
  </table>
</div>
