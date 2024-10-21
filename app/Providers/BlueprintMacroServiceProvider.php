<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class BlueprintMacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blueprint::macro('createdBy', function () {
            $this->uuid('created_by')->index()->nullable();
            $this->uuid('updated_by')->index()->nullable();
            $this->uuid('deleted_by')->index()->nullable();

            $this->foreign('created_by')->references('uuid')->on('users')->onDelete('set null');
            $this->foreign('updated_by')->references('uuid')->on('users')->onDelete('set null');
            $this->foreign('deleted_by')->references('uuid')->on('users')->onDelete('set null');
        });

        Blueprint::macro('dropCreatedBy', function () {
            $this->dropForeign(['created_by']);
            $this->dropForeign(['updated_by']);
            $this->dropForeign(['deleted_by']);
            $this->dropColumn(['created_by', 'updated_by', 'deleted_by']);
        });
    }
}
