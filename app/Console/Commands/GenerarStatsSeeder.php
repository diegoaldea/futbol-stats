<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// Comando temporal: lee categorías y estadísticas de la base actual (local)
// y genera un seeder con esos datos hardcodeados para poder cargarlos en Railway.
class GenerarStatsSeeder extends Command
{
    protected $signature = 'stats:generar-seeder';

    protected $description = 'Genera database/seeders/StatsSeeder.php con las categorías y estadísticas de la base actual';

    public function handle(): int
    {
        // Se respeta el orden de dependencia: primero categorías, después estadísticas.
        $categorias = DB::table('stat_categories')->orderBy('id')->get()
            ->map(fn ($fila) => (array) $fila)->all();

        $estadisticas = DB::table('stats')->orderBy('id')->get()
            ->map(fn ($fila) => (array) $fila)->all();

        $categoriasLiteral = var_export($categorias, true);
        $estadisticasLiteral = var_export($estadisticas, true);

        $contenido = <<<PHP
        <?php

        namespace Database\\Seeders;

        use Illuminate\\Database\\Seeder;
        use Illuminate\\Support\\Facades\\DB;

        // Seeder generado automáticamente con: php artisan stats:generar-seeder
        // Es idempotente: usa updateOrInsert por id, así que se puede correr varias
        // veces sin duplicar filas.
        class StatsSeeder extends Seeder
        {
            public function run(): void
            {
                // Primero las categorías, porque las estadísticas dependen de ellas (stat_category_id).
                \$categorias = {$categoriasLiteral};

                // Después las estadísticas.
                \$estadisticas = {$estadisticasLiteral};

                foreach (\$categorias as \$fila) {
                    DB::table('stat_categories')->updateOrInsert(['id' => \$fila['id']], \$fila);
                }

                foreach (\$estadisticas as \$fila) {
                    DB::table('stats')->updateOrInsert(['id' => \$fila['id']], \$fila);
                }
            }
        }

        PHP;

        file_put_contents(database_path('seeders/StatsSeeder.php'), $contenido);

        $this->info('StatsSeeder.php generado.');
        $this->line('stat_categories: '.count($categorias).' filas');
        $this->line('stats: '.count($estadisticas).' filas');

        return self::SUCCESS;
    }
}
