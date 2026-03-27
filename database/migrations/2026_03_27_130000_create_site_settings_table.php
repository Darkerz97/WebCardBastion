<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->string('site_tagline')->nullable();
            $table->string('home_kicker')->nullable();
            $table->string('home_headline');
            $table->text('home_description');
            $table->string('catalog_heading');
            $table->text('catalog_description');
            $table->string('benefit_one_title');
            $table->text('benefit_one_description');
            $table->string('benefit_two_title');
            $table->text('benefit_two_description');
            $table->string('benefit_three_title');
            $table->text('benefit_three_description');
            $table->string('announcement_text')->nullable();
            $table->timestamps();
        });

        DB::table('site_settings')->insert([
            'site_name' => 'Card Bastion',
            'site_tagline' => 'Boutique TCG + Player Hub',
            'home_kicker' => 'Card Bastion Store',
            'home_headline' => 'Cartas, accesorios y picks curados para jugadores que si cuidan su mesa.',
            'home_description' => 'Descubre un catalogo especializado de TCG con seleccion premium, novedades en rotacion y una experiencia de compra pensada para jugadores competitivos y coleccionistas.',
            'catalog_heading' => 'Explora la tienda con mejor contexto y menos ruido.',
            'catalog_description' => 'Filtra por categoria, busca cartas o productos clave y revisa una seleccion presentada con mejor jerarquia visual para compra rapida.',
            'benefit_one_title' => 'Seleccion enfocada en juego real',
            'benefit_one_description' => 'Productos organizados para que ubiques staples, accesorios y cartas utiles sin navegar una tienda caotica.',
            'benefit_two_title' => 'Tienda conectada con jugadores',
            'benefit_two_description' => 'El ecosistema de Card Bastion esta pensado para combinar compra, torneos y seguimiento de cuenta en una sola experiencia.',
            'benefit_three_title' => 'Proceso claro desde el primer clic',
            'benefit_three_description' => 'Filtros directos, fichas limpias y carrito visible para que la navegacion se sienta rapida, elegante y confiable.',
            'announcement_text' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
