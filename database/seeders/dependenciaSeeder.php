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
                'nombre' => 'Intendente',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => null,
            ],
            [
                'nombre' => 'Asesoria Letrada',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 1,
            ],
            [
                'nombre' => 'Entes Descentralizados',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 1,
            ],
            [
                'nombre' => 'Secretaria Privada',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 1,
            ],
            [
                'nombre' => 'Jefatura de Gabinete',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => null,
            ],
            [
                'nombre' => 'Coordinación de Comunicación Institucional',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'CRESTA',
                'activa' => 1,
                'id_direccion' => 2,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Cultura',
                'activa' => 1,
                'id_direccion' => 3,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Centro Cultural',
                'activa' => 1,
                'id_direccion' => 3,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Despacho General',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Deportes',
                'activa' => 1,
                'id_direccion' => 4,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Delegacion de Orense',
                'activa' => 1,
                'id_direccion' => 5,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Delegacion de Reta',
                'activa' => 1,
                'id_direccion' => 6,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Delegacion de San Francisco de Bellocq',
                'activa' => 1,
                'id_direccion' => 7,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Delegacion de Micaela Cascallares',
                'activa' => 1,
                'id_direccion' => 8,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Delegacion de Copetonas',
                'activa' => 1,
                'id_direccion' => 9,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Museo Mulazzi',
                'activa' => 1,
                'id_direccion' => 10,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Departamento de Recursos Humano de la Administración Central (RRHH)',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Oficina de Información al Consumidor (OMIC)',
                'activa' => 1,
                'id_direccion' => 11,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Dirección de Politicas de la Juventud',
                'activa' => 1,
                'id_direccion' => 12,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Punto Digital',
                'activa' => 1,
                'id_direccion' => 12,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Escrituraciones Sociales',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 5,
            ],
            [
                'nombre' => 'Secretaria de Hacienda',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => null,
            ],
            [
                'nombre' => 'Direccion de Politicas Tributarias',
                'activa' => 1,
                'id_direccion' => 11,
                'id_dependencia_padre' => 23,
            ],
            [
                'nombre' => 'Subdireccion de Servicios Informáticos',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 24,
            ],
            [
                'nombre' => 'Centro de Computos',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 25,
            ],
            [
                'nombre' => 'Comunicaciones',
                'activa' => 1,
                'id_direccion' => 11, // PREGUNTAR
                'id_dependencia_padre' => 25,
            ],
            [
                'nombre' => 'Subdirección de Recaudación',
                'activa' => 1,
                'id_direccion' => 11,
                'id_dependencia_padre' => 24,
            ],
            [
                'nombre' => 'Atencion Presencial, Remota y Logística',
                'activa' => 1,
                'id_direccion' => 11,
                'id_dependencia_padre' => 28,
            ],
            [
                'nombre' => 'Catastro',
                'activa' => 1,
                'id_direccion' => 11,
                'id_dependencia_padre' => 28,
            ],
            [
                'nombre' => 'Juzgados',
                'activa' => 1,
                'id_direccion' => 13,
                'id_dependencia_padre' => 28,
            ],
            [
                'nombre' => 'Marcas y Señales',
                'activa' => 1,
                'id_direccion' => 14,
                'id_dependencia_padre' => 28,
            ],
            [
                'nombre' => 'Automotores',
                'activa' => 1,
                'id_direccion' => 11,
                'id_dependencia_padre' => 28,
            ],
            [
                'nombre' => 'Subdireccion de Cobranzas y Fiscalización',
                'activa' => 1,
                'id_direccion' => 11,
                'id_dependencia_padre' => 24,
            ],
            [
                'nombre' => 'Fiscalización',
                'activa' => 1,
                'id_direccion' => 11,
                'id_dependencia_padre' => 34,
            ],
            [
                'nombre' => 'Gestión Cobranzas',
                'activa' => 1,
                'id_direccion' => 11,
                'id_dependencia_padre' => 34,
            ],
            [
                'nombre' => 'Dirección de Hacienda Finanzas',
                'activa' => 1,
                'id_direccion' => 11,
                'id_dependencia_padre' => 23 ,
            ],
            [
                'nombre' => 'Compras',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 37 ,
            ],
            [
                'nombre' => 'Contaduría',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 37 ,
            ],
            [
                'nombre' => 'Tesoreria',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 37 ,
            ],
            [
                'nombre' => 'Sub Tesoreria',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 37 ,
            ],
            [
                'nombre' => 'HCD',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => null,
            ],
            [
                'nombre' => 'Bloque Juntos por el Cambio',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 42,
            ],
            [
                'nombre' => 'Bloque Frente de Todos',
                'activa' => 1,
                'id_direccion' => 1,  // PREGUNTAR
                'id_dependencia_padre' => 42,
            ],
            [
                'nombre' => 'Bloque Movimiento Vecinal',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 42,
            ],
            [
                'nombre' => 'Secretaria de Desarrollo Económico, Ciencia y Tecnología',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => null,
            ],
            [
                'nombre' => 'Dirección de Turismo',
                'activa' => 1,
                'id_direccion' => 12,
                'id_dependencia_padre' => 46,
            ],
            [
                'nombre' => 'Oficina de Empleo y Capacitación',
                'activa' => 1,
                'id_direccion' => 15,
                'id_dependencia_padre' => 46,
            ],
            [
                'nombre' => 'Oficina de Industria y Comercio',
                'activa' => 1,
                'id_direccion' => 1,
                'id_dependencia_padre' => 46,
            ],
            [
                'nombre' => 'Aeródromo Público Provincial de Tres Arroyos "Teniente Ricardo Volponi"',
                'activa' => 1,
                'id_direccion' => 16,
                'id_dependencia_padre' => 46,
            ],
            [
                'nombre' => 'Secretaria de Obras Públicas',
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' => null,
            ],
            [
                'nombre' => 'Cementerio',
                'activa' => 1,
                'id_direccion' => 18,
                'id_dependencia_padre' => 51,
            ],
            [
                'nombre' => 'Electrotécnica',
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' => 51,
            ],
            [
                'nombre' => 'Departamento de Obras Sanitarias',
                'activa' => 1,
                'id_direccion' => 19,
                'id_dependencia_padre' => 51,
            ],
            [
                'nombre' => 'Obras Particulares',
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' => 51,
            ],
            [
                'nombre' => 'Departamento de Paseos Públicos',
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' => 51,
            ],
            [
                'nombre' => 'Planeamiento',
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' => 51,
            ],
            [
                'nombre' => 'Servicios Urbanos',
                'activa' => 1,
                'id_direccion' => 17,
                'id_dependencia_padre' => 51,
            ],
            [
                'nombre' => 'Secretaría de Seguridad',
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => null,
            ],
            [
                'nombre' => 'Dirección de Tránsito e Inspección General',
                'activa' => 1,
                'id_direccion' => 20, // VER
                'id_dependencia_padre' => 59,
            ],
            [
                'nombre' => 'Centro de Monitoreo',
                'activa' => 1,
                'id_direccion' => 20, // VER
                'id_dependencia_padre' => 60,
            ],
            [
                'nombre' => 'Cuerpo de Inspectores',
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 60,
            ],
            [
                'nombre' => 'Estacionamiento medido',
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 60,
            ],
            [
                'nombre' => 'Exposiciones Civiles',
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 60,
            ],
            [
                'nombre' => 'Defensa Civil',
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 60,
            ],
            [
                'nombre' => 'Licencia de Conducir',
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 60,
            ],
            [
                'nombre' => 'Patrulla Urbana',
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 60,
            ],
            [
                'nombre' => 'Policia Comunal',
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 60,
            ],
            [
                'nombre' => 'Transporte',
                'activa' => 1,
                'id_direccion' => 20,
                'id_dependencia_padre' => 60,
            ],
            [
                'nombre' => 'Terminal de Omnibus',
                'activa' => 1,
                'id_direccion' => 21,
                'id_dependencia_padre' => 60,
            ],
            [
                'nombre' => 'Secretaría de Desarrollo Social',
                'activa' => 1,
                'id_direccion' => 22,
                'id_dependencia_padre' => null,
            ],
            [
                'nombre' => 'Dirección de Acción Social',
                'activa' => 1,
                'id_direccion' => 23,
                'id_dependencia_padre' => 71,
            ],
            [
                'nombre' => 'Dirección de Mujeres, género y diversidad',
                'activa' => 1,
                'id_direccion' => 22,
                'id_dependencia_padre' => 71,
            ],
            [
                'nombre' => 'Emergencia Habitacional',
                'activa' => 1,
                'id_direccion' => 22,
                'id_dependencia_padre' => 71,
            ],
            [
                'nombre' => 'Niñez, Adolescencia y Familia',
                'activa' => 1,
                'id_direccion' => 22,
                'id_dependencia_padre' => 71,
            ],
            [
                'nombre' => 'IPS',
                'activa' => 1,
                'id_direccion' => 22,
                'id_dependencia_padre' => 71,
            ],
            [
                'nombre' => 'Secretaria de Gestión Ambiental',
                'activa' => 1,
                'id_direccion' => 24,
                'id_dependencia_padre' => null,
            ],
            [
                'nombre' => 'Dirreción de Higiene Veterinaria y Bromatología',
                'activa' => 1,
                'id_direccion' => 25,
                'id_dependencia_padre' => 77,
            ],
            [
                'nombre' => 'Secretaria de Salud y Prevención',
                'activa' => 1,
                'id_direccion' => 26,
                'id_dependencia_padre' => null,
            ],

        ]);
    }
}
