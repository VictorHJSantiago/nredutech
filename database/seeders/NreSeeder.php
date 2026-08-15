<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Municipio;
use App\Models\Escola;
use Illuminate\Support\Facades\DB;

class NreSeeder extends Seeder
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
            ['id_municipio' => 1, 'nome' => 'Abatiá'],
            ['id_municipio' => 2, 'nome' => 'Adrianópolis'],
            ['id_municipio' => 3, 'nome' => 'Agudos do Sul'],
            ['id_municipio' => 4, 'nome' => 'Almirante Tamandaré'],
            ['id_municipio' => 5, 'nome' => 'Altamira do Paraná'],
            ['id_municipio' => 6, 'nome' => 'Alto Paraíso'],
            ['id_municipio' => 7, 'nome' => 'Alto Paraná'],
            ['id_municipio' => 8, 'nome' => 'Alto Piquiri'],
            ['id_municipio' => 9, 'nome' => 'Altônia'],
            ['id_municipio' => 10, 'nome' => 'Alvorada do Sul'],
            ['id_municipio' => 11, 'nome' => 'Amaporã'],
            ['id_municipio' => 12, 'nome' => 'Ampére'],
            ['id_municipio' => 13, 'nome' => 'Anahy'],
            ['id_municipio' => 14, 'nome' => 'Andirá'],
            ['id_municipio' => 15, 'nome' => 'Ângulo'],
            ['id_municipio' => 16, 'nome' => 'Antonina'],
            ['id_municipio' => 17, 'nome' => 'Antônio Olinto'],
            ['id_municipio' => 18, 'nome' => 'Apucarana'],
            ['id_municipio' => 19, 'nome' => 'Arapongas'],
            ['id_municipio' => 20, 'nome' => 'Arapoti'],
            ['id_municipio' => 21, 'nome' => 'Arapuã'],
            ['id_municipio' => 22, 'nome' => 'Araruna'],
            ['id_municipio' => 23, 'nome' => 'Araucária'],
            ['id_municipio' => 24, 'nome' => 'Ariranha do Ivaí'],
            ['id_municipio' => 25, 'nome' => 'Assaí'],
            ['id_municipio' => 26, 'nome' => 'Assis Chateaubriand'],
            ['id_municipio' => 27, 'nome' => 'Astorga'],
            ['id_municipio' => 28, 'nome' => 'Atalaia'],
            ['id_municipio' => 29, 'nome' => 'Balsa Nova'],
            ['id_municipio' => 30, 'nome' => 'Bandeirantes'],
            ['id_municipio' => 31, 'nome' => 'Barbosa Ferraz'],
            ['id_municipio' => 32, 'nome' => 'Barra do Jacaré'],
            ['id_municipio' => 33, 'nome' => 'Barracão'],
            ['id_municipio' => 34, 'nome' => 'Bela Vista da Caroba'],
            ['id_municipio' => 35, 'nome' => 'Bela Vista do Paraíso'],
            ['id_municipio' => 36, 'nome' => 'Bituruna'],
            ['id_municipio' => 37, 'nome' => 'Boa Esperança'],
            ['id_municipio' => 38, 'nome' => 'Boa Esperança do Iguaçu'],
            ['id_municipio' => 39, 'nome' => 'Boa Ventura de São Roque'],
            ['id_municipio' => 40, 'nome' => 'Boa Vista da Aparecida'],
            ['id_municipio' => 41, 'nome' => 'Bocaiúva do Sul'],
            ['id_municipio' => 42, 'nome' => 'Bom Jesus do Sul'],
            ['id_municipio' => 43, 'nome' => 'Bom Sucesso'],
            ['id_municipio' => 44, 'nome' => 'Bom Sucesso do Sul'],
            ['id_municipio' => 45, 'nome' => 'Borrazópolis'],
            ['id_municipio' => 46, 'nome' => 'Braganey'],
            ['id_municipio' => 47, 'nome' => 'Brasilândia do Sul'],
            ['id_municipio' => 48, 'nome' => 'Cafeara'],
            ['id_municipio' => 49, 'nome' => 'Cafelândia'],
            ['id_municipio' => 50, 'nome' => 'Cafezal do Sul'],
            ['id_municipio' => 51, 'nome' => 'Califórnia'],
            ['id_municipio' => 52, 'nome' => 'Cambará'],
            ['id_municipio' => 53, 'nome' => 'Cambé'],
            ['id_municipio' => 54, 'nome' => 'Cambira'],
            ['id_municipio' => 55, 'nome' => 'Campina da Lagoa'],
            ['id_municipio' => 56, 'nome' => 'Campina do Simão'],
            ['id_municipio' => 57, 'nome' => 'Campina Grande do Sul'],
            ['id_municipio' => 58, 'nome' => 'Campo Bonito'],
            ['id_municipio' => 59, 'nome' => 'Campo do Tenente'],
            ['id_municipio' => 60, 'nome' => 'Campo Largo'],
            ['id_municipio' => 61, 'nome' => 'Campo Magro'],
            ['id_municipio' => 62, 'nome' => 'Campo Mourão'],
            ['id_municipio' => 63, 'nome' => 'Cândido de Abreu'],
            ['id_municipio' => 64, 'nome' => 'Candói'],
            ['id_municipio' => 65, 'nome' => 'Cantagalo'],
            ['id_municipio' => 66, 'nome' => 'Capanema'],
            ['id_municipio' => 67, 'nome' => 'Capitão Leônidas Marques'],
            ['id_municipio' => 68, 'nome' => 'Carambeí'],
            ['id_municipio' => 69, 'nome' => 'Carlópolis'],
            ['id_municipio' => 70, 'nome' => 'Cascavel'],
            ['id_municipio' => 71, 'nome' => 'Castro'],
            ['id_municipio' => 72, 'nome' => 'Catanduvas'],
            ['id_municipio' => 73, 'nome' => 'Centenário do Sul'],
            ['id_municipio' => 74, 'nome' => 'Cerro Azul'],
            ['id_municipio' => 75, 'nome' => 'Céu Azul'],
            ['id_municipio' => 76, 'nome' => 'Chopinzinho'],
            ['id_municipio' => 77, 'nome' => 'Cianorte'],
            ['id_municipio' => 78, 'nome' => 'Cidade Gaúcha'],
            ['id_municipio' => 79, 'nome' => 'Clevelândia'],
            ['id_municipio' => 80, 'nome' => 'Colombo'],
            ['id_municipio' => 81, 'nome' => 'Colorado'],
            ['id_municipio' => 82, 'nome' => 'Congonhinhas'],
            ['id_municipio' => 83, 'nome' => 'Conselheiro Mairinck'],
            ['id_municipio' => 84, 'nome' => 'Contenda'],
            ['id_municipio' => 85, 'nome' => 'Corbélia'],
            ['id_municipio' => 86, 'nome' => 'Cornélio Procópio'],
            ['id_municipio' => 87, 'nome' => 'Coronel Domingos Soares'],
            ['id_municipio' => 88, 'nome' => 'Coronel Vivida'],
            ['id_municipio' => 89, 'nome' => 'Corumbataí do Sul'],
            ['id_municipio' => 90, 'nome' => 'Cruz Machado'],
            ['id_municipio' => 91, 'nome' => 'Cruzeiro do Iguaçu'],
            ['id_municipio' => 92, 'nome' => 'Cruzeiro do Oeste'],
            ['id_municipio' => 93, 'nome' => 'Cruzeiro do Sul'],
            ['id_municipio' => 94, 'nome' => 'Cruzmaltina'],
            ['id_municipio' => 95, 'nome' => 'Curitiba'],
            ['id_municipio' => 96, 'nome' => 'Curiúva'],
            ['id_municipio' => 97, 'nome' => 'Diamante do Norte'],
            ['id_municipio' => 98, 'nome' => 'Diamante do Sul'],
            ['id_municipio' => 99, 'nome' => 'Diamante D\'Oeste'],
            ['id_municipio' => 100, 'nome' => 'Dois Vizinhos'],
            ['id_municipio' => 101, 'nome' => 'Douradina'],
            ['id_municipio' => 102, 'nome' => 'Doutor Camargo'],
            ['id_municipio' => 103, 'nome' => 'Doutor Ulysses'],
            ['id_municipio' => 104, 'nome' => 'Enéas Marques'],
            ['id_municipio' => 105, 'nome' => 'Engenheiro Beltrão'],
            ['id_municipio' => 106, 'nome' => 'Entre Rios do Oeste'],
            ['id_municipio' => 107, 'nome' => 'Esperança Nova'],
            ['id_municipio' => 108, 'nome' => 'Espigão Alto do Iguaçu'],
            ['id_municipio' => 109, 'nome' => 'Farol'],
            ['id_municipio' => 110, 'nome' => 'Faxinal'],
            ['id_municipio' => 111, 'nome' => 'Fazenda Rio Grande'],
            ['id_municipio' => 112, 'nome' => 'Fênix'],
            ['id_municipio' => 113, 'nome' => 'Fernandes Pinheiro'],
            ['id_municipio' => 114, 'nome' => 'Figueira'],
            ['id_municipio' => 115, 'nome' => 'Flor da Serra do Sul'],
            ['id_municipio' => 116, 'nome' => 'Floraí'],
            ['id_municipio' => 117, 'nome' => 'Floresta'],
            ['id_municipio' => 118, 'nome' => 'Florestópolis'],
            ['id_municipio' => 119, 'nome' => 'Flórida'],
            ['id_municipio' => 120, 'nome' => 'Formosa do Oeste'],
            ['id_municipio' => 121, 'nome' => 'Foz do Iguaçu'],
            ['id_municipio' => 122, 'nome' => 'Foz do Jordão'],
            ['id_municipio' => 123, 'nome' => 'Francisco Alves'],
            ['id_municipio' => 124, 'nome' => 'Francisco Beltrão'],
            ['id_municipio' => 125, 'nome' => 'General Carneiro'],
            ['id_municipio' => 126, 'nome' => 'Godoy Moreira'],
            ['id_municipio' => 127, 'nome' => 'Goioerê'],
            ['id_municipio' => 128, 'nome' => 'Goioxim'],
            ['id_municipio' => 129, 'nome' => 'Grandes Rios'],
            ['id_municipio' => 130, 'nome' => 'Guaíra'],
            ['id_municipio' => 131, 'nome' => 'Guairaçá'],
            ['id_municipio' => 132, 'nome' => 'Guamiranga'],
            ['id_municipio' => 133, 'nome' => 'Guapirama'],
            ['id_municipio' => 134, 'nome' => 'Guaporema'],
            ['id_municipio' => 135, 'nome' => 'Guaraci'],
            ['id_municipio' => 136, 'nome' => 'Guaraniaçu'],
            ['id_municipio' => 137, 'nome' => 'Guarapuava'],
            ['id_municipio' => 138, 'nome' => 'Guaraqueçaba'],
            ['id_municipio' => 139, 'nome' => 'Guaratuba'],
            ['id_municipio' => 140, 'nome' => 'Honório Serpa'],
            ['id_municipio' => 141, 'nome' => 'Ibaiti'],
            ['id_municipio' => 142, 'nome' => 'Ibema'],
            ['id_municipio' => 143, 'nome' => 'Ibiporã'],
            ['id_municipio' => 144, 'nome' => 'Icaraíma'],
            ['id_municipio' => 145, 'nome' => 'Iguaraçu'],
            ['id_municipio' => 146, 'nome' => 'Iguatu'],
            ['id_municipio' => 147, 'nome' => 'Imbaú'],
            ['id_municipio' => 148, 'nome' => 'Imbituva'],
            ['id_municipio' => 149, 'nome' => 'Inácio Martins'],
            ['id_municipio' => 150, 'nome' => 'Inajá'],
            ['id_municipio' => 151, 'nome' => 'Indianópolis'],
            ['id_municipio' => 152, 'nome' => 'Ipiranga'],
            ['id_municipio' => 153, 'nome' => 'Iporã'],
            ['id_municipio' => 154, 'nome' => 'Iracema do Oeste'],
            ['id_municipio' => 155, 'nome' => 'Irati'],
            ['id_municipio' => 156, 'nome' => 'Iretama'],
            ['id_municipio' => 157, 'nome' => 'Itaguajé'],
            ['id_municipio' => 158, 'nome' => 'Itaipulândia'],
            ['id_municipio' => 159, 'nome' => 'Itambaracá'],
            ['id_municipio' => 160, 'nome' => 'Itambé'],
            ['id_municipio' => 161, 'nome' => 'Itapejara d\'Oeste'],
            ['id_municipio' => 162, 'nome' => 'Itaperuçu'],
            ['id_municipio' => 163, 'nome' => 'Itaúna do Sul'],
            ['id_municipio' => 164, 'nome' => 'Ivaí'],
            ['id_municipio' => 165, 'nome' => 'Ivaiporã'],
            ['id_municipio' => 166, 'nome' => 'Ivaté'],
            ['id_municipio' => 167, 'nome' => 'Ivatuba'],
            ['id_municipio' => 168, 'nome' => 'Jaboti'],
            ['id_municipio' => 169, 'nome' => 'Jacarezinho'],
            ['id_municipio' => 170, 'nome' => 'Jaguapitã'],
            ['id_municipio' => 171, 'nome' => 'Jaguariaíva'],
            ['id_municipio' => 172, 'nome' => 'Jandaia do Sul'],
            ['id_municipio' => 173, 'nome' => 'Janiópolis'],
            ['id_municipio' => 174, 'nome' => 'Japira'],
            ['id_municipio' => 175, 'nome' => 'Japurá'],
            ['id_municipio' => 176, 'nome' => 'Jardim Alegre'],
            ['id_municipio' => 177, 'nome' => 'Jardim Olinda'],
            ['id_municipio' => 178, 'nome' => 'Jataizinho'],
            ['id_municipio' => 179, 'nome' => 'Jesuítas'],
            ['id_municipio' => 180, 'nome' => 'Joaquim Távora'],
            ['id_municipio' => 181, 'nome' => 'Jundiaí do Sul'],
            ['id_municipio' => 182, 'nome' => 'Juranda'],
            ['id_municipio' => 183, 'nome' => 'Jussara'],
            ['id_municipio' => 184, 'nome' => 'Kaloré'],
            ['id_municipio' => 185, 'nome' => 'Lapa'],
            ['id_municipio' => 186, 'nome' => 'Laranjal'],
            ['id_municipio' => 187, 'nome' => 'Laranjeiras do Sul'],
            ['id_municipio' => 188, 'nome' => 'Leópolis'],
            ['id_municipio' => 189, 'nome' => 'Lidianópolis'],
            ['id_municipio' => 190, 'nome' => 'Lindoeste'],
            ['id_municipio' => 191, 'nome' => 'Loanda'],
            ['id_municipio' => 192, 'nome' => 'Lobato'],
            ['id_municipio' => 193, 'nome' => 'Londrina'],
            ['id_municipio' => 194, 'nome' => 'Luiziana'],
            ['id_municipio' => 195, 'nome' => 'Lunardelli'],
            ['id_municipio' => 196, 'nome' => 'Lupionópolis'],
            ['id_municipio' => 197, 'nome' => 'Mallet'],
            ['id_municipio' => 198, 'nome' => 'Mamborê'],
            ['id_municipio' => 199, 'nome' => 'Mandaguaçu'],
            ['id_municipio' => 200, 'nome' => 'Mandaguari'],
            ['id_municipio' => 201, 'nome' => 'Mandirituba'],
            ['id_municipio' => 202, 'nome' => 'Manfrinópolis'],
            ['id_municipio' => 203, 'nome' => 'Mangueirinha'],
            ['id_municipio' => 204, 'nome' => 'Manoel Ribas'],
            ['id_municipio' => 205, 'nome' => 'Marechal Cândido Rondon'],
            ['id_municipio' => 206, 'nome' => 'Maria Helena'],
            ['id_municipio' => 207, 'nome' => 'Marialva'],
            ['id_municipio' => 208, 'nome' => 'Marilândia do Sul'],
            ['id_municipio' => 209, 'nome' => 'Marilena'],
            ['id_municipio' => 210, 'nome' => 'Mariluz'],
            ['id_municipio' => 211, 'nome' => 'Maringá'],
            ['id_municipio' => 212, 'nome' => 'Mariópolis'],
            ['id_municipio' => 213, 'nome' => 'Maripá'],
            ['id_municipio' => 214, 'nome' => 'Marmeleiro'],
            ['id_municipio' => 215, 'nome' => 'Marquinho'],
            ['id_municipio' => 216, 'nome' => 'Marumbi'],
            ['id_municipio' => 217, 'nome' => 'Matelândia'],
            ['id_municipio' => 218, 'nome' => 'Matinhos'],
            ['id_municipio' => 219, 'nome' => 'Mato Rico'],
            ['id_municipio' => 220, 'nome' => 'Mauá da Serra'],
            ['id_municipio' => 221, 'nome' => 'Medianeira'],
            ['id_municipio' => 222, 'nome' => 'Mercedes'],
            ['id_municipio' => 223, 'nome' => 'Mirador'],
            ['id_municipio' => 224, 'nome' => 'Miraselva'],
            ['id_municipio' => 225, 'nome' => 'Missal'],
            ['id_municipio' => 226, 'nome' => 'Moreira Sales'],
            ['id_municipio' => 227, 'nome' => 'Morretes'],
            ['id_municipio' => 228, 'nome' => 'Munhoz de Melo'],
            ['id_municipio' => 229, 'nome' => 'Nossa Senhora das Graças'],
            ['id_municipio' => 230, 'nome' => 'Nova Aliança do Ivaí'],
            ['id_municipio' => 231, 'nome' => 'Nova América da Colina'],
            ['id_municipio' => 232, 'nome' => 'Nova Aurora'],
            ['id_municipio' => 233, 'nome' => 'Nova Cantu'],
            ['id_municipio' => 234, 'nome' => 'Nova Esperança'],
            ['id_municipio' => 235, 'nome' => 'Nova Esperança do Sudoeste'],
            ['id_municipio' => 236, 'nome' => 'Nova Fátima'],
            ['id_municipio' => 237, 'nome' => 'Nova Laranjeiras'],
            ['id_municipio' => 238, 'nome' => 'Nova Londrina'],
            ['id_municipio' => 239, 'nome' => 'Nova Olímpia'],
            ['id_municipio' => 240, 'nome' => 'Nova Prata do Iguaçu'],
            ['id_municipio' => 241, 'nome' => 'Nova Santa Bárbara'],
            ['id_municipio' => 242, 'nome' => 'Nova Santa Rosa'],
            ['id_municipio' => 243, 'nome' => 'Nova Tebas'],
            ['id_municipio' => 244, 'nome' => 'Novo Itacolomi'],
            ['id_municipio' => 245, 'nome' => 'Ortigueira'],
            ['id_municipio' => 246, 'nome' => 'Ourizona'],
            ['id_municipio' => 247, 'nome' => 'Ouro Verde do Oeste'],
            ['id_municipio' => 248, 'nome' => 'Paiçandu'],
            ['id_municipio' => 249, 'nome' => 'Palmas'],
            ['id_municipio' => 250, 'nome' => 'Palmeira'],
            ['id_municipio' => 251, 'nome' => 'Palmital'],
            ['id_municipio' => 252, 'nome' => 'Palotina'],
            ['id_municipio' => 253, 'nome' => 'Paraíso do Norte'],
            ['id_municipio' => 254, 'nome' => 'Paranacity'],
            ['id_municipio' => 255, 'nome' => 'Paranaguá'],
            ['id_municipio' => 256, 'nome' => 'Paranapoema'],
            ['id_municipio' => 257, 'nome' => 'Paranavaí'],
            ['id_municipio' => 258, 'nome' => 'Pato Bragado'],
            ['id_municipio' => 259, 'nome' => 'Pato Branco'],
            ['id_municipio' => 260, 'nome' => 'Paula Freitas'],
            ['id_municipio' => 261, 'nome' => 'Paulo Frontin'],
            ['id_municipio' => 262, 'nome' => 'Peabiru'],
            ['id_municipio' => 263, 'nome' => 'Perobal'],
            ['id_municipio' => 264, 'nome' => 'Pérola'],
            ['id_municipio' => 265, 'nome' => 'Pérola d\'Oeste'],
            ['id_municipio' => 266, 'nome' => 'Piên'],
            ['id_municipio' => 267, 'nome' => 'Pinhais'],
            ['id_municipio' => 268, 'nome' => 'Pinhal de São Bento'],
            ['id_municipio' => 269, 'nome' => 'Pinhalão'],
            ['id_municipio' => 270, 'nome' => 'Pinhão'],
            ['id_municipio' => 271, 'nome' => 'Piraí do Sul'],
            ['id_municipio' => 272, 'nome' => 'Piraquara'],
            ['id_municipio' => 273, 'nome' => 'Pitanga'],
            ['id_municipio' => 274, 'nome' => 'Pitangueiras'],
            ['id_municipio' => 275, 'nome' => 'Planaltina do Paraná'],
            ['id_municipio' => 276, 'nome' => 'Planalto'],
            ['id_municipio' => 277, 'nome' => 'Ponta Grossa'],
            ['id_municipio' => 278, 'nome' => 'Pontal do Paraná'],
            ['id_municipio' => 279, 'nome' => 'Porecatu'],
            ['id_municipio' => 280, 'nome' => 'Porto Amazonas'],
            ['id_municipio' => 281, 'nome' => 'Porto Barreiro'],
            ['id_municipio' => 282, 'nome' => 'Porto Rico'],
            ['id_municipio' => 283, 'nome' => 'Porto Vitória'],
            ['id_municipio' => 284, 'nome' => 'Prado Ferreira'],
            ['id_municipio' => 285, 'nome' => 'Pranchita'],
            ['id_municipio' => 286, 'nome' => 'Presidente Castelo Branco'],
            ['id_municipio' => 287, 'nome' => 'Primeiro de Maio'],
            ['id_municipio' => 288, 'nome' => 'Prudentópolis'],
            ['id_municipio' => 289, 'nome' => 'Quarto Centenário'],
            ['id_municipio' => 290, 'nome' => 'Quatiguá'],
            ['id_municipio' => 291, 'nome' => 'Quatro Barras'],
            ['id_municipio' => 292, 'nome' => 'Quatro Pontes'],
            ['id_municipio' => 293, 'nome' => 'Quedas do Iguaçu'],
            ['id_municipio' => 294, 'nome' => 'Querência do Norte'],
            ['id_municipio' => 295, 'nome' => 'Quinta do Sol'],
            ['id_municipio' => 296, 'nome' => 'Quitandinha'],
            ['id_municipio' => 297, 'nome' => 'Ramilândia'],
            ['id_municipio' => 298, 'nome' => 'Rancho Alegre'],
            ['id_municipio' => 299, 'nome' => 'Rancho Alegre D\'Oeste'],
            ['id_municipio' => 300, 'nome' => 'Realeza'],
            ['id_municipio' => 301, 'nome' => 'Rebouças'],
            ['id_municipio' => 302, 'nome' => 'Renascença'],
            ['id_municipio' => 303, 'nome' => 'Reserva'],
            ['id_municipio' => 304, 'nome' => 'Reserva do Iguaçu'],
            ['id_municipio' => 305, 'nome' => 'Ribeirão Claro'],
            ['id_municipio' => 306, 'nome' => 'Ribeirão do Pinhal'],
            ['id_municipio' => 307, 'nome' => 'Rio Azul'],
            ['id_municipio' => 308, 'nome' => 'Rio Bom'],
            ['id_municipio' => 309, 'nome' => 'Rio Bonito do Iguaçu'],
            ['id_municipio' => 310, 'nome' => 'Rio Branco do Ivaí'],
            ['id_municipio' => 311, 'nome' => 'Rio Branco do Sul'],
            ['id_municipio' => 312, 'nome' => 'Rio Negro'],
            ['id_municipio' => 313, 'nome' => 'Rolândia'],
            ['id_municipio' => 314, 'nome' => 'Roncador'],
            ['id_municipio' => 315, 'nome' => 'Rondon'],
            ['id_municipio' => 316, 'nome' => 'Rosário do Ivaí'],
            ['id_municipio' => 317, 'nome' => 'Sabáudia'],
            ['id_municipio' => 318, 'nome' => 'Salgado Filho'],
            ['id_municipio' => 319, 'nome' => 'Salto do Itararé'],
            ['id_municipio' => 320, 'nome' => 'Salto do Lontra'],
            ['id_municipio' => 321, 'nome' => 'Santa Amélia'],
            ['id_municipio' => 322, 'nome' => 'Santa Cecília do Pavão'],
            ['id_municipio' => 323, 'nome' => 'Santa Cruz de Monte Castelo'],
            ['id_municipio' => 324, 'nome' => 'Santa Fé'],
            ['id_municipio' => 325, 'nome' => 'Santa Helena'],
            ['id_municipio' => 326, 'nome' => 'Santa Inês'],
            ['id_municipio' => 327, 'nome' => 'Santa Isabel do Ivaí'],
            ['id_municipio' => 328, 'nome' => 'Santa Izabel do Oeste'],
            ['id_municipio' => 329, 'nome' => 'Santa Lúcia'],
            ['id_municipio' => 330, 'nome' => 'Santa Maria do Oeste'],
            ['id_municipio' => 331, 'nome' => 'Santa Mariana'],
            ['id_municipio' => 332, 'nome' => 'Santa Mônica'],
            ['id_municipio' => 333, 'nome' => 'Santa Tereza do Oeste'],
            ['id_municipio' => 334, 'nome' => 'Santa Terezinha de Itaipu'],
            ['id_municipio' => 335, 'nome' => 'Santana do Itararé'],
            ['id_municipio' => 336, 'nome' => 'Santo Antônio da Platina'],
            ['id_municipio' => 337, 'nome' => 'Santo Antônio do Caiuá'],
            ['id_municipio' => 338, 'nome' => 'Santo Antônio do Paraíso'],
            ['id_municipio' => 339, 'nome' => 'Santo Antônio do Sudoeste'],
            ['id_municipio' => 340, 'nome' => 'Santo Inácio'],
            ['id_municipio' => 341, 'nome' => 'São Carlos do Ivaí'],
            ['id_municipio' => 342, 'nome' => 'São Jerônimo da Serra'],
            ['id_municipio' => 343, 'nome' => 'São João'],
            ['id_municipio' => 344, 'nome' => 'São João do Caiuá'],
            ['id_municipio' => 345, 'nome' => 'São João do Ivaí'],
            ['id_municipio' => 346, 'nome' => 'São João do Triunfo'],
            ['id_municipio' => 347, 'nome' => 'São Jorge d\'Oeste'],
            ['id_municipio' => 348, 'nome' => 'São Jorge do Ivaí'],
            ['id_municipio' => 349, 'nome' => 'São Jorge do Patrocínio'],
            ['id_municipio' => 350, 'nome' => 'São José da Boa Vista'],
            ['id_municipio' => 351, 'nome' => 'São José das Palmeiras'],
            ['id_municipio' => 352, 'nome' => 'São José dos Pinhais'],
            ['id_municipio' => 353, 'nome' => 'São Manoel do Paraná'],
            ['id_municipio' => 354, 'nome' => 'São Mateus do Sul'],
            ['id_municipio' => 355, 'nome' => 'São Miguel do Iguaçu'],
            ['id_municipio' => 356, 'nome' => 'São Pedro do Iguaçu'],
            ['id_municipio' => 357, 'nome' => 'São Pedro do Ivaí'],
            ['id_municipio' => 358, 'nome' => 'São Pedro do Paraná'],
            ['id_municipio' => 359, 'nome' => 'São Sebastião da Amoreira'],
            ['id_municipio' => 360, 'nome' => 'São Tomé'],
            ['id_municipio' => 361, 'nome' => 'Sapopema'],
            ['id_municipio' => 362, 'nome' => 'Sarandi'],
            ['id_municipio' => 363, 'nome' => 'Saudade do Iguaçu'],
            ['id_municipio' => 364, 'nome' => 'Sengés'],
            ['id_municipio' => 365, 'nome' => 'Serranópolis do Iguaçu'],
            ['id_municipio' => 366, 'nome' => 'Sertaneja'],
            ['id_municipio' => 367, 'nome' => 'Sertanópolis'],
            ['id_municipio' => 368, 'nome' => 'Siqueira Campos'],
            ['id_municipio' => 369, 'nome' => 'Sulina'],
            ['id_municipio' => 370, 'nome' => 'Tamarana'],
            ['id_municipio' => 371, 'nome' => 'Tamboara'],
            ['id_municipio' => 372, 'nome' => 'Tapejara'],
            ['id_municipio' => 373, 'nome' => 'Tapira'],
            ['id_municipio' => 374, 'nome' => 'Teixeira Soares'],
            ['id_municipio' => 375, 'nome' => 'Telêmaco Borba'],
            ['id_municipio' => 376, 'nome' => 'Terra Boa'],
            ['id_municipio' => 377, 'nome' => 'Terra Rica'],
            ['id_municipio' => 378, 'nome' => 'Terra Roxa'],
            ['id_municipio' => 379, 'nome' => 'Tibagi'],
            ['id_municipio' => 380, 'nome' => 'Tijucas do Sul'],
            ['id_municipio' => 381, 'nome' => 'Toledo'],
            ['id_municipio' => 382, 'nome' => 'Tomazina'],
            ['id_municipio' => 383, 'nome' => 'Três Barras do Paraná'],
            ['id_municipio' => 384, 'nome' => 'Tunas do Paraná'],
            ['id_municipio' => 385, 'nome' => 'Tuneiras do Oeste'],
            ['id_municipio' => 386, 'nome' => 'Tupãssi'],
            ['id_municipio' => 387, 'nome' => 'Turvo'],
            ['id_municipio' => 388, 'nome' => 'Ubiratã'],
            ['id_municipio' => 389, 'nome' => 'Umuarama'],
            ['id_municipio' => 390, 'nome' => 'União da Vitória'],
            ['id_municipio' => 391, 'nome' => 'Uniflor'],
            ['id_municipio' => 392, 'nome' => 'Uraí'],
            ['id_municipio' => 393, 'nome' => 'Ventania'],
            ['id_municipio' => 394, 'nome' => 'Vera Cruz do Oeste'],
            ['id_municipio' => 395, 'nome' => 'Verê'],
            ['id_municipio' => 396, 'nome' => 'Virmond'],
            ['id_municipio' => 397, 'nome' => 'Vitorino'],
            ['id_municipio' => 398, 'nome' => 'Wenceslau Braz'],
            ['id_municipio' => 399, 'nome' => 'Xambrê'],
        ];
        Municipio::insert($municipios);

        $escolas = [
            ['nome' => 'CE PROF EF M', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE DO CAMPO ALDEIA INDIGENA TUPANI VY', 'id_municipio' => 1, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'CE QUILOMBOLA DE BARRA GRANDE', 'id_municipio' => 2, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE PREFEITO ADRIANO MONTEIRO DE CASTRO', 'id_municipio' => 2, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'EE DO CAMPO DE RIBEIRAO GRANDE', 'id_municipio' => 2, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'CE PROFª ALBERTA ROCHOCHA', 'id_municipio' => 3, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'EE DO CAMPO DE PAPANDUVA', 'id_municipio' => 3, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'EE DO CAMPO DE SAO JOAO', 'id_municipio' => 3, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'CE VEREADOR PEDRO P S SANTOS', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE PROF EDIMAR L DE ALMEIDA', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE AMBRÓSIO BIZELLO', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE DO CAMPO DE BATEIAS', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE TANCREDO NEVES', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE PROFª ROSA SAPORSKI', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE ADELINO DE S PAULA', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA ALMIRANTE TAMANDARE', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE DO CAMPO LAMENHA GRANDE', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE VILA PROSDOCIMO', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE PAPA JOAO PAULO I', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE SÃO TOMAZ DE AQUINO', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE CH HENRY FORD', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE PROF HELENA W IORDACHE', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE JARDIM BONFIM', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE JARDIM DO PARAISO', 'id_municipio' => 4, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'CE DE ALTAMIRA DO PARANA', 'id_municipio' => 5, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'CE VILA ALTA', 'id_municipio' => 6, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE DO CAMPO DE OURO VERDE', 'id_municipio' => 6, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'CE ALTO PARANA', 'id_municipio' => 7, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE DO CAMPO DE MARISTELA', 'id_municipio' => 7, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE DO CAMPO DE SANTA MARIA', 'id_municipio' => 7, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'CE PROF MANOEL RIBAS', 'id_municipio' => 8, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE DO CAMPO DE MIRADOR', 'id_municipio' => 8, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE DO CAMPO DE PAULISTANIA', 'id_municipio' => 8, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'CE MALBA TAHAN', 'id_municipio' => 9, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE LUCIA ALVES DE O SCHOFFEN', 'id_municipio' => 9, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE DO CAMPO DE JARDIM PAREDÃO', 'id_municipio' => 9, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE DO CAMPO DE SAO JOAO', 'id_municipio' => 9, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'CE ALBOR II', 'id_municipio' => 10, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE PROFª ALZIRA HORVATICH', 'id_municipio' => 10, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'CE AMAPORA', 'id_municipio' => 11, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'CE NOSSA SRA APARECIDA', 'id_municipio' => 12, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE BANDEIRANTES', 'id_municipio' => 12, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA DE AMPERE', 'id_municipio' => 12, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE DO CAMPO DE SANTA TEREZINHA', 'id_municipio' => 12, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'CE DE ANAHY', 'id_municipio' => 13, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'CE NORMAL ESTADUAL', 'id_municipio' => 14, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE DURVAL RAMOS FILHO', 'id_municipio' => 14, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE DO CAMPO PE TIMÓTEO', 'id_municipio' => 14, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'CE TOBIAS DE AGUIAR', 'id_municipio' => 15, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'CE ROCHA POMBO', 'id_municipio' => 16, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE MOYSES LUPION', 'id_municipio' => 16, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CE DO CAMPO DE CACHOEIRA', 'id_municipio' => 16, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'CE DO CAMPO DE SÃO ROQUE', 'id_municipio' => 17, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CE DR GETÚLIO VARGAS', 'id_municipio' => 17, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E PROFª NILO ROMANO', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E POLIVALENTE DE APUCARANA', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª IZIDORO LUIZ CERAVOLO', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PADRE JOSÉ DE ANCHIETA', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E ALBERTO SANTOS DUMONT', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª DULCE MASCHIO', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E HEITOR DE AZEVEDO', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª GODOY', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª ZILDA ROMANO', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA DE APUCARANA', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR CARLOS MASSARTE', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E ARISTIDES DE REZENDE', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROF VALENTIN', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª WILMA AP FONSECA', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA CORINA DE SOUZA', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE PIRAPO', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E PROFESSOR JOSÉ ARIANO', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E NOSSA SENHORA DA GLORIA', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR JUNIOR', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR OSVALDO', 'id_municipio' => 18, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E EMILIO DE MENEZES', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E UNIDADE POLO', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DR OLÍVIO BELICH', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE ARICANDUVA', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CEEBJA DE ARAPONGAS', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E HONÓRIO CARNEIRO LEÃO', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROF IVANILDE S GODOY', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROF FRANCISCO FERRAZ', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DR JOSÉ DE ANCHIETA', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E MARQUÊS DE CARAVELAS', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DR ANTONIO GARCEZ', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª MARIA LEONE', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DR CLOTARIO DE MACEDO', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA ÁUREA', 'id_municipio' => 19, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E CORONEL DUVAL DE GÓES', 'id_municipio' => 20, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E LUIZ A XAVIER', 'id_municipio' => 20, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA DE ARAPOTI', 'id_municipio' => 20, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE CALOERAS', 'id_municipio' => 20, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E DE ARAPUÃ', 'id_municipio' => 21, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DO CAMPO DE SAO VICENTE', 'id_municipio' => 22, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E DO CAMPO DE SAO GERALDO', 'id_municipio' => 22, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E 29 DE NOVEMBRO', 'id_municipio' => 22, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E PROF AGALVIRA B PINTO', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DIAS DA ROCHA', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª FAZIO', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª HELENA WIDMANN', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR LINCOLN', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE GUARAITUBA', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E PROF LINCOLN S NEVES', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA DE ARAUCARIA', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JARDIM IPÊ', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROF PEDRO DEMETERCO', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA MARILZE', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA ZILDA', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA VERA', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR LUIZ', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA TEREZINHA', 'id_municipio' => 23, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE ARIRANHA DO IVAI', 'id_municipio' => 24, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE ARIRANHA DO IVAI', 'id_municipio' => 24, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E CONSELHEIRO CARRIÃO', 'id_municipio' => 25, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª MARIA APARECIDA', 'id_municipio' => 25, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE PAU D ALHO', 'id_municipio' => 25, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E DUQUE DE CAXIAS', 'id_municipio' => 26, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E GUIMARÃES ROSA', 'id_municipio' => 26, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PADRE ANCHIETA', 'id_municipio' => 26, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE SAO COSME E DAMIAO', 'id_municipio' => 26, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CEEBJA DE ASSIS CHATEAUBRIAND', 'id_municipio' => 26, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE ENCRUZILHADA', 'id_municipio' => 26, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E PROFª MARIA HELENA', 'id_municipio' => 26, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E GENEROSO MARQUES', 'id_municipio' => 27, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª ZILAH', 'id_municipio' => 27, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE IRAPUÃ', 'id_municipio' => 27, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E ATALAIA', 'id_municipio' => 28, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DO CAMPO DE SAO LUIZ', 'id_municipio' => 29, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E DO CAMPO DE BUGRE', 'id_municipio' => 29, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E DO CAMPO DE RODEIO', 'id_municipio' => 29, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E DR CYRILLO', 'id_municipio' => 30, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA MARIA', 'id_municipio' => 30, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR JOÃO', 'id_municipio' => 30, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E LUZIA GARCIA', 'id_municipio' => 31, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE CORUMBATAI', 'id_municipio' => 31, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E PROF LUIZ', 'id_municipio' => 31, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DR JOÃO DA ROCHA', 'id_municipio' => 32, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE BARRACÃO', 'id_municipio' => 33, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA DE BARRACAO', 'id_municipio' => 33, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE BELA VISTA DA CAROBA', 'id_municipio' => 34, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E PROF BRASílio de A', 'id_municipio' => 35, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª MARIA LUIZA', 'id_municipio' => 35, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E SANTA IZABEL', 'id_municipio' => 36, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA IVETE', 'id_municipio' => 36, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E VINTE E CINCO DE DEZEMBRO', 'id_municipio' => 37, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE BOA ESPERANÇA DO IGUAÇU', 'id_municipio' => 38, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE BOA VENTURA DE SÃO ROQUE', 'id_municipio' => 39, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE BOA VISTA DA APARECIDA', 'id_municipio' => 40, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DO CAMPO DE PAPANDUVA', 'id_municipio' => 41, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E DE BOCAIUVA DO SUL', 'id_municipio' => 41, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE RIO GRANDE', 'id_municipio' => 41, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E DO CAMPO DE BARRA GRANDE', 'id_municipio' => 41, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E DO CAMPO DE BOA VISTA', 'id_municipio' => 41, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E DO CAMPO DE SÃO JOÃO', 'id_municipio' => 41, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E DE BOM JESUS DO SUL', 'id_municipio' => 42, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE BOM SUCESSO', 'id_municipio' => 43, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE BOM SUCESSO DO SUL', 'id_municipio' => 44, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE BORRAZÓPOLIS', 'id_municipio' => 45, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE FAXINALZINHO', 'id_municipio' => 45, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E DE BRAGANEY', 'id_municipio' => 46, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E PROF ANTÔNIO CARLOS', 'id_municipio' => 47, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E VICENTE RIJO', 'id_municipio' => 48, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CAFELÂNDIA', 'id_municipio' => 49, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CAFEZAL DO SUL', 'id_municipio' => 50, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E TALITA BRESOLIN', 'id_municipio' => 51, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DR HERCULANO F A', 'id_municipio' => 52, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª SILVIA G PENTEADO', 'id_municipio' => 52, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E PROFESSORA CECILIA', 'id_municipio' => 53, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA MARIA', 'id_municipio' => 53, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DOM ÁLVARO', 'id_municipio' => 53, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA OLÍVIA', 'id_municipio' => 53, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR ANTÔNIO', 'id_municipio' => 53, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR LUIZ', 'id_municipio' => 53, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR PEDRO', 'id_municipio' => 53, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CAMBIRA', 'id_municipio' => 54, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CAMPINA DA LAGOA', 'id_municipio' => 55, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE HERVAL', 'id_municipio' => 55, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E DE CAMPINA DO SIMÃO', 'id_municipio' => 56, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E PROFª ANNA RITA', 'id_municipio' => 57, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE CAMPINA GRANDE DO SUL', 'id_municipio' => 57, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E IVAN FERREIRA DO AMARAL', 'id_municipio' => 57, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª SIRIA M DA CRUZ', 'id_municipio' => 57, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E TIMBU VELHO', 'id_municipio' => 57, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JARDIM PAULISTA', 'id_municipio' => 57, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DE ENSINO PROFISSIONALIZANTE', 'id_municipio' => 57, 'nivel_ensino' => 'escola_tecnica', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA DE CAMPINA GRANDE DO SUL', 'id_municipio' => 57, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E NILSON JAIME', 'id_municipio' => 57, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª ELIZABETH', 'id_municipio' => 57, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA JOANITA', 'id_municipio' => 57, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CAMPO BONITO', 'id_municipio' => 58, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E PROFª ELISABETH', 'id_municipio' => 59, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DESEMBARGADOR CLOTARIO', 'id_municipio' => 60, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª MARIA DE LOURDES', 'id_municipio' => 60, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E SAGRADA FAMÍLIA', 'id_municipio' => 60, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª EDITH', 'id_municipio' => 60, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA DE CAMPO LARGO', 'id_municipio' => 60, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª MARIA HILDA', 'id_municipio' => 60, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR OSWALDO', 'id_municipio' => 60, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JULIO NERONE', 'id_municipio' => 60, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE BALSA NOVA', 'id_municipio' => 60, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E PROFESSORA OTALIVIA', 'id_municipio' => 60, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR DARCY', 'id_municipio' => 60, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA VERA', 'id_municipio' => 60, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E JARDIM BOA VISTA', 'id_municipio' => 61, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E CONDE DE PARANAGUÁ', 'id_municipio' => 61, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª IVONE', 'id_municipio' => 61, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E LÚCIA BASTOS', 'id_municipio' => 61, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CAMPO MOURÃO', 'id_municipio' => 62, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DR OSVALDO CRUZ', 'id_municipio' => 62, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DE ENSINO PROFISSIONALIZANTE', 'id_municipio' => 62, 'nivel_ensino' => 'escola_tecnica', 'tipo' => 'urbana'],
            ['nome' => 'C E DOM BOSCO', 'id_municipio' => 62, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR IVONE', 'id_municipio' => 62, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA DE CAMPO MOURÃO', 'id_municipio' => 62, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA MARECHAL', 'id_municipio' => 62, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA ALCIONE', 'id_municipio' => 62, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E UNIDADE POLO', 'id_municipio' => 62, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR DARCY', 'id_municipio' => 62, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA TEREZINHA', 'id_municipio' => 62, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E PROFESSOR CLEMENTE', 'id_municipio' => 63, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DE CÂNDIDO DE ABREU', 'id_municipio' => 63, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CANDÓI', 'id_municipio' => 64, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CANTAGALO', 'id_municipio' => 65, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE CAVILHA', 'id_municipio' => 65, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E DE CAPANEMA', 'id_municipio' => 66, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CAPITÃO LEÔNIDAS MARQUES', 'id_municipio' => 67, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CARAMBEÍ', 'id_municipio' => 68, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CARLÓPOLIS', 'id_municipio' => 69, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E PROFESSOR FRANCISCO', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR PAULO', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA MARLI', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JARDIM PANORAMA', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E OLINDA CARNIEL', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PRESIDENTE COSTA E SILVA', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR MÁRIO', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR PEDRO', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JARDIM CLARITO', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA DE CASCAVEL', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR VICTOR', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA IEDA', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E WILSON JOFFRE', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA JÚLIA', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E HORÁCIO RIBEIRO', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E CASTELO BRANCO', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JARDIM SANTA FELICIDADE', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JARDIM INTERLAGOS', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JARDIM COLMÉIA', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA REGINA', 'id_municipio' => 70, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E VESPASIANO DE ALMEIDA', 'id_municipio' => 71, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E MAJOR VESPASIANO DE ALMEIDA', 'id_municipio' => 71, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA MARIA APARECIDA', 'id_municipio' => 71, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE SOCAVÃO', 'id_municipio' => 71, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'CEEBJA DE CASTRO', 'id_municipio' => 71, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR ARTUR', 'id_municipio' => 71, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR JOSÉ', 'id_municipio' => 71, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA DINA', 'id_municipio' => 71, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CATANDUVAS', 'id_municipio' => 72, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE CATANDUVAS', 'id_municipio' => 72, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E PADRE ANCHIETA', 'id_municipio' => 73, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E IRMA ANTOINETTE', 'id_municipio' => 73, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DAVID CARNEIRO', 'id_municipio' => 74, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DR ELIAS ZARUR', 'id_municipio' => 74, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E QUILOMBOLA DIOGO RAMOS', 'id_municipio' => 74, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E DO CAMPO FAZENDA VELHA', 'id_municipio' => 74, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E CECILIA MEIRELES', 'id_municipio' => 75, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E JOSE ARMETI KRUG', 'id_municipio' => 76, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA DE CHOPINZINHO', 'id_municipio' => 76, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO SAO LUIZ', 'id_municipio' => 76, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E DO CAMPO SAO FRANCISCO', 'id_municipio' => 76, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E CIANORTE', 'id_municipio' => 77, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DOM PEDRO I', 'id_municipio' => 77, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E ITACIANO V AREAL', 'id_municipio' => 77, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JARDIM UNIVERSITARIO', 'id_municipio' => 77, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JOSE GUIMARAES', 'id_municipio' => 77, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E IVONE T GUIDELLI', 'id_municipio' => 77, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E ALMIRANTE CUSTODIO', 'id_municipio' => 77, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª VERA LUCIA', 'id_municipio' => 77, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA DE CIANORTE', 'id_municipio' => 77, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CIDADE GAÚCHA', 'id_municipio' => 78, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CLEVELÂNDIA', 'id_municipio' => 79, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E ALFREDO CHAVES', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JÚLIA CAVALIN', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DOM ORIONE', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E LUIZ SELLA', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JOÃO RIBEIRO DE CAMARGO', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª MARIA LUIZA', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E VINICIUS DE MORAES', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E TANCREDO NEVES', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DR ALFREDO CHAVES', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª ZILDA', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E MONSENHOR THEODORO', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR PLÍNIO', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR JOÃO', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR LUIZ', 'id_municipio' => 80, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE COLORADO', 'id_municipio' => 81, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR JOÃO', 'id_municipio' => 81, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CONGONHINHAS', 'id_municipio' => 82, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CONSELHEIRO MAIRINCK', 'id_municipio' => 83, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CONTENDA', 'id_municipio' => 84, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE CONTENDA', 'id_municipio' => 84, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E DE CORBÉLIA', 'id_municipio' => 85, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE CORBÉLIA', 'id_municipio' => 85, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E DE CORNÉLIO PROCÓPIO', 'id_municipio' => 86, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E MONSENHOR JEFFERSON', 'id_municipio' => 86, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E CASTRO ALVES', 'id_municipio' => 86, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E ANDRE NADAL', 'id_municipio' => 86, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR LUIZ', 'id_municipio' => 86, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E ZULMIRA MARQUES', 'id_municipio' => 86, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E MONTEIRO LOBATO', 'id_municipio' => 86, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DE ENSINO PROFISSIONALIZANTE', 'id_municipio' => 86, 'nivel_ensino' => 'escola_tecnica', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CORONEL DOMINGOS SOARES', 'id_municipio' => 87, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CORONEL VIVIDA', 'id_municipio' => 88, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E ARNALDO BUSATO', 'id_municipio' => 88, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CORUMBATAÍ DO SUL', 'id_municipio' => 89, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CRUZ MACHADO', 'id_municipio' => 90, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE CRUZ MACHADO', 'id_municipio' => 90, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E DE CRUZEIRO DO IGUAÇU', 'id_municipio' => 91, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CRUZEIRO DO OESTE', 'id_municipio' => 92, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E ALMIRANTE TAMANDARÉ', 'id_municipio' => 92, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA DE CRUZEIRO DO OESTE', 'id_municipio' => 92, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CRUZEIRO DO SUL', 'id_municipio' => 93, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CRUZMALTINA', 'id_municipio' => 94, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E PROFESSORA ISABEL', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA MARIA', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E TIRADENTES', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR LUIZ', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR CLETO', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA REGINA', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO PARANÁ', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DR XAVIER DA SILVA', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR NILO', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR PAULO', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E ANÍBAL KHURY', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA MARIA', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA ERMELINA', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E SANTO AGOSTINHO', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA MARLENE', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR ELIAS', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA THEREZA', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA ALBA', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA LEONILDA', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA MARIA', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA MARIA', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA MARIA', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E NOSSA SENHORA DE FÁTIMA', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR GUIDO', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA LUCY', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA OLGA', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSOR JOSÉ', 'id_municipio' => 95, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE CURIÚVA', 'id_municipio' => 96, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE CURIÚVA', 'id_municipio' => 96, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E DE DIAMANTE DO NORTE', 'id_municipio' => 97, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE DIAMANTE DO SUL', 'id_municipio' => 98, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE DIAMANTE D\'OESTE', 'id_municipio' => 99, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DOIS VIZINHOS', 'id_municipio' => 100, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO DE DOIS VIZINHOS', 'id_municipio' => 100, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],
            ['nome' => 'C E MONSENHOR QUIRINO', 'id_municipio' => 100, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E LEONARDO DA VINCI', 'id_municipio' => 100, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DOURADINA', 'id_municipio' => 101, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DR CAMARGO', 'id_municipio' => 102, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE DOUTOR ULYSSES', 'id_municipio' => 103, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE ENÉAS MARQUES', 'id_municipio' => 104, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E PADRE ANTÔNIO', 'id_municipio' => 105, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E ENTRE RIOS DO OESTE', 'id_municipio' => 106, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E ESPERANÇA NOVA', 'id_municipio' => 107, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E ESPIGÃO ALTO DO IGUAÇU', 'id_municipio' => 108, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E CULTURA UNIVERSAL', 'id_municipio' => 109, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E ÉRICO VERÍSSIMO', 'id_municipio' => 110, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª MARIA M C BATISTA', 'id_municipio' => 110, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E ABÍLIO LOURENÇO DOS SANTOS', 'id_municipio' => 111, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E ANITA CANET', 'id_municipio' => 111, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E BAYARD OSÓRIO', 'id_municipio' => 111, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E CÍVICO-MILITAR PROFESSOR ANDERSON RANGEL', 'id_municipio' => 111, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DESEMBARGADOR CUNHA PEREIRA', 'id_municipio' => 111, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JORGE ANDRIGUETTO', 'id_municipio' => 111, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA FAZENDA RIO GRANDE', 'id_municipio' => 111, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA LUCIA BASTOS', 'id_municipio' => 111, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFESSORA SUELI A A B', 'id_municipio' => 111, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E SANTO INÁCIO DE LOYOLA', 'id_municipio' => 112, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DR AFONSO A DE CAMARGO', 'id_municipio' => 113, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DO CAMPO ANGAÍ', 'id_municipio' => 113, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'rural'],

            ['nome' => 'C E PROFª MARIA L P ALVES', 'id_municipio' => 114, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE FLOR DA SERRA DO SUL', 'id_municipio' => 115, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E JOSÉ ALENCAR', 'id_municipio' => 116, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DOM PEDRO I', 'id_municipio' => 117, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E ADELIA C DE SOUZA', 'id_municipio' => 118, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E PROFª CLELIA M BRANDÃO', 'id_municipio' => 119, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E GETÚLIO VARGAS', 'id_municipio' => 120, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E GUSTAVO DOBRANDINO', 'id_municipio' => 121, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PAULO FREIRE', 'id_municipio' => 121, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E DOM PEDRO II', 'id_municipio' => 121, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E MONSENHOR GUILHERME', 'id_municipio' => 121, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E ALMIRANTE TAMANDARÉ', 'id_municipio' => 121, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E SANTA RITA', 'id_municipio' => 121, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E BARÃO DO RIO BRANCO', 'id_municipio' => 121, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E MARCOS FREIRE', 'id_municipio' => 121, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'CEEBJA DE FOZ DO IGUAÇU', 'id_municipio' => 121, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROF FLAVIANO', 'id_municipio' => 121, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E JARDIM PANORAMA', 'id_municipio' => 121, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
            ['nome' => 'C E PROFª IRMÃ MARIA', 'id_municipio' => 121, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE FOZ DO JORDÃO', 'id_municipio' => 122, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE FRANCISCO ALVES', 'id_municipio' => 123, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE FRANCISCO BELTRÃO', 'id_municipio' => 124, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GENERAL CARNEIRO', 'id_municipio' => 125, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GODOY MOREIRA', 'id_municipio' => 126, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GOIOERÊ', 'id_municipio' => 127, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GOIOXIM', 'id_municipio' => 128, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GRANDES RIOS', 'id_municipio' => 129, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GUAÍRA', 'id_municipio' => 130, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GUAIRAÇÁ', 'id_municipio' => 131, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GUAMIRANGA', 'id_municipio' => 132, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GUAPIRAMA', 'id_municipio' => 133, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GUAPOREMA', 'id_municipio' => 134, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GUARACI', 'id_municipio' => 135, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GUARANIAÇU', 'id_municipio' => 136, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GUARAPUAVA', 'id_municipio' => 137, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GUARAQUEÇABA', 'id_municipio' => 138, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE GUARATUBA', 'id_municipio' => 139, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE HONÓRIO SERPA', 'id_municipio' => 140, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IBAITI', 'id_municipio' => 141, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IBEMA', 'id_municipio' => 142, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IBIPORÃ', 'id_municipio' => 143, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE ICARAÍMA', 'id_municipio' => 144, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IGUARAÇU', 'id_municipio' => 145, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IGUATU', 'id_municipio' => 146, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IMBAÚ', 'id_municipio' => 147, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IMBITUVA', 'id_municipio' => 148, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE INÁCIO MARTINS', 'id_municipio' => 149, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE INAJÁ', 'id_municipio' => 150, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE INDIANÓPOLIS', 'id_municipio' => 151, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IPIRANGA', 'id_municipio' => 152, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IPORÃ', 'id_municipio' => 153, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IRACEMA DO OESTE', 'id_municipio' => 154, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IRATI', 'id_municipio' => 155, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IRETAMA', 'id_municipio' => 156, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE ITAGUAJÉ', 'id_municipio' => 157, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE ITAIPULÂNDIA', 'id_municipio' => 158, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE ITAMBARACÁ', 'id_municipio' => 159, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE ITAMBÉ', 'id_municipio' => 160, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE ITAPEJARA D\'OESTE', 'id_municipio' => 161, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE ITAPERUÇU', 'id_municipio' => 162, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE ITAÚNA DO SUL', 'id_municipio' => 163, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IVAÍ', 'id_municipio' => 164, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IVAIPORÃ', 'id_municipio' => 165, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IVATÉ', 'id_municipio' => 166, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE IVATUBA', 'id_municipio' => 167, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JABOTI', 'id_municipio' => 168, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JACAREZINHO', 'id_municipio' => 169, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JAGUAPITÃ', 'id_municipio' => 170, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JAGUARIAÍVA', 'id_municipio' => 171, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JANDAIA DO SUL', 'id_municipio' => 172, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JANIÓPOLIS', 'id_municipio' => 173, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JAPIRA', 'id_municipio' => 174, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JAPURÁ', 'id_municipio' => 175, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JARDIM ALEGRE', 'id_municipio' => 176, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JARDIM OLINDA', 'id_municipio' => 177, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JATAIZINHO', 'id_municipio' => 178, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JESUÍTAS', 'id_municipio' => 179, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JOAQUIM TÁVORA', 'id_municipio' => 180, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JUNDIAÍ DO SUL', 'id_municipio' => 181, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JURANDA', 'id_municipio' => 182, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE JUSSARA', 'id_municipio' => 183, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE KALORÉ', 'id_municipio' => 184, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE LAPA', 'id_municipio' => 185, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE LARANJAL', 'id_municipio' => 186, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE LARANJEIRAS DO SUL', 'id_municipio' => 187, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE LEÓPOLIS', 'id_municipio' => 188, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE LIDIANÓPOLIS', 'id_municipio' => 189, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE LINDOESTE', 'id_municipio' => 190, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE LOANDA', 'id_municipio' => 191, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE LOBATO', 'id_municipio' => 192, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE LONDRINA', 'id_municipio' => 193, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE LUIZIANA', 'id_municipio' => 194, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE LUNARDELLI', 'id_municipio' => 195, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE LUPIONÓPOLIS', 'id_municipio' => 196, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MALLET', 'id_municipio' => 197, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MAMBORÊ', 'id_municipio' => 198, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MANDAGUAÇU', 'id_municipio' => 199, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MANDAGUARI', 'id_municipio' => 200, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MANDIRITUBA', 'id_municipio' => 201, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MANFRINÓPOLIS', 'id_municipio' => 202, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MANGUEIRINHA', 'id_municipio' => 203, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MANOEL RIBAS', 'id_municipio' => 204, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MARECHAL CÂNDIDO RONDON', 'id_municipio' => 205, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MARIA HELENA', 'id_municipio' => 206, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MARIALVA', 'id_municipio' => 207, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MARILÂNDIA DO SUL', 'id_municipio' => 208, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MARILENA', 'id_municipio' => 209, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MARILUZ', 'id_municipio' => 210, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MARINGÁ', 'id_municipio' => 211, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MARIÓPOLIS', 'id_municipio' => 212, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MARIPÁ', 'id_municipio' => 213, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MARMELEIRO', 'id_municipio' => 214, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MARQUINHO', 'id_municipio' => 215, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MARUMBI', 'id_municipio' => 216, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MATELÂNDIA', 'id_municipio' => 217, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MATINHOS', 'id_municipio' => 218, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MATO RICO', 'id_municipio' => 219, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MAUÁ DA SERRA', 'id_municipio' => 220, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MEDIANEIRA', 'id_municipio' => 221, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MERCEDES', 'id_municipio' => 222, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MIRADOR', 'id_municipio' => 223, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MIRASELVA', 'id_municipio' => 224, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MISSAL', 'id_municipio' => 225, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MOREIRA SALES', 'id_municipio' => 226, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MORRETES', 'id_municipio' => 227, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE MUNHOZ DE MELO', 'id_municipio' => 228, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOSSA SENHORA DAS GRAÇAS', 'id_municipio' => 229, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA ALIANÇA DO IVAÍ', 'id_municipio' => 230, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA AMÉRICA DA COLINA', 'id_municipio' => 231, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA AURORA', 'id_municipio' => 232, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA CANTU', 'id_municipio' => 233, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA ESPERANÇA', 'id_municipio' => 234, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA ESPERANÇA DO SUDOESTE', 'id_municipio' => 235, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA FÁTIMA', 'id_municipio' => 236, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA LARANJEIRAS', 'id_municipio' => 237, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA LONDRINA', 'id_municipio' => 238, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA OLÍMPIA', 'id_municipio' => 239, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA PRATA DO IGUAÇU', 'id_municipio' => 240, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA SANTA BÁRBARA', 'id_municipio' => 241, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA SANTA ROSA', 'id_municipio' => 242, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVA TEBAS', 'id_municipio' => 243, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE NOVO ITACOLOMI', 'id_municipio' => 244, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE ORTIGUEIRA', 'id_municipio' => 245, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE OURIZONA', 'id_municipio' => 246, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE OURO VERDE DO OESTE', 'id_municipio' => 247, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PAIÇANDU', 'id_municipio' => 248, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PALMAS', 'id_municipio' => 249, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PALMEIRA', 'id_municipio' => 250, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PALMITAL', 'id_municipio' => 251, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PALOTINA', 'id_municipio' => 252, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PARAÍSO DO NORTE', 'id_municipio' => 253, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PARANACITY', 'id_municipio' => 254, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PARANAGUÁ', 'id_municipio' => 255, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PARANAPOEMA', 'id_municipio' => 256, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PARANAVAÍ', 'id_municipio' => 257, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PATO BRAGADO', 'id_municipio' => 258, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PATO BRANCO', 'id_municipio' => 259, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PAULA FREITAS', 'id_municipio' => 260, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PAULO FRONTIN', 'id_municipio' => 261, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PEABIRU', 'id_municipio' => 262, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PEROBAL', 'id_municipio' => 263, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PÉROLA', 'id_municipio' => 264, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PÉROLA D\'OESTE', 'id_municipio' => 265, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PIÊN', 'id_municipio' => 266, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PINHAIS', 'id_municipio' => 267, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PINHAL DE SÃO BENTO', 'id_municipio' => 268, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PINHALÃO', 'id_municipio' => 269, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PINHÃO', 'id_municipio' => 270, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PIRAÍ DO SUL', 'id_municipio' => 271, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PIRAQUARA', 'id_municipio' => 272, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PITANGA', 'id_municipio' => 273, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PITANGUEIRAS', 'id_municipio' => 274, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PLANALTINA DO PARANÁ', 'id_municipio' => 275, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PLANALTO', 'id_municipio' => 276, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PONTA GROSSA', 'id_municipio' => 277, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PONTAL DO PARANÁ', 'id_municipio' => 278, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PORECATU', 'id_municipio' => 279, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PORTO AMAZONAS', 'id_municipio' => 280, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PORTO BARREIRO', 'id_municipio' => 281, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PORTO RICO', 'id_municipio' => 282, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PORTO VITÓRIA', 'id_municipio' => 283, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PRADO FERREIRA', 'id_municipio' => 284, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PRANCHITA', 'id_municipio' => 285, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PRESIDENTE CASTELO BRANCO', 'id_municipio' => 286, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PRIMEIRO DE MAIO', 'id_municipio' => 287, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE PRUDENTÓPOLIS', 'id_municipio' => 288, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE QUARTO CENTENÁRIO', 'id_municipio' => 289, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE QUATIGUÁ', 'id_municipio' => 290, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE QUATRO BARRAS', 'id_municipio' => 291, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE QUATRO PONTES', 'id_municipio' => 292, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE QUEDAS DO IGUAÇU', 'id_municipio' => 293, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE QUERÊNCIA DO NORTE', 'id_municipio' => 294, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE QUINTA DO SOL', 'id_municipio' => 295, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE QUITANDINHA', 'id_municipio' => 296, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RAMILÂNDIA', 'id_municipio' => 297, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RANCHO ALEGRE', 'id_municipio' => 298, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RANCHO ALEGRE D\'OESTE', 'id_municipio' => 299, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE REALEZA', 'id_municipio' => 300, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE REBOUÇAS', 'id_municipio' => 301, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RENASCENÇA', 'id_municipio' => 302, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RESERVA', 'id_municipio' => 303, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RESERVA DO IGUAÇU', 'id_municipio' => 304, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RIBEIRÃO CLARO', 'id_municipio' => 305, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RIBEIRÃO DO PINHAL', 'id_municipio' => 306, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RIO AZUL', 'id_municipio' => 307, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RIO BOM', 'id_municipio' => 308, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RIO BONITO DO IGUAÇU', 'id_municipio' => 309, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RIO BRANCO DO IVAÍ', 'id_municipio' => 310, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RIO BRANCO DO SUL', 'id_municipio' => 311, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RIO NEGRO', 'id_municipio' => 312, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE ROLÂNDIA', 'id_municipio' => 313, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RONCADOR', 'id_municipio' => 314, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE RONDON', 'id_municipio' => 315, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE ROSÁRIO DO IVAÍ', 'id_municipio' => 316, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SABÁUDIA', 'id_municipio' => 317, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SALGADO FILHO', 'id_municipio' => 318, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SALTO DO ITARARÉ', 'id_municipio' => 319, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SALTO DO LONTRA', 'id_municipio' => 320, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA AMÉLIA', 'id_municipio' => 321, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA CECÍLIA DO PAVÃO', 'id_municipio' => 322, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA CRUZ DE MONTE CASTELO', 'id_municipio' => 323, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA FÉ', 'id_municipio' => 324, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA HELENA', 'id_municipio' => 325, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA INÊS', 'id_municipio' => 326, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA ISABEL DO IVAÍ', 'id_municipio' => 327, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA IZABEL DO OESTE', 'id_municipio' => 328, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA LÚCIA', 'id_municipio' => 329, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA MARIA DO OESTE', 'id_municipio' => 330, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA MARIANA', 'id_municipio' => 331, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA MÔNICA', 'id_municipio' => 332, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA TEREZA DO OESTE', 'id_municipio' => 333, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTA TEREZINHA DE ITAIPU', 'id_municipio' => 334, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTANA DO ITARARÉ', 'id_municipio' => 335, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTO ANTÔNIO DA PLATINA', 'id_municipio' => 336, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTO ANTÔNIO DO CAIUÁ', 'id_municipio' => 337, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTO ANTÔNIO DO PARAÍSO', 'id_municipio' => 338, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTO ANTÔNIO DO SUDOESTE', 'id_municipio' => 339, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SANTO INÁCIO', 'id_municipio' => 340, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO CARLOS DO IVAÍ', 'id_municipio' => 341, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO JERÔNIMO DA SERRA', 'id_municipio' => 342, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO JOÃO', 'id_municipio' => 343, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO JOÃO DO CAIUÁ', 'id_municipio' => 344, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO JOÃO DO IVAÍ', 'id_municipio' => 345, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO JOÃO DO TRIUNFO', 'id_municipio' => 346, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO JORGE D\'OESTE', 'id_municipio' => 347, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO JORGE DO IVAÍ', 'id_municipio' => 348, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO JORGE DO PATROCÍNIO', 'id_municipio' => 349, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO JOSÉ DA BOA VISTA', 'id_municipio' => 350, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO JOSÉ DAS PALMEIRAS', 'id_municipio' => 351, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO JOSÉ DOS PINHAIS', 'id_municipio' => 352, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO MANOEL DO PARANÁ', 'id_municipio' => 353, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO MATEUS DO SUL', 'id_municipio' => 354, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO MIGUEL DO IGUAÇU', 'id_municipio' => 355, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO PEDRO DO IGUAÇU', 'id_municipio' => 356, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO PEDRO DO IVAÍ', 'id_municipio' => 357, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO PEDRO DO PARANÁ', 'id_municipio' => 358, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO SEBASTIÃO DA AMOREIRA', 'id_municipio' => 359, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SÃO TOMÉ', 'id_municipio' => 360, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SAPOPEMA', 'id_municipio' => 361, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SARANDI', 'id_municipio' => 362, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SAUDADE DO IGUAÇU', 'id_municipio' => 363, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SENGÉS', 'id_municipio' => 364, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SERRANÓPOLIS DO IGUAÇU', 'id_municipio' => 365, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SERTANEJA', 'id_municipio' => 366, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SERTANÓPOLIS', 'id_municipio' => 367, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SIQUEIRA CAMPOS', 'id_municipio' => 368, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE SULINA', 'id_municipio' => 369, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TAMARANA', 'id_municipio' => 370, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TAMBOARA', 'id_municipio' => 371, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TAPEJARA', 'id_municipio' => 372, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TAPIRA', 'id_municipio' => 373, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TEIXEIRA SOARES', 'id_municipio' => 374, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TELÊMACO BORBA', 'id_municipio' => 375, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TERRA BOA', 'id_municipio' => 376, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TERRA RICA', 'id_municipio' => 377, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TERRA ROXA', 'id_municipio' => 378, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TIBAGI', 'id_municipio' => 379, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TIJUCAS DO SUL', 'id_municipio' => 380, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TOLEDO', 'id_municipio' => 381, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TOMAZINA', 'id_municipio' => 382, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TRÊS BARRAS DO PARANÁ', 'id_municipio' => 383, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TUNAS DO PARANÁ', 'id_municipio' => 384, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TUNEIRAS DO OESTE', 'id_municipio' => 385, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TUPÃSSI', 'id_municipio' => 386, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE TURVO', 'id_municipio' => 387, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE UBIRATÃ', 'id_municipio' => 388, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE UMUARAMA', 'id_municipio' => 389, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE UNIÃO DA VITÓRIA', 'id_municipio' => 390, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE UNIFLOR', 'id_municipio' => 391, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE URAÍ', 'id_municipio' => 392, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE VENTANIA', 'id_municipio' => 393, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE VERA CRUZ DO OESTE', 'id_municipio' => 394, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE VERÊ', 'id_municipio' => 395, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE VIRMOND', 'id_municipio' => 396, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE VITORINO', 'id_municipio' => 397, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE WENCESLAU BRAZ', 'id_municipio' => 398, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],

            ['nome' => 'C E DE XAMBRÊ', 'id_municipio' => 399, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana'],
        ];

        Escola::insert($escolas);

        if (config('database.default') !== 'sqlite') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}