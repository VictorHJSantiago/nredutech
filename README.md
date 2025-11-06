<div align="center">
  <br />
  <img src="https://raw.githubusercontent.com/victorhjsantiago/nredutech/main/public/images/nredutech.png" alt="Logo NREduTech" width="150" style="border-radius: 50%;">
  
  <h1 style="border-bottom: none; font-size: 2.5em; margin-bottom: 0;">NREduTech</h1>
  
  <strong style="font-size: 1.2em; color: #555;">
    Sistema de Gestão Acadêmica e Agendamento de Recursos Didáticos
  </strong>
  
  <br />
  <br />

  <p style="font-size: 1.1em; max-width: 700px;">
    Uma solução robusta e centralizada, desenvolvida sob a arquitetura <strong>Laravel MVC</strong>, destinada à gestão integrada de escolas, turmas, recursos didáticos e agendamentos para o <strong>Núcleo Regional de Educação (NRE)</strong>.
  </p>

  <p>
    <img src="https://img.shields.io/badge/status-em%20desenvolvimento-yellow?style=for-the-badge" alt="Status do Projeto: Em Desenvolvimento">
    <img src="https://img.shields.io/badge/PHP-8.4.11-777BB4?style=for-the-badge&logo=php" alt="Versão do PHP">
    <img src="https://img.shields.io/badge/Laravel-12.28.1-FF2D20?style=for-the-badge&logo=laravel" alt="Versão do Laravel">
    <img src="https://img.shields.io/badge/MariaDB-11.8.3-003545?style=for-the-badge&logo=mariadb" alt="Banco de Dados">
  </p>
</div>

---

## 📖 Sobre o Projeto

O **NREduTech** é um Sistema de Gestão Acadêmica (SGA) concebido para atuar como a plataforma central de administração do Núcleo Regional de Educação. A aplicação aborda o desafio de gerenciar de forma eficiente a alocação de recursos pedagógicos, o agendamento de laboratórios e a organização de componentes curriculares entre múltiplas instituições de ensino.

Do ponto de vista acadêmico, o projeto é uma implementação prática dos princípios de **Desenvolvimento de Software Orientado a Objetos (POO)** e da arquitetura **Model-View-Controller (MVC)**. Ele utiliza o framework Laravel para garantir um desenvolvimento rápido, seguro e escalável, abstraindo complexidades de baixo nível e permitindo foco total nas regras de negócio.

A plataforma é desenhada com foco em diferentes perfis de usuário (Administradores, Diretores e Professores), oferecendo *dashboards* e funcionalidades específicas para cada nível de acesso. O sistema incorpora funcionalidades essenciais como geração de relatórios complexos, um sistema de notificações proativo e rotinas de backup automatizadas, garantindo a integridade e a disponibilidade dos dados.

## ✨ Funcionalidades Principais

O sistema é modularizado para cobrir todas as necessidades da gestão educacional:

* **👥 Gestão de Usuários:** Controle de acesso granular com três níveis de permissão (Administrador, Diretor, Professor).
* **🏫 Gestão de Escolas e Municípios:** Cadastro e administração centralizada das instituições de ensino e suas localidades.
* **👨‍🎓 Gestão de Turmas:** Organização de turmas vinculadas a cada escola.
* **📂 Gestão de Disciplinas:** (Componentes Curriculares) Cadastro e associação das disciplinas lecionadas.
* **📖 Gestão de Recursos Didáticos:** Catálogo de todos os recursos pedagógicos e tecnológicos disponíveis para agendamento (ex: laboratórios, projetores, kits de robótica).
* **📅 Agendamento Inteligente:** Interface de calendário (baseada em *FullCalendar*) para que professores possam reservar recursos para suas turmas, com validação de disponibilidade.
* **📊 Relatórios Avançados:** Geração de relatórios dinâmicos sobre a utilização de recursos, agendamentos por escola e mais, com exportação para **PDF** e **Excel**.
* **🔔 Sistema de Notificações:** Alertas em tempo real na plataforma e envio de e-mails para ações críticas (ex: confirmação de agendamento).
* **🗃️ Backup e Restauração:** Funcionalidade robusta para criação de *backups* da aplicação e do banco de dados, com agendamento automático e restauração.
* **♿ Acessibilidade:** Integração nativa com o **VLibras** para garantir a acessibilidade para pessoas com deficiência.

---

## 🛠️ Requisitos e Regras de Negócio

A lógica do sistema foi modelada para refletir as hierarquias e processos de um ambiente educacional real.

### Regras de Negócio Principais

* 🔑 **Aprovação de Usuários:** Professores e Diretores podem se autocadastrar, mas suas contas são criadas com status `pendente`. Um `Administrador` deve aprovar manualmente o cadastro para que o usuário possa acessar o sistema.
* 🚦 **Hierarquia de Permissões:**
    * **Administrador:** Possui controle total (CRUD) sobre todas as entidades: Escolas, Municípios, Usuários, Turmas, Recursos e Disciplinas. É o único perfil que pode realizar backups e restaurações do sistema.
    * **Diretor:** Possui controle (CRUD) sobre entidades *apenas* da sua própria escola (Turmas, Professores, Recursos, Disciplinas). Pode visualizar relatórios referentes à sua escola.
    * **Professor:** O foco é no agendamento. Pode agendar recursos para suas turmas/disciplinas (Ofertas) e gerenciar (CRUD) os recursos e disciplinas que ele mesmo cadastrou.
* 🌍 **Propriedade de Recursos:** Recursos e Disciplinas podem ser "Globais" (pertencem ao NRE, `school_id = null`) e disponíveis para todas as escolas, ou pertencer a uma escola específica (visíveis apenas para usuários daquela escola).
* ⏱️ **Conflito de Agendamento:** O sistema impede ativamente que um mesmo recurso (`recurso_didatico_id`) seja agendado por duas pessoas no mesmo intervalo de tempo (validação de sobreposição de `data_inicio` e `data_fim`).
* 🔗 **Integridade de Dados:** O sistema utiliza restrições de chave estrangeira (`FOREIGN KEY`) para garantir a integridade referencial. Não é possível excluir uma Escola se ela possuir Turmas ou Usuários vinculados; não é possível excluir um Município se ele possuir Escolas.

### Requisitos Funcionais (RF)

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #0169b4; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">ID</th>
        <th style="padding: 12px 15px; text-align: left;">Descrição</th>
        <th style="padding: 12px 15px; text-align: left;">Perfil(s)</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-001</td>
        <td style="padding: 12px 15px;">O sistema deve permitir a autenticação de usuários por e-mail e senha.</td>
        <td style="padding: 12px 15px;">Todos</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-002</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o autocadastro de Professores e Diretores (com status inicial "pendente").</td>
        <td style="padding: 12px 15px;">Visitante</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-003</td>
        <td style="padding: 12px 15px;">O sistema deve permitir que Administradores aprovem ou rejeitem cadastros pendentes.</td>
        <td style="padding: 12px 15px;">Administrador</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-004</td>
        <td style="padding: 12px 15px;">O sistema deve permitir ao usuário alterar suas próprias informações de perfil (nome, senha, etc.).</td>
        <td style="padding: 12px 15px;">Todos</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-005</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o gerenciamento (CRUD) de Municípios e Escolas.</td>
        <td style="padding: 12px 15px;">Administrador</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-006</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o gerenciamento (CRUD) de Turmas, vinculando-as a uma escola.</td>
        <td style="padding: 12px 15px;">Admin, Diretor</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-007</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o gerenciamento (CRUD) de Componentes Curriculares (Disciplinas).</td>
        <td style="padding: 12px 15px;">Admin, Diretor</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-008</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o gerenciamento (CRUD) de Recursos Didáticos.</td>
        <td style="padding: 12px 15px;">Admin, Diretor, Professor</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-009</td>
        <td style="padding: 12px 15px;">O sistema deve permitir a associação de Professores a Turmas/Disciplinas (Ofertas).</td>
        <td style="padding: 12px 15px;">Admin, Diretor</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-010</td>
        <td style="padding: 12px 15px;">O sistema deve permitir a criação de Agendamentos de recursos, vinculando-os a uma "Oferta".</td>
        <td style="padding: 12px 15px;">Professor</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-011</td>
        <td style="padding: 12px 15px;">O sistema deve exibir um calendário com todos os agendamentos.</td>
        <td style="padding: 12px 15px;">Todos</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-012</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o cancelamento de agendamentos pelo criador ou por um superior.</td>
        <td style="padding: 12px 15px;">Professor, Diretor, Admin</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-013</td>
        <td style="padding: 12px 15px;">O sistema deve gerar relatórios dinâmicos com múltiplos filtros.</td>
        <td style="padding: 12px 15px;">Admin, Diretor</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-014</td>
        <td style="padding: 12px 15px;">O sistema deve permitir a exportação de relatórios em formatos PDF, XLSX, ODS, CSV e HTML.</td>
        <td style="padding: 12px 15px;">Admin, Diretor</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-015</td>
        <td style="padding: 12px 15px;">O sistema deve exibir notificações na plataforma e enviá-las por e-mail.</td>
        <td style="padding: 12px 15px;">Todos</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-016</td>
        <td style="padding: 12px 15px;">O sistema deve permitir a criação manual e agendada de backups.</td>
        <td style="padding: 12px 15px;">Administrador</td>
      </tr>
      <tr style="background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-017</td>
        <td style="padding: 12px 15px;">O sistema deve permitir a restauração de um backup a partir de um arquivo SQL.</td>
        <td style="padding: 12px 15px;">Administrador</td>
      </tr>
    </tbody>
  </table>
</div>

### Requisitos Não-Funcionais (RNF)

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #0169b4; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">ID</th>
        <th style="padding: 12px 15px; text-align: left;">Categoria</th>
        <th style="padding: 12px 15px; text-align: left;">Descrição</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-001</td>
        <td style="padding: 12px 15px;"><strong>Usabilidade</strong></td>
        <td style="padding: 12px 15px;">A interface deve ser responsiva, adaptando-se a desktops, tablets e smartphones (Mobile-First).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RNF-002</td>
        <td style="padding: 12px 15px;"><strong>Segurança</strong></td>
        <td style="padding: 12px 15px;">As senhas devem ser armazenadas de forma irreversível, utilizando o algoritmo de hashing <strong>Argon2id</strong>.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-003</td>
        <td style="padding: 12px 15px;"><strong>Segurança</strong></td>
        <td style="padding: 12px 15px;">Todas as submissões de formulários (POST, PUT, DELETE) devem ser protegidas contra ataques CSRF.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RNF-004</td>
        <td style="padding: 12px 15px;"><strong>Confiabilidade</strong></td>
        <td style="padding: 12px 15px;">O sistema deve ter alta disponibilidade, garantida por rotinas de backup automáticas (agendadas).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-005</td>
        <td style="padding: 12px 15px;"><strong>Manutenibilidade</strong></td>
        <td style="padding: 12px 15px;">O código-fonte deve seguir os padrões PSR-12, ser modularizado (MVC) e utilizar validação em <em>Form Requests</em>.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RNF-006</td>
        <td style="padding: 12px 15px;"><strong>Acessibilidade</strong></td>
        <td style="padding: 12px 15px;">O sistema deve ser acessível, fornecendo suporte ao VLibras em todas as páginas (logado ou não).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-007</td>
        <td style="padding: 12px 15px;"><strong>Segurança</strong></td>
        <td style="padding: 12px 15px;">A aplicação deve criptografar dados sensíveis (sessões, cookies) usando o padrão <strong>AES-256-CBC</strong>.</td>
      </tr>
      <tr style="background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-008</td>
        <td style="padding: 12px 15px;"><strong>Internacionalização</strong></td>
        <td style="padding: 12px 15px;">O sistema deve ter seus textos e mensagens de validação traduzidos para o Português do Brasil (pt_BR).</td>
      </tr>
    </tbody>
  </table>
</div>

---

## 💻 Ambiente de Desenvolvimento

O projeto foi desenvolvido utilizando um conjunto de ferramentas moderno, focado em segurança e produtividade, em um ambiente híbrido.

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #444; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">Categoria</th>
        <th style="padding: 12px 15px; text-align: left;">Ferramenta</th>
        <th style="padding: 12px 15px; text-align: left;">Versão</th>
        <th style="padding: 12px 15px; text-align: left;">Propósito</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">Sistema Operacional</td>
        <td style="padding: 12px 15px;"><strong>Windows 11 + WSL 2 (Ubuntu)</strong></td>
        <td style="padding: 12px 15px;">-</td>
        <td style="padding: 12px 15px;">Ambiente de desenvolvimento híbrido, combinando a UI do Windows com um terminal Linux nativo (WSL) para performance.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">Sistema Operacional</td>
        <td style="padding: 12px 15px;"><strong>Kali GNU/Linux Rolling</strong></td>
        <td style="padding: 12px 15px;">2025.3</td>
        <td style="padding: 12px 15px;">Utilizado para testes de segurança (Pentest) e validação da robustez da aplicação.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">Editor de Código</td>
        <td style="padding: 12px 15px;"><strong>Visual Studio Code</strong></td>
        <td style="padding: 12px 15px;">1.103.1</td>
        <td style="padding: 12px 15px;">Editor principal com extensões para PHP, Laravel, Blade e Tailwind.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">Controle de Versão</td>
        <td style="padding: 12px 15px;"><strong>Git</strong></td>
        <td style="padding: 12px 15px;">2.50.1</td>
        <td style="padding: 12px 15px;">Gerenciamento do código-fonte e versionamento.</td>
      </tr>
    </tbody>
  </table>
</div>

---

## 🚀 Stack Tecnológica e Justificativa Acadêmica

A seleção de tecnologias (o *stack*) do NREduTech foi deliberada para otimizar a performance, a segurança e a produtividade do desenvolvimento.

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #444; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">Tecnologia</th>
        <th style="padding: 12px 15px; text-align: left;">Versão</th>
        <th style="padding: 12px 15px; text-align: left;">Por que foi escolhida? (Vantagens sobre concorrentes)</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>PHP</strong></td>
        <td style="padding: 12px 15px;">8.4.11</td>
        <td style="padding: 12px 15px;">
          <strong>Performance e Modernidade:</strong> O PHP 8.4 oferece melhorias drásticas de performance com o compilador <strong>JIT (Just-In-Time)</strong>. Seus recursos modernos (tipagem estrita, Enums, Readonly Properties) o tornam mais robusto e menos propenso a erros.<br>
          <strong>Vantagem vs. Concorrentes (Python/Node.js):</strong> A facilidade de *deploy* (hospedagem) do PHP é incomparável. Sua curva de aprendizado é mais rápida que a de frameworks como Django (Python), e seu modelo *multi-process* é mais simples de gerenciar para aplicações web tradicionais do que o *event-loop* do Node.js.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Laravel</strong></td>
        <td style="padding: 12px 15px;">12.28.1</td>
        <td style="padding: 12px 15px;">
          <strong>Ecossistema "Baterias Inclusas":</strong> Escolhido por seu ecossistema completo. O <strong>Eloquent ORM</strong> é considerado mais elegante e produtivo que o Doctrine (Symfony) ou o TypeORM (Node.js). O *template engine* <strong>Blade</strong> é simples e extensível. Ferramentas integradas como `artisan` e agendamento de tarefas abstraem complexidades que em *frameworks* mais "agnósticos" exigiriam implementação manual.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>MariaDB (Server/Client)</strong></td>
        <td style="padding: 12px 15px;">11.8.3 / 15.2</td>
        <td style="padding: 12px 15px;">
          <strong>Performance Open-Source:</strong> Um *fork* do MySQL mantido pela comunidade, focado em performance e abertura. Oferece compatibilidade total com o MySQL (e Eloquent), mas com otimizações de performance (ex: *storage engines* como Aria) e um ciclo de *features* mais rápido. É superior ao MySQL em termos de licenciamento e abertura, e frequentemente supera o MySQL em performance de *queries* complexas.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Tailwind CSS</strong></td>
        <td style="padding: 12px 15px;">3.x</td>
        <td style="padding: 12px 15px;">
          <strong>Produtividade e Customização:</strong> Superior a *frameworks* baseados em componentes (como Bootstrap). Em vez de fornecer componentes prontos (ex: `.card`) que precisam ser sobrescritos, o Tailwind fornece classes utilitárias de baixo nível. Isso permite criar designs 100% customizados e responsivos sem "lutar" contra estilos pré-definidos, resultando em um CSS final menor.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Vite.js</strong></td>
        <td style="padding: 12px 15px;">7.1.10</td>
        <td style="padding: 12px 15px;">
          <strong>Velocidade de Desenvolvimento:</strong> Substitui o Webpack/Mix. Sua principal vantagem é o <strong>Hot Module Replacement (HMR)</strong> quase instantâneo. Ele usa o ESBuild (escrito em Go) para pré-compilar dependências, tornando o *build* e a atualização do servidor de desenvolvimento ordens de magnitude mais rápidos que o Webpack.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Node.js / NPM</strong></td>
        <td style="padding: 12px 15px;">20.19.2 / 9.2.0</td>
        <td style="padding: 12px 15px;">
          <strong>Ecossistema Frontend:</strong> Runtime de JavaScript essencial para o processo de *build* do frontend (Vite, Tailwind). A versão 20.x é a LTS (Long-Term Support), garantindo estabilidade. O NPM é usado para a gestão de pacotes do frontend.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Composer</strong></td>
        <td style="padding: 12px 15px;">2.8.10</td>
        <td style="padding: 12px 15px;">
          <strong>Gerenciador de Dependências PHP:</strong> Padrão de-facto, essencial para gerenciar os pacotes do Laravel e suas dependências (Spatie, Maatwebsite, etc.).
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Maatwebsite/Excel</strong></td>
        <td style="padding: 12px 15px;">3.1</td>
        <td style="padding: 12px 15px;">
          <strong>Exportação de Relatórios:</strong> Padrão da comunidade Laravel para exportação de dados. Abstrai a complexidade da PHPOffice/PhpSpreadsheet, permitindo a exportação de *views* Blade ou coleções Eloquent diretamente para XLSX, CSV, ODS ou PDF.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Spatie/laravel-backup</strong></td>
        <td style="padding: 12px 15px;">8.x</td>
        <td style="padding: 12px 15px;">
          <strong>Confiabilidade de Backup:</strong> Solução superior a *scripts cron* manuais, pois cuida de todo o ciclo de vida do backup: agendamento, execução do *dump* do DB, compactação, notificação por e-mail e limpeza de backups antigos.
        </td>
      </tr>
    </tbody>
  </table>
</div>

---

## 🔒 Segurança e Criptografia

A segurança é um pilar central do NREduTech, implementando padrões modernos para proteção de dados.

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #444; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">Tópico</th>
        <th style="padding: 12px 15px; text-align: left;">Implementação</th>
        <th style="padding: 12px 15px; text-align: left;">Justificativa (Por que é superior?)</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Hashing de Senhas</strong></td>
        <td style="padding: 12px 15px;"><strong>Argon2id</strong> (via <code>config/hashing.php</code>)</td>
        <td style="padding: 12px 15px;">
          <strong>Resistência a Hardware Específico:</strong> Argon2id é o vencedor da <strong>Password Hashing Competition (2015)</strong> e o padrão recomendado pelo OWASP.
          <ul>
            <li><strong>Superior ao Bcrypt:</strong> Bcrypt é resistente a ataques de força bruta, mas vulnerável a hardware especializado (GPUs).</li>
            <li><strong>Superior ao scrypt:</strong> scrypt foi pioneiro em ser "memory-hard" (resistente a GPU), mas o Argon2id é mais robusto contra uma gama maior de ataques.</li>
            <li><strong>Superior ao Argon2d/2i:</strong> A variante <strong>Argon2id</strong> é híbrida, oferecendo a resistência a GPU do Argon2d e a resistência a ataques de <em>side-channel</em> do Argon2i, sendo a escolha mais segura.</li>
          </ul>
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;"><strong>Criptografia de Sessão</strong></td>
        <td style="padding: 12px 15px;"><strong>AES-256-CBC</strong></td>
        <td style="padding: 12px 15px;">
          <strong>Padrão da Indústria:</strong> Utiliza criptografia simétrica forte para proteger os dados da sessão e cookies de "lembrar-me". Isso impede que um invasor leia ou falsifique o conteúdo da sessão de um usuário, pois ele não possui a chave secreta (<code>APP_KEY</code>) para descriptografar os dados.
        </td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;"><strong>Proteção de Formulários</strong></td>
        <td style="padding: 12px 15px;"><strong>Tokens CSRF</strong> (via <code>@csrf</code> e Middleware)</td>
        <td style="padding: 12px 15px;">
          <strong>Prevenção de Ataques:</strong> Garante que requisições que alteram dados (<code>POST</code>, <code>PUT</code>, <code>DELETE</code>) só possam se originar de dentro da própria aplicação. Isso previne que um site malicioso externo engane um usuário logado a executar ações indesejadas (ex: excluir um agendamento).
        </td>
      </tr>
    </tbody>
  </table>
</div>

---

## 💡 Notas de Arquitetura e Curiosidades

* **Validação Desacoplada:** O projeto faz uso extensivo de *Form Requests* (ex: `StoreUserRequest`, `StoreAppointmentRequest`). Esta é uma *best practice* do Laravel que move toda a lógica de validação de dados para fora dos Controladores, tornando-os mais limpos, legíveis e fáceis de testar.
* **Consultas Eficientes:** A funcionalidade de Relatórios (`ReportController`) utiliza *Model Scopes* (ex: `scopeFiltroRecursos`, `scopeFiltroUsuarios`) definidos diretamente nos Modelos. Isso torna as consultas ao banco de dados dinâmicas, eficientes e reutilizáveis.
* **Seeders Prontos para Produção:** O projeto inclui *seeders* como o `NreIratiSeeder`, que populam o banco com dados reais (municípios e escolas do NRE de Irati), demonstrando um foco na implantação prática.
* **Tempo de Desenvolvimento:**
    * **Início:** 31/07/2025
    * **Conclusão (v1.0):** 26/11/2025
    * **Total de Horas (Aprox.):** 250 horas
    * **Total de dias decorridos:** 119 dias

---

## 👨‍💻 Autor

<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9;">
    <tr>
      <td style="padding: 20px; width: 100px; text-align: center;">
        <img src="https://avatars.githubusercontent.com/u/142981329?v=4" width="90" alt="Avatar do Victor" style="border-radius: 50%;">
      </td>
      <td style="padding: 20px; color: #333;">
        <strong style="font-size: 1.3em; color: #0169b4;">Victor Henrique Jesus Santiago</strong><br>
        Desenvolvedor Full Stack<br><br>
        📧 <a href="mailto:victorhenriquedejesussantiago@gmail.com" style="color: #0169b4; text-decoration: none;">victorhenriquedejesussantiago@gmail.com</a><br>
        👔 <a href="https://www.linkedin.com/in/victorhjsantiago/" style="color: #0169b4; text-decoration: none;">LinkedIn/victorhjsantiago</a><br>
        🐙 <a href="https://github.com/victorhjsantiago" style="color: #0169b4; text-decoration: none;">GitHub/victorhjsantiago</a>
      </td>
    </tr>
  </table>
</div>
