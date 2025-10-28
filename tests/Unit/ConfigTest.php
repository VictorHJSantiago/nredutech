<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class ConfigTest extends TestCase
{
    /** @test */
    public function timezone_da_aplicacao_esta_correta()
    {
        $this->assertEquals('America/Sao_Paulo', Config::get('app.timezone'));
    }

    /** @test */
    public function driver_de_backup_padrao_esta_configurado()
    {
        $this->assertContains('backups_local', Config::get('backup.backup.destination.disks'));
    }

    /** @test */
    public function driver_de_autenticacao_padrao_e_eloquent()
    {
        $this->assertEquals('usuarios', Config::get('auth.providers.users.model'));
    }

    /** @test */
    public function driver_de_hash_padrao_e_bcrypt()
    {
        $this->assertEquals('bcrypt', Config::get('hashing.driver'));
    }

    /** @test */
    public function bcrypt_rounds_esta_configurado()
    {
        $this->assertIsInt(Config::get('hashing.bcrypt.rounds'));
        $this->assertGreaterThanOrEqual(10, Config::get('hashing.bcrypt.rounds')); // Valor seguro
    }
}