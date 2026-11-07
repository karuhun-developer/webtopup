<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('provider')->default('apigames');
            $table->string('identity_key', 64)->nullable();
        });

        DB::table('accounts')->chunkById(500, function ($accounts) {
            foreach ($accounts as $account) {
                $identity = hash('sha256', json_encode([
                    'apigames', trim($account->game), trim($account->uid), trim($account->server ?? ''),
                ]));
                DB::table('accounts')->where('identity_key', $identity)->delete();
                DB::table('accounts')->where('id', $account->id)->update([
                    'identity_key' => $identity,
                    'server' => trim($account->server ?? ''),
                ]);
            }
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->unique('identity_key');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropUnique(['identity_key']);
            $table->dropColumn(['provider', 'identity_key']);
        });
    }
};
