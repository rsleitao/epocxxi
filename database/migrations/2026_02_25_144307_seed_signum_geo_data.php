<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $path = database_path('data/signum_geo_data.sql');
        if (! File::exists($path)) {
            return;
        }

        $sql = File::get($path);
        // Remove comment lines
        $sql = preg_replace('/^--.*$/m', '', $sql);
        // Split by ");" followed by newline to get full INSERT statements (semicolon alone would cut off the closing paren)
        $parts = preg_split('/\);\s*\n/', $sql);
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '' || stripos($part, 'INSERT INTO') !== 0) {
                continue;
            }
            $statement = preg_replace('/\);?\s*$/', '', $part).');';
            DB::unprepared($statement);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('freguesias')->truncate();
        DB::table('concelhos')->truncate();
        DB::table('distritos')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
