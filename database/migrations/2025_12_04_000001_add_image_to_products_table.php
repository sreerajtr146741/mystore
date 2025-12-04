// database/migrations/2025_12_04_000001_add_image_to_products_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'discount_type')) {
                $table->enum('discount_type', ['percent','flat'])->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'discount_value')) {
                $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            }
            if (!Schema::hasColumn('products', 'is_discount_active')) {
                $table->boolean('is_discount_active')->default(false)->after('discount_value');
            }
            if (!Schema::hasColumn('products', 'category')) {
                $table->string('category')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // keep data safe; remove only if you want
            // $table->dropColumn(['image','discount_type','discount_value','is_discount_active','category']);
        });
    }
};
