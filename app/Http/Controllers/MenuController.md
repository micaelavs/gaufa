Creación de menú con clases en PHP
Este es un ejemplo de cómo crear y estructurar un menú utilizando clases en PHP.

Primero, se crea una instancia de la clase Menu con un id y un nombre. Luego, se crean varias instancias de Menu para representar los elementos del menú, desde el primer nivel hasta el tercer nivel, como subelementos y sub-subelementos.

Luego, se agregan los subelementos y sub-subelementos al elemento padre correspondiente utilizando el método agregarHijo. Después, se establece el padre de cada subelemento y sub-subelemento utilizando el método setPadre.

Finalmente, se crea un array con el elemento principal del menú utilizando el método toArray y se almacena en la variable $elementos.

El método toArray se encarga de convertir cada instancia de la clase Menu y sus subelementos y sub-subelementos en un array anidado que se puede utilizar para construir el menú.

En resumen, este código utiliza clases para crear una estructura jerárquica de elementos de menú y subelementos, lo que facilita la construcción de menús complejos y personalizados en PHP.

Uso del código
Para utilizar este código, simplemente crea una instancia de la clase Menu para el elemento principal y luego crea las instancias de Menu para los subelementos y sub-subelementos correspondientes. Asegúrate de establecer correctamente el padre de cada subelemento y sub-subelemento utilizando el método setPadre.

Luego, utiliza el método toArray para convertir el elemento principal en un array que se puede utilizar para construir el menú en HTML.

      /**
       *EJEMPLO DE MENU
       */
      $menu = new Menu(1, 'Elemento 1');
      $subMenu1_1 = new Menu(2, 'Subelemento 1.1');
      $subMenu1_1_1 = new Menu(3, 'Sub-subelemento 1.1.1');
      $subMenu1_1_2 = new Menu(4, 'Sub-subelemento 1.1.2');
      $subMenu1_2 = new Menu(5, 'Subelemento 1.2');
      $subMenu1_2_1 = new Menu(6, 'Sub-subelemento 1.2.1');
      $subMenu1_2_2 = new Menu(7, 'Sub-subelemento 1.2.2');

      $menu->agregarHijo($subMenu1_1);
      $menu->agregarHijo($subMenu1_2);

      $subMenu1_1->agregarHijo($subMenu1_1_1);
      $subMenu1_1->agregarHijo($subMenu1_1_2);

      $subMenu1_2->agregarHijo($subMenu1_2_1);
      $subMenu1_2->agregarHijo($subMenu1_2_2);

      $subMenu1_1->setPadre($menu->id);
      $subMenu1_2->setPadre($menu->id);
      $subMenu1_1_1->setPadre($subMenu1_1->id);
      $subMenu1_1_2->setPadre($subMenu1_1->id);
      $subMenu1_2_1->setPadre($subMenu1_2->id);
      $subMenu1_2_2->setPadre($subMenu1_2->id);

      $elementos = [$menu->toArray()];