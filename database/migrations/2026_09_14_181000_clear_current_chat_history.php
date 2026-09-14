<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('chats')->delete();
        DB::table('conversations')->delete();
    }

    public function down(): void
    {
        // Os registros excluídos não podem ser recuperados automaticamente.
    }
};
