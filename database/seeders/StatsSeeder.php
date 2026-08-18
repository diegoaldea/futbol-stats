<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// Seeder generado automáticamente con: php artisan stats:generar-seeder
// Es idempotente: usa updateOrInsert por id, así que se puede correr varias
// veces sin duplicar filas.
class StatsSeeder extends Seeder
{
    public function run(): void
    {
        // Primero las categorías, porque las estadísticas dependen de ellas (stat_category_id).
        $categorias = array (
  0 => 
  array (
    'id' => 6,
    'name' => 'Pases',
    'description' => NULL,
    'created_at' => '2026-06-10 20:14:02',
    'updated_at' => '2026-06-10 20:14:02',
  ),
  1 => 
  array (
    'id' => 7,
    'name' => 'Ataque',
    'description' => NULL,
    'created_at' => '2026-06-10 20:14:12',
    'updated_at' => '2026-06-10 20:14:12',
  ),
  2 => 
  array (
    'id' => 8,
    'name' => 'Defensa',
    'description' => NULL,
    'created_at' => '2026-06-10 20:14:16',
    'updated_at' => '2026-06-10 20:14:16',
  ),
  3 => 
  array (
    'id' => 9,
    'name' => 'Errores',
    'description' => NULL,
    'created_at' => '2026-06-10 20:14:20',
    'updated_at' => '2026-06-10 20:14:20',
  ),
  4 => 
  array (
    'id' => 10,
    'name' => 'Bonificador',
    'description' => NULL,
    'created_at' => '2026-06-30 20:16:47',
    'updated_at' => '2026-06-30 20:16:47',
  ),
  5 => 
  array (
    'id' => 11,
    'name' => 'Resultado',
    'description' => NULL,
    'created_at' => '2026-06-30 20:20:08',
    'updated_at' => '2026-06-30 20:20:13',
  ),
  6 => 
  array (
    'id' => 12,
    'name' => 'Ejecucion',
    'description' => NULL,
    'created_at' => '2026-07-08 21:50:08',
    'updated_at' => '2026-07-08 21:50:08',
  ),
);

        // Después las estadísticas.
        $estadisticas = array (
  0 => 
  array (
    'id' => 19,
    'name' => 'Pase progresivo',
    'description' => 'Avanza hacia el arco rival, supera líneas',
    'stat_category_id' => 6,
    'points' => '0.15',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:18:34',
    'updated_at' => '2026-07-10 01:48:28',
  ),
  1 => 
  array (
    'id' => 20,
    'name' => 'Buen pase',
    'description' => 'Pase de calidad excepcional o en situación difícil',
    'stat_category_id' => 6,
    'points' => '0.15',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:19:36',
    'updated_at' => '2026-06-10 20:19:36',
  ),
  2 => 
  array (
    'id' => 21,
    'name' => 'Mal pase',
    'description' => 'Error claro o en posición peligrosa',
    'stat_category_id' => 6,
    'points' => '-0.20',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:22:00',
    'updated_at' => '2026-07-08 21:04:21',
  ),
  3 => 
  array (
    'id' => 22,
    'name' => 'Ocasión creada',
    'description' => 'Pase que genera una chance de gol clara',
    'stat_category_id' => 11,
    'points' => '0.25',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:22:21',
    'updated_at' => '2026-06-30 20:20:28',
  ),
  4 => 
  array (
    'id' => 24,
    'name' => 'Pase incompleto',
    'description' => 'No llega al receptor, corta el ataque',
    'stat_category_id' => 6,
    'points' => '-0.10',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:23:50',
    'updated_at' => '2026-07-08 21:06:00',
  ),
  5 => 
  array (
    'id' => 25,
    'name' => 'Asistencia',
    'description' => 'Pase directo al gol',
    'stat_category_id' => 6,
    'points' => '0.75',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:24:10',
    'updated_at' => '2026-06-10 20:24:10',
  ),
  6 => 
  array (
    'id' => 26,
    'name' => 'Gol',
    'description' => 'Gol convertido.',
    'stat_category_id' => 7,
    'points' => '1.20',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:24:35',
    'updated_at' => '2026-06-22 01:44:21',
  ),
  7 => 
  array (
    'id' => 27,
    'name' => 'Disparo afuera',
    'description' => 'Tiro que no va al arco',
    'stat_category_id' => 7,
    'points' => '0.00',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:24:48',
    'updated_at' => '2026-06-10 20:24:48',
  ),
  8 => 
  array (
    'id' => 28,
    'name' => 'Gambeta exitosa',
    'description' => 'Superar al rival solo',
    'stat_category_id' => 7,
    'points' => '0.20',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:25:26',
    'updated_at' => '2026-06-10 20:25:26',
  ),
  9 => 
  array (
    'id' => 29,
    'name' => 'Disparo al arco',
    'description' => 'Tiro entre los tres palos',
    'stat_category_id' => 7,
    'points' => '0.20',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:25:47',
    'updated_at' => '2026-06-29 04:06:59',
  ),
  10 => 
  array (
    'id' => 30,
    'name' => 'Gran ocasión fallida',
    'description' => 'Mano a mano o remate solo sin convertir',
    'stat_category_id' => 7,
    'points' => '-0.30',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:26:10',
    'updated_at' => '2026-06-10 20:26:10',
  ),
  11 => 
  array (
    'id' => 31,
    'name' => 'Gambeta fallida',
    'description' => 'Intenta la gambeta y lo pierde',
    'stat_category_id' => 7,
    'points' => '-0.10',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:28:24',
    'updated_at' => '2026-06-10 20:28:24',
  ),
  12 => 
  array (
    'id' => 32,
    'name' => 'Recuperación',
    'description' => 'Gana el balón en cualquier zona',
    'stat_category_id' => 8,
    'points' => '0.20',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:28:50',
    'updated_at' => '2026-06-10 20:28:50',
  ),
  13 => 
  array (
    'id' => 33,
    'name' => 'Duelo ganado',
    'description' => 'Gana el 1 vs 1 defensivo',
    'stat_category_id' => 8,
    'points' => '0.15',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:29:10',
    'updated_at' => '2026-06-10 20:29:10',
  ),
  14 => 
  array (
    'id' => 34,
    'name' => 'Bloqueo',
    'description' => 'Bloquea un disparo con el cuerpo',
    'stat_category_id' => 8,
    'points' => '0.15',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:29:36',
    'updated_at' => '2026-06-10 20:29:36',
  ),
  15 => 
  array (
    'id' => 35,
    'name' => 'Salvada épica',
    'description' => 'Para excepcional, evita el gol de forma destacada',
    'stat_category_id' => 8,
    'points' => '0.50',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:30:36',
    'updated_at' => '2026-06-10 20:30:36',
  ),
  16 => 
  array (
    'id' => 36,
    'name' => 'Falta cometida',
    'description' => 'Comete una infracción',
    'stat_category_id' => 8,
    'points' => '-0.10',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:30:55',
    'updated_at' => '2026-07-10 02:32:34',
  ),
  17 => 
  array (
    'id' => 37,
    'name' => 'Recuperación alta',
    'description' => 'Gana el balón en campo rival',
    'stat_category_id' => 8,
    'points' => '0.30',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:31:11',
    'updated_at' => '2026-06-10 20:31:11',
  ),
  18 => 
  array (
    'id' => 38,
    'name' => 'Intercepción',
    'description' => 'Corta un pase rival',
    'stat_category_id' => 8,
    'points' => '0.20',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:31:31',
    'updated_at' => '2026-06-10 20:31:31',
  ),
  19 => 
  array (
    'id' => 39,
    'name' => 'Presión',
    'description' => 'Presiona activamente al poseedor',
    'stat_category_id' => 8,
    'points' => '0.20',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:32:32',
    'updated_at' => '2026-06-10 20:32:32',
  ),
  20 => 
  array (
    'id' => 40,
    'name' => 'Gol evitado',
    'description' => 'Despeja bajo la línea o acción heroica',
    'stat_category_id' => 8,
    'points' => '0.35',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:33:01',
    'updated_at' => '2026-06-10 20:33:01',
  ),
  21 => 
  array (
    'id' => 41,
    'name' => 'Atajada',
    'description' => 'Para un disparo dentro del arco',
    'stat_category_id' => 8,
    'points' => '0.20',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:34:20',
    'updated_at' => '2026-06-21 01:44:12',
  ),
  22 => 
  array (
    'id' => 42,
    'name' => 'Pérdida de posesión',
    'description' => 'Pierde el balón',
    'stat_category_id' => 9,
    'points' => '-0.10',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:34:51',
    'updated_at' => '2026-07-08 21:30:25',
  ),
  23 => 
  array (
    'id' => 43,
    'name' => 'Burrada no grave',
    'description' => 'Error importante sin consecuencia directa',
    'stat_category_id' => 9,
    'points' => '-0.20',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:35:09',
    'updated_at' => '2026-06-10 20:35:09',
  ),
  24 => 
  array (
    'id' => 44,
    'name' => 'Mala resolución',
    'description' => 'Decisión equivocada en buena posición',
    'stat_category_id' => 9,
    'points' => '-0.25',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:35:30',
    'updated_at' => '2026-07-08 21:29:46',
  ),
  25 => 
  array (
    'id' => 45,
    'name' => 'Burrada histórica',
    'description' => 'Error importante causa de la habilidad del jugador',
    'stat_category_id' => 9,
    'points' => '-0.40',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:36:32',
    'updated_at' => '2026-06-10 20:36:32',
  ),
  26 => 
  array (
    'id' => 46,
    'name' => 'Error de gol',
    'description' => 'Falla directamente en jugada de gol en contra',
    'stat_category_id' => 9,
    'points' => '-0.60',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-10 20:36:47',
    'updated_at' => '2026-06-10 20:36:47',
  ),
  27 => 
  array (
    'id' => 47,
    'name' => 'Despeje',
    'description' => 'alejar la pelota fuera del peligro',
    'stat_category_id' => 8,
    'points' => '0.10',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-12 07:44:52',
    'updated_at' => '2026-06-12 07:44:52',
  ),
  28 => 
  array (
    'id' => 48,
    'name' => 'Caño',
    'description' => NULL,
    'stat_category_id' => 7,
    'points' => '0.20',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-12 07:46:05',
    'updated_at' => '2026-06-12 07:46:05',
  ),
  29 => 
  array (
    'id' => 49,
    'name' => 'Buen control',
    'description' => NULL,
    'stat_category_id' => 12,
    'points' => '0.15',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-12 17:19:06',
    'updated_at' => '2026-07-08 21:50:21',
  ),
  30 => 
  array (
    'id' => 50,
    'name' => 'Conducción progresiva',
    'description' => 'Avanza una distancia significativa',
    'stat_category_id' => 7,
    'points' => '0.15',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-20 06:57:41',
    'updated_at' => '2026-06-20 06:58:16',
  ),
  31 => 
  array (
    'id' => 51,
    'name' => 'Gol en contra',
    'description' => NULL,
    'stat_category_id' => 9,
    'points' => '-1.00',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-20 07:11:20',
    'updated_at' => '2026-06-20 07:11:20',
  ),
  32 => 
  array (
    'id' => 52,
    'name' => 'Ejecución excepcional',
    'description' => 'Bonificación que se suma a una acción cuando la ejecución técnica fue destacada',
    'stat_category_id' => 10,
    'points' => '0.10',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-06-30 20:16:56',
    'updated_at' => '2026-06-30 20:16:56',
  ),
  33 => 
  array (
    'id' => 53,
    'name' => 'Pase',
    'description' => NULL,
    'stat_category_id' => 6,
    'points' => '0.05',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-07-08 20:19:06',
    'updated_at' => '2026-07-08 20:19:06',
  ),
  34 => 
  array (
    'id' => 54,
    'name' => 'Mal control',
    'description' => NULL,
    'stat_category_id' => 12,
    'points' => '-0.15',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-07-08 21:49:30',
    'updated_at' => '2026-07-08 21:50:28',
  ),
  35 => 
  array (
    'id' => 55,
    'name' => 'Remate',
    'description' => NULL,
    'stat_category_id' => 7,
    'points' => '0.05',
    'is_global' => 1,
    'user_id' => NULL,
    'created_at' => '2026-07-09 07:25:24',
    'updated_at' => '2026-07-09 07:25:24',
  ),
);

        foreach ($categorias as $fila) {
            DB::table('stat_categories')->updateOrInsert(['id' => $fila['id']], $fila);
        }

        foreach ($estadisticas as $fila) {
            DB::table('stats')->updateOrInsert(['id' => $fila['id']], $fila);
        }
    }
}
