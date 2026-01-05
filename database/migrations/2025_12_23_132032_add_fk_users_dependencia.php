public function up(): void
{
Schema::table('users', function (Blueprint $table) {
$table->foreign('id_dependencia')
->references('id')
->on('dependencias')
->onDelete('cascade');
});
}

public function down(): void
{
Schema::table('users', function (Blueprint $table) {
$table->dropForeign(['id_dependencia']);
});
}
