<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Municipio;
use App\Models\Escola;
use Illuminate\Support\Facades\DB;

class NreIratiSeeder extends Seeder
{
    /**
     *
     * @return void
     */
    public function run(): void
    {
        if (config('database.default') !== 'sqlite') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        Escola::query()->truncate();
        Municipio::query()->truncate();
        $municipios = [
            ['id_municipio' => 1, 'nome' => 'Irati'],
            ['id_municipio' => 2, 'nome' => 'Fernandes Pinheiro'],
            ['id_municipio' => 3, 'nome' => 'Guamiranga'],
            ['id_municipio' => 4, 'nome' => 'Imbituva'],
            ['id_municipio' => 5, 'nome' => 'Inácio Martins'],
            ['id_municipio' => 6, 'nome' => 'Mallet'],
            ['id_municipio' => 7, 'nome' => 'Rebouças'],
            ['id_municipio' => 8, 'nome' => 'Rio Azul'],
            ['id_municipio' => 9, 'nome' => 'Teixeira Soares'],
        ];
        Municipio::insert($municipios);
        $escolas = [
            // Irati (ID: 1)
            ['nome' => 'CE Antônio Dorigon', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE Profª Benedicta W. Oilveira', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE Florestal de Ed. Profissional', 'id_municipio' => 1, 'nivel_ensino' => 'escola_tecnica', 'tipo' => 'urbana'],
            ['nome' => 'CE Getúlio Vargas', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE João XXIII', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE João de Mattos Pessoa', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE Modelar N. S. das Graças', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE N. S. de Fátima', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE Parigot de Souza', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE Prof. José A. de Almeida', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE São Vicente de Paulo', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE Trajano Gracia', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE do Campo de Gonçalves Junior', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'EE Duque de Caxias', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'EE Eva Vanda S. B. Francisco', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'EE N. S. do Perpétuo Socorro', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'EE Rosa Zarpellon', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA de Irati', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            // Fernandes Pinheiro (ID: 2)
            ['nome' => 'CE Angaí', 'id_municipio' => 2, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE Dr. Afonso A. de Camargo', 'id_municipio' => 2, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            // Guamiranga (ID: 3)
            ['nome' => 'CE do Campo de Boa Vista', 'id_municipio' => 3, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE do Campo de Guamirim', 'id_municipio' => 3, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE Prof. Ovidio G. de Carvalho', 'id_municipio' => 3, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            // Imbituva (ID: 4)
            ['nome' => 'CE Alberto D. de Araújo', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE Antônio F. da Costa', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE Bento M. da Rocha Neto', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE do Campo de Mato Branco', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE do Campo de Vila Nova', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE Francisco J. Rebouças', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'EE Santa Gema Galgani', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            // Inácio Martins (ID: 5)
            ['nome' => 'CE Bibiana M. de Góes', 'id_municipio' => 5, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE do Campo de Góes Artigas', 'id_municipio' => 5, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE do Campo de S. Miguel do T.', 'id_municipio' => 5, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE do Campo de Sto. Antônio', 'id_municipio' => 5, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE Parigot de Souza', 'id_municipio' => 5, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'EE Quilombola Maria J. Ferreira', 'id_municipio' => 5, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            // Mallet (ID: 6)
            ['nome' => 'CE do Campo de Dorizon', 'id_municipio' => 6, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE do Campo de Fluviópolis', 'id_municipio' => 6, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE do Campo de Rio Claro', 'id_municipio' => 6, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE Nicolau Copérnico', 'id_municipio' => 6, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE Prof. Dario Vellozo', 'id_municipio' => 6, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            // Rebouças (ID: 7)
            ['nome' => 'CE Profª Chafica C. S. Saleh', 'id_municipio' => 7, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE Prof. J. F. da Mota', 'id_municipio' => 7, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE do Campo de Marmeleiro', 'id_municipio' => 7, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'EE Maria I. T. P. de Andrade', 'id_municipio' => 7, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            // Rio Azul (ID: 8)
            ['nome' => 'CE Dr. Chafic Cury', 'id_municipio' => 8, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE do Campo de Rio Azul', 'id_municipio' => 8, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE Pres. Costa e Silva', 'id_municipio' => 8, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE Prof. J. A. da R. P.', 'id_municipio' => 8, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'EE N. S. do Perpétuo Socorro', 'id_municipio' => 8, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            // Teixeira Soares (ID: 9)
            ['nome' => 'CE João C. da R. P.', 'id_municipio' => 9, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE do Campo de Angaí', 'id_municipio' => 9, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE do Campo de Guaraúna', 'id_municipio' => 9, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
        ];

        Escola::insert($escolas);

        if (config('database.default') !== 'sqlite') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}