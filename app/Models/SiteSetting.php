<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'site_tagline',
        'home_kicker',
        'home_headline',
        'home_description',
        'catalog_heading',
        'catalog_description',
        'benefit_one_title',
        'benefit_one_description',
        'benefit_two_title',
        'benefit_two_description',
        'benefit_three_title',
        'benefit_three_description',
        'announcement_text',
        'social_heading',
        'social_description',
        'facebook_url',
        'facebook_embed',
        'instagram_url',
        'instagram_embed',
        'tiktok_url',
        'tiktok_embed',
    ];

    public static function defaults(): array
    {
        return [
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
            'social_heading' => 'Sigue la comunidad Card Bastion en tiempo real.',
            'social_description' => 'Conecta Facebook, Instagram y TikTok para mostrar albumes, publicaciones recientes y accesos directos a tus perfiles oficiales.',
            'facebook_url' => null,
            'facebook_embed' => null,
            'instagram_url' => null,
            'instagram_embed' => null,
            'tiktok_url' => null,
            'tiktok_embed' => null,
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            static::defaults(),
        );
    }
}
