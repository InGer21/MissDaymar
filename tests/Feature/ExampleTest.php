<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * La raíz del sitio redirige al panel de administración
     * (ver routes/web.php). Antes este test esperaba un 200 heredado del
     * esqueleto de Laravel y fallaba siempre.
     */
    public function test_the_root_redirects_to_the_admin_panel(): void
    {
        $this->get('/')->assertRedirect('/admin');
    }
}
