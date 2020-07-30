# About VAMOS

VAMOS aims to promote access to information about sexual and reproductive health services and to encourage citizen participation to improve these services’ quality.

You will be able to find places that provide the following services in an easy and fast way, and then qualify their support’s quality:

- Condoms distribution.
- Contraceptives distribution.
- HIV and other Sexually Transmitted Infections testing.
- Early detection of breast cancer and cervical cancer.
- Reproductive and sexual health services.
- Legal abortion.
- VAMOS is an open source platform that works from any internet-connected device, and its use is anonymous and free. You can also download it as an Android app from its Play Store.

- - -

# Acerca de VAMOS

VAMOS tiene como objetivo promover el acceso a la información sobre los servicios de salud sexual y reproductiva y alentar la participación ciudadana para mejorar la calidad de estos servicios.

Podrá encontrar lugares que ofrecen los siguientes servicios de una manera fácil y rápida, y luego calificar la calidad de su soporte:

- Distribución de condones.
- Distribución de anticonceptivos.
- VIH y otras pruebas de infecciones de transmisión sexual.
- Detección temprana de cáncer de mama y cáncer de cuello uterino.
- Servicios de salud reproductiva y sexual.
- Aborto legal.
- VAMOS es una plataforma de código abierto que funciona desde cualquier dispositivo conectado a Internet, y su uso es anónimo y gratuito. También puedes descargarlo como una aplicación de Android desde Play Store.

# Instrucciones para la instalación del sistema

1. Clonar este repositorio al repositorio local de trabajo.
2. Instalar dependencias necesarias para el entorno de desarrollo: php, nginx, mysql. También se puede realizar utilizando una máquina virtual (sobretodo en entornos como Windows) para mayor seguridad y facilidad, ya que no se requiere instalar las dependecias y cualquier problema se descarta el box, su uso recomendado. Ver laravel Homestead para más información https://laravel.com/docs/5.7/homestead.
3. Crear el archivo .env en la carpeta raíz, necesario para asignar las variables de entorno del sistema.
4. Crear la base de datos. Para esto, simplemente importar el último dump de producción disponible con alguna herramienta de administración de mysql. No hay necesidad de crear tablas, configurar la BD, sólo correr el script. Este paso es importante porque los dumps ya contienen algunas optimizaciones en la BD. Verificar si el script crea o no a priori la BD, sino debe crearse el schema primero.
5. Servir (php serve) o levantar la máquina virtual.
6. Probar!
