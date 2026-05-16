<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CentrosAgendaSeeder extends Seeder
{
    public function run(): void
    {
        $centros = [
            ['id' => 14, 'nombre' => 'BIOLOGICOS ESPECIALIZADOS CUAUHTEMOC',        'direccion' => 'Alvaro Obregón 121-7, Roma Norte, Cuauhtémoc, CDMX 06700'],
            ['id' => 15, 'nombre' => 'BIOLOGICOS ESPECIALIZADOS NUEVO LEON',         'direccion' => 'Av. Miguel Hidalgo 2030 Pte, Obispado, Monterrey, NL 64060'],
            ['id' => 16, 'nombre' => 'BIOLOGICOS ESPECIALIZADOS GUADALAJARA',        'direccion' => 'Miguel Lerdo de Tejada 2218-A, Lafayette, Guadalajara, Jal 06700'],
            ['id' => 17, 'nombre' => 'CENTRO DE REUMATOLOGIA E INFUSIÓN ROMA',      'direccion' => 'Sonora 119, Roma Norte, Cuauhtémoc, CDMX 06700'],
            ['id' => 18, 'nombre' => 'CLIDITER CUAUHTEMOC',                          'direccion' => 'Durango 69, Roma, Cuauhtémoc, CDMX 06700'],
            ['id' => 19, 'nombre' => 'INFUZONE ALVARO OBREGON',                      'direccion' => 'Sur 132-118 Int 103, Las Américas, Álvaro Obregón, CDMX 01120'],
            ['id' => 20, 'nombre' => 'ONCARE TREATMENT CENTER NAPOLES',              'direccion' => 'Viad. Río Becerra 27, Nápoles, Benito Juárez, CDMX 03810'],
            ['id' => 21, 'nombre' => 'ONCOMED DEL VALLE SUR',                        'direccion' => 'San Francisco 1634, Del Valle Sur, Benito Juárez, CDMX 03100'],
            ['id' => 23, 'nombre' => 'GLOBAL ONCOLOGY ROMA',                         'direccion' => 'Frontera 153, Roma Norte, Cuauhtémoc, CDMX 06700'],
            ['id' => 24, 'nombre' => 'GLOBAL ONCOLOGY COYOACAN',                     'direccion' => 'Miguel Ángel de Quevedo 773, San Francisco, Coyoacán, CDMX 04200'],
            ['id' => 25, 'nombre' => 'GLOBAL ONCOLOGY TLALPAN',                      'direccion' => 'Camino a Santa Teresa 1055-S Int 1192, Héroes de Padierna, Tlalpan, CDMX 10700'],
            ['id' => 26, 'nombre' => 'GLOBAL ONCOLOGY CUAJIMALPA',                   'direccion' => 'Juárez 21-B09, Cuajimalpa de Morelos, CDMX 05000'],
            ['id' => 27, 'nombre' => 'SETRAS CHIHUAHUA',                             'direccion' => 'Av. Antonio de Montes 3714, Parques de San Felipe, Chihuahua, Chih 31203'],
            ['id' => 28, 'nombre' => 'GLOBAL ONCOLOGY GDL — LADRÓN DE GUEVARA',     'direccion' => 'Av. México 2472, Ladrón de Guevara, Guadalajara, Jal 44650'],
            ['id' => 29, 'nombre' => 'GLOBAL ONCOLOGY GDL — COUNTRY',               'direccion' => 'Av. Circunvalación Jorge Álvarez del Castillo 1558, Lomas de Country, Guadalajara, Jal 44610'],
            ['id' => 30, 'nombre' => 'GLOBAL ONCOLOGY MTY',                          'direccion' => 'Av. Cto. Frida Kahlo 180 Piso 7, Valle Oriente, San Pedro Garza García, NL 66260'],
            ['id' => 31, 'nombre' => 'GLOBAL ONCOLOGY PUEBLA',                       'direccion' => '7 Sur 415, Alpha 2, Puebla, Pue 72424'],
            ['id' => 32, 'nombre' => 'RENACER CENTRO DE INFUSION DE QUIMIOTERAPIA', 'direccion' => 'Av. Huayacán SM311 MZ30 L03, Álamos 1, Benito Juárez, QRoo 77533'],
            ['id' => 33, 'nombre' => 'ONCOMED QUERETARO',                            'direccion' => 'Blvd. Bernardo Quintana Arrioja 4060, San Pablo, Querétaro, Qro 76125'],
            ['id' => 34, 'nombre' => 'UNIDAD MÉDICA ONCO-HEMATOLÓGICA UMO PUEBLA',  'direccion' => '7 Sur 4515, Alpha 2, Puebla, Pue 72424'],
            ['id' => 35, 'nombre' => 'RED OSMO OAXACA',                              'direccion' => 'Humboldt 302, Centro, Oaxaca de Juárez, Oax 68000'],
            ['id' => 36, 'nombre' => 'RED OSMO MERIDA',                              'direccion' => 'Calle 23 #112, México, Mérida, Yuc 97125'],
            ['id' => 37, 'nombre' => 'CIMA AGUASCALIENTES',                          'direccion' => 'Av. Independencia 2130-A, Trojes de Alonso, Aguascalientes, Ags 20116'],
            ['id' => 38, 'nombre' => 'ONCOLOGICO POTOSINO',                          'direccion' => 'La Mora 139, Fracc. del Parque, San Luis Potosí, SLP 78209'],
            ['id' => 39, 'nombre' => 'CHOP TOLUCA',                                  'direccion' => 'Fernando Quiroz 416, Federal, Toluca, EdoMex 50120'],
            ['id' => 40, 'nombre' => 'CENTRO HEMATOLÓGICO HOPE SLP',                'direccion' => 'Real de Lomas 150, Las Lomas 4a Secc, San Luis Potosí, SLP 78216'],
            ['id' => 41, 'nombre' => 'CENTRO HEMATOLÓGICO HOPE GDL',                'direccion' => 'México 2582, Ladrón de Guevara, Guadalajara, Jal 44600'],
            ['id' => 42, 'nombre' => 'NOVOINFUSE SATELITE',                          'direccion' => 'Cto. de Médicos 10 Cons. 15, Cd. Satélite, Naucalpan, EdoMex 53100'],
            ['id' => 43, 'nombre' => 'IDeCSa',                                       'direccion' => 'Viaducto Tlalpan 1013-A, Polotlan, Tlalpan, CDMX 14090'],
            ['id' => 44, 'nombre' => 'IPHARMA',                                      'direccion' => 'Celaya 322, Mitras, Monterrey, NL 64460'],
            ['id' => 45, 'nombre' => 'GRUPO INTEPRO',                                'direccion' => 'Av. Guadalupe 4819, UNIVA, Zapopan, Jal 45034'],
            ['id' => 46, 'nombre' => 'ATRYS HEALTH',                                 'direccion' => 'Ave. Ferrocarril Central Col. 709, Los Laureles 1a Secc, Celaya, Gto 38020'],
        ];

        foreach ($centros as $centro) {
            DB::table('centros_agenda')->insert([
                'id'         => $centro['id'],
                'nombre'     => $centro['nombre'],
                'direccion'  => $centro['direccion'],
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ajustar el AUTO_INCREMENT para que no colisione con los IDs legacy
        DB::statement('ALTER TABLE centros_agenda AUTO_INCREMENT = 100');
    }
}