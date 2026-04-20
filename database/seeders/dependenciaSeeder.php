<?php

namespace Database\Seeders;
use App\Models\Dependencia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DependenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Dependencia::insert([
            [
                'nombre' => 'Intendente',  //1
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => null,
            ],
            [
                'nombre' => 'Secretaria Privada',  //2
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 1,
            ],
            [
                'nombre' => 'Asesoria Letrada', //3
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 1,
            ],

            [
                'nombre' => 'Ente Descentralizado Vialidad Rural',  //4
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 1,
                ],

                [
                    'nombre' => 'PITA',   //5
                    'activa' => 1,
                    'id_direccion' => 1,
                    'id_dependencia_padre' => 1,
                    ],

                [
                    'nombre' => 'CRESTA',  //6
                    'activa' => 1,
                    'id_direccion' => 2,
                    'id_dependencia_padre' => 1,
                    ],

                    [
                                   'nombre' => 'Juzgados De Falta 1 y 2',  //7
                                   'activa' => 1,
                                   'id_direccion' => 13,
                                   'id_dependencia_padre' => 1,
                               ],


//***************** primera linea primera entidad ******************************** */
                        [
                            'nombre' => 'Jefatura De Gabinete', //8
                            'activa' => 1,
                            'id_direccion' => 1,
                            'id_dependencia_padre' => 1,
                        ],

//linea debajo de jefatura de gabinete

    [
                'nombre' => 'Secretaria Privada 2',   //9
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 8,
            ],

                [
                'nombre' => 'Subsecretaria De Gobierno Y Transporte',  //10
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 8,
            ],


            [
                'nombre' => 'Direccion De Cultura', //11
                'activa' => 1,
                'id_direccion' => 3,
                'id_dependencia_padre' => 8,
            ],
              [
                'nombre' => 'Museo Mulazzi', //12
                'activa' => 1,
                'id_direccion' => 10,
                'id_dependencia_padre' => 11,
            ],


            [
                'nombre' => 'Centro Cultural La Estacion', //13
                'activa' => 1,
                'id_direccion' => 3,
                'id_dependencia_padre' => 11,
            ],

 [
                'nombre' => 'Direccion De Deportes Y Juventud', //14
                'activa' => 1,
                'id_direccion' => 12,
                'id_dependencia_padre' => 8,
            ],

[
                'nombre' => 'Punto Digital',//15
                'activa' => 1,
                'id_direccion' => 12,
                'id_dependencia_padre' => 14,
            ],

            //delegaciones

    [
                'nombre' => 'Ente Descentralizado Claromeco Servicios Turisticos', //16
                'activa' => 1,
                'id_direccion' => 27,
                'id_dependencia_padre' => 8,
            ],


  [
                'nombre' => 'Delegacion De San Francisco De Bellocq', //17
                'activa' => 1,
                'id_direccion' => 7,
                'id_dependencia_padre' => 8,
            ],


     [
                'nombre' => 'Delegacion De Micaela Cascallares',//18
                'activa' => 1,
                'id_direccion' => 8,
                'id_dependencia_padre' => 8,
            ],
            [
                'nombre' => 'Delegacion De Copetonas',//19
                'activa' => 1,
                'id_direccion' => 9,
                'id_dependencia_padre' => 8,
            ],


    [
                'nombre' => 'Delegacion De Reta',//20
                'activa' => 1,
                'id_direccion' => 6,
                'id_dependencia_padre' => 8,
            ],

            [
                'nombre' => 'Lincalel',//21
                'activa' => 1,
                'id_direccion' => 5,
                'id_dependencia_padre' => 8,
            ],

              [
                'nombre' => 'San Mayol',//22
                'activa' => 1,
                'id_direccion' => 5,
                'id_dependencia_padre' => 8,
            ],

  [
                'nombre' => 'Delegacion De Orense',//23
                'activa' => 1,
                'id_direccion' => 5,
                'id_dependencia_padre' => 8,
            ],

[
                'nombre' => 'Balneario Orense',//24
                'activa' => 1,
                'id_direccion' => 5,
                'id_dependencia_padre' => 23,
            ],

             [
                'nombre' => 'Despacho General',  //25
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 8,
            ],

              [
                'nombre' => 'Mesa De Entradas',  //26
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 25,
            ],

       [
                'nombre' => 'Departamento De Recursos Humano De La Administración Central (RRHH)', //27
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 8,
            ],

     [
                'nombre' => 'Oficina De Información Al Consumidor (OMIC)', //28
                'activa' => 1,
                'id_direccion' => 11,
                'id_dependencia_padre' => 8,
            ],

[
                'nombre' => 'Desarrollo Territorial', //29
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 8,
            ],



            //***************** primera linea segunda entidad ***************************************

  [
                'nombre' => 'Secretaria De Hacienda', //30
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 1,
            ],

[
                'nombre' => 'Direccion De Politicas Tributarias', //31
                'activa' => 1,
                'id_direccion' => 11,
                'id_dependencia_padre' => 30,
            ],

  [
                'nombre' => 'Direccion De Finanzas', //32
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 30,
            ],
            [
                'nombre' => 'Unidad De Fiscalización',//33
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 32,
            ],


            [
                'nombre' => 'Oficina De Compras', //34
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 32 ,
            ],

    [
                'nombre' => 'Contaduría',//35
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 32,
            ],

  [
                'nombre' => 'Tesoreria',//36
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' =>  32,
            ],

   [
                'nombre' => 'Sistemas', //37
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 30,
            ],




 // ********************** primera linea tercera entidad ***********************************

 [
                'nombre' => 'Unidad De Impulso Para El Desarrollo Local Impulsar Tres Arroyos', //38
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 1,
            ],

  [
                'nombre' => 'Dirección De Turismo', //39
                'activa' => 1,
                'id_direccion' => 12,
                'id_dependencia_padre' => 38,
            ],

   [
                'nombre' => 'Direccion De Estrategia Productiva', //40
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 38,
            ],

   [
                'nombre' => 'Direccion De Produccion Y Sustentabilidad', //41
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' =>38,
            ],

               [
                'nombre' => 'Direccion De Industria, Comercio Y Emprendedurismo',//42
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 38,
            ],

   [
                'nombre' => 'Oficina De Empleo Y Capacitación', //43
                'activa' => 1,
                'id_direccion' => 15,
                'id_dependencia_padre' => 42,
            ],



 // **************************  primera linea  cuarta  entidad ****************************

  [
                'nombre' => 'Secretaria De Planeamiento Urbano', //44
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' => 1,
            ],

      [
                'nombre' => 'Subsecretaria De Gestión Ambiental', //45
                'activa' => 1,
                'id_direccion' => 24,
                'id_dependencia_padre' => 44,
            ],

     [
                'nombre' => 'Dirección Bromatología Y Zoonosis', //46
                'activa' => 1,
                'id_direccion' => 25,
                'id_dependencia_padre' => 45,
            ],

             [
                'nombre' => 'Secretaria De Higiene Urbana', //47
                'activa' => 1,
                'id_direccion' => 24,
                'id_dependencia_padre' =>44 ,
            ],

            [
                'nombre' => 'Predio De Disposicion Final Y Tratamiento De Residuos', //48
                'activa' => 1,
                'id_direccion' => 24,
                'id_dependencia_padre' =>47 ,
            ],

    [
                'nombre' => 'Subdireccion De Catastro',//49
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' => 44,
            ],


    [
                'nombre' => 'Direccion De Obras Sanitarias',//50
                'activa' => 1,
                'id_direccion' => 19,
                'id_dependencia_padre' => 44,
            ],

    [
                'nombre' => 'Direccion De Servicios Urbanos', //51
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' => 44,
            ],

    [
                'nombre' => 'Cementerio',//52
                'activa' => 1,
                'id_direccion' => 18,
                'id_dependencia_padre' => 51,
            ],

     [
                'nombre' => 'Departamento De Paseos Públicos',//53
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' => 51,
            ],

      [
                'nombre' => 'Direccion De Coordinacion Operativa',//54
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' => 44,
            ],

   [
                'nombre' => 'Electrotecnia', //55
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' => 44,
            ],

  [
                'nombre' => 'Obras Particulares', //56
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' => 44,
            ],



// ****************************** primera linea quinta entidad *****************************

   [
                'nombre' => 'Secretaria De Seguridad', //57
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 1,
            ],

 [
                'nombre' => 'Direccion Geneal De Coordinacion Operativa', //58
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 57,
            ],

  [
                'nombre' => 'Patrulla Urbana', //59
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 58,
            ],

            [
                'nombre' => 'Policia Comunal', //60
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 58,
            ],

 [
                'nombre' => 'Dirección De Tránsito E Inspección General', //61
                'activa' => 1,
                'id_direccion' => 20, // VER
                'id_dependencia_padre' => 58,
            ],

 [
                'nombre' => 'Cuerpo De Inspectores', //62
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 58,
            ],

  [
                'nombre' => 'Patrulla Municipal De Prevencion',//63
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 58,
            ],

   [
                'nombre' => 'Estacionamiento Medido', //64
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 58,
            ],

//
 [
                'nombre' => 'Direccion General De Coordinacion Administrativa', //65
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 57,
            ],

  [
                'nombre' => 'Centro De Monitoreo',//66
                'activa' => 1,
                'id_direccion' => 20, // VER
                'id_dependencia_padre' => 65,
            ],


            [
                'nombre' => 'Exposiciones Civiles',//67
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 65,
            ],
            [
                'nombre' => 'Defensa Civil',//68
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 65,
            ],

   [
                'nombre' => 'Aeródromo Público Provincial De Tres Arroyos "Teniente Ricardo Volponi"',//69
                'activa' => 1,
                'id_direccion' => 16,
                'id_dependencia_padre' => 57,
            ],


//********************************  primera linea sexta entidad  ************************************ */

  [
                'nombre' => 'Secretaria De Desarrollo Social', //70
                'activa' => 1,
                'id_direccion' => 22,
                'id_dependencia_padre' => 1,
            ],

   [
                'nombre' => 'Direccion De Acción Social',//71
                'activa' => 1,
                'id_direccion' => 23,
                'id_dependencia_padre' => 70,
            ],

  [
                'nombre' => 'Direccion De Mujeres Genero Y Diversidad', //72
                'activa' => 1,
                'id_direccion' => 22,
                'id_dependencia_padre' => 70,
            ],




//***************************** primera linea septima entidad *********************** */

[
                'nombre' => 'Secretaria De Salud', //73
                'activa' => 1,
                'id_direccion' => 25,
                'id_dependencia_padre' => 1,
            ],

[
                'nombre' => 'Ente Descentralizado Centro Municipal De Salud', //74
                'activa' => 1,
                'id_direccion' => 25,
                'id_dependencia_padre' => 73,
            ],

            [
                'nombre' => 'Direccion Tecnica', //75
                'activa' => 1,
                'id_direccion' => 25,
                'id_dependencia_padre' => 74,
            ],

[
                'nombre' => 'Direccion Administrativa', //76
                'activa' => 1,
                'id_direccion' => 25,
                'id_dependencia_padre' => 74,
            ],

            [
                'nombre' => 'Direccion De Atencion Primaria De La Salud', //77
                'activa' => 1,
                'id_direccion' => 25,
                'id_dependencia_padre' =>74 ,
            ],





//******************************* oficinas a reubicar **************************************************


            [
                'nombre' => 'Marcas Y Señales', //78
                'activa' => 1,
                'id_direccion' => 14,
                'id_dependencia_padre' => null,
            ],



            [
                'nombre' => 'Secretaria De Obras Públicas', //79
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' =>null,
            ],



            [
                'nombre' => 'IPS', //80
                'activa' => 1,
                'id_direccion' => 22,
                'id_dependencia_padre' =>null,
            ],



           [
                'nombre' => 'Parque Industrial',//81
                'activa' => 1,
                'id_direccion' => 28,
                'id_dependencia_padre' =>null,
            ],
  [
                'nombre' => 'Hormigonera Municipal',//82
                'activa' => 1,
                'id_direccion' => 28,
                'id_dependencia_padre' => null,
            ],



        ]);
    }
}
