<div align="center">
  <br />
  <img src="https://raw.githubusercontent.com/victorhjsantiago/nredutech/main/public/images/nredutech.png" alt="Logo NREduTech" width="150">
  <h1>NREduTech</h1>
  <strong>
    Sistema de Gestão Acadêmica e Agendamento de Recursos Didáticos
  </strong>
  <br />
  <br />

  <p>
    Uma solução robusta e centralizada, desenvolvida sob a arquitetura <strong>Laravel MVC</strong>, destinada à gestão integrada de escolas, turmas, recursos didáticos e agendamentos para o <strong>Núcleo Regional de Educação (NRE)</strong>.
  </p>

  <p>
    <img src="https://img.shields.io/badge/status-em%20desenvolvimento-yellow?style=for-the-badge" alt="Status do Projeto: Em Desenvolvimento">
    <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php" alt="Versão do PHP">
    <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel" alt="Versão do Laravel">
    <img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql" alt="Banco de Dados">
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
* **♿ Acessibilidade:** Integração nativa com o **VLibras** para garantir a acessibilidade para pessoas com deficiência auditiva.

---

## 🛠️ Requisitos e Regras de Negócio

A lógica do sistema foi modelada para refletir as hierarquias e processos de um ambiente educacional real.

### Regras de Negócio Principais

* **Aprovação de Usuários:** Professores e Diretores podem se autocadastrar, mas suas contas são criadas com status `pendente`. Um `Administrador` deve aprovar manualmente o cadastro para que o usuário possa acessar o sistema.
* **Hierarquia de Permissões:**
    * **Administrador:** Possui controle total. Gerencia escolas, municípios e usuários de todos os níveis. É o único que pode realizar backups e restaurar o sistema.
    * **Diretor:** Gerencia turmas, professores e recursos *apenas* da sua própria escola. Pode visualizar relatórios.
    * **Professor:** O foco é no agendamento. Pode agendar recursos para suas turmas/disciplinas (Ofertas) e gerenciar os recursos/disciplinas que ele mesmo cadastrou.
* **Propriedade de Recursos:** Recursos e Disciplinas podem ser "Globais" (pertencem ao NRE, disponíveis para todos) ou pertencer a uma escola específica (visíveis apenas para usuários daquela escola).
* **Conflito de Agendamento:** O sistema impede ativamente que um mesmo recurso seja agendado por duas pessoas no mesmo horário (validação de conflito de datas/horas).
* **Integridade de Dados:** Não é possível excluir uma Escola se ela possuir Turmas ou Usuários vinculados (proteção de chave estrangeira).

### Requisitos Funcionais (RF)

<div style="width: 100%; overflow-x: auto;">
  <table width="100%">
    <thead>
      <tr>
        <th align="left">ID</th>
        <th align="left">Descrição</th>
        <th align="left">Perfil(s)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>RF-001</td>
        <td>O sistema deve permitir a autenticação de usuários por e-mail e senha.</td>
        <td>Todos</td>
      </tr>
      <tr>
        <td>RF-002</td>
        <td>O sistema deve permitir o autocadastro de Professores e Diretores (com status inicial "pendente").</td>
        <td>Visitante</td>
      </tr>
      <tr>
        <td>RF-003</td>
        <td>O sistema deve permitir que Administradores aprovem ou rejeitem cadastros pendentes.</td>
        <td>Administrador</td>
      </tr>
      <tr>
        <td>RF-004</td>
        <td>O sistema deve permitir ao usuário alterar suas próprias informações de perfil (nome, senha, etc.).</td>
        <td>Todos</td>
      </tr>
      <tr>
        <td>RF-005</td>
        <td>O sistema deve permitir o gerenciamento (CRUD) de Municípios e Escolas.</td>
        <td>Administrador</td>
      </tr>
      <tr>
        <td>RF-006</td>
        <td>O sistema deve permitir o gerenciamento (CRUD) de Turmas, vinculando-as a uma escola.</td>
        <td>Admin, Diretor</td>
      </tr>
      <tr>
        <td>RF-007</td>
        <td>O sistema deve permitir o gerenciamento (CRUD) de Componentes Curriculares (Disciplinas).</td>
        <td>Admin, Diretor</td>
      </tr>
      <tr>
        <td>RF-008</td>
        <td>O sistema deve permitir o gerenciamento (CRUD) de Recursos Didáticos.</td>
        <td>Admin, Diretor, Professor</td>
      </tr>
      <tr>
        <td>RF-009</td>
        <td>O sistema deve permitir a associação de Professores a Turmas/Disciplinas (Ofertas).</td>
        <td>Admin, Diretor</td>
      </tr>
      <tr>
        <td>RF-010</td>
        <td>O sistema deve permitir a criação de Agendamentos de recursos, vinculando-os a uma "Oferta".</td>
        <td>Professor</td>
      </tr>
      <tr>
        <td>RF-011</td>
        <td>O sistema deve exibir um calendário com todos os agendamentos.</td>
        <td>Todos</td>
      </tr>
      <tr>
        <td>RF-012</td>
        <td>O sistema deve permitir o cancelamento de agendamentos pelo criador ou por um superior.</td>
        <td>Professor, Diretor, Admin</td>
      </tr>
      <tr>
        <td>RF-013</td>
        <td>O sistema deve gerar relatórios dinâmicos com múltiplos filtros.</td>
        <td>Admin, Diretor</td>
      </tr>
      <tr>
        <td>RF-014</td>
        <td>O sistema deve permitir a exportação de relatórios em formatos PDF, XLSX, ODS, CSV e HTML.</td>
        <td>Admin, Diretor</td>
      </tr>
      <tr>
        <td>RF-015</td>
        <td>O sistema deve exibir notificações na plataforma e enviá-las por e-mail.</td>
        <td>Todos</td>
      </tr>
      <tr>
        <td>RF-016</td>
        <td>O sistema deve permitir a criação manual e agendada de backups.</td>
        <td>Administrador</td>
      </tr>
      <tr>
        <td>RF-017</td>
        <td>O sistema deve permitir a restauração de um backup a partir de um arquivo SQL.</td>
        <td>Administrador</td>
      </tr>
    </tbody>
  </table>
</div>

### Requisitos Não-Funcionais (RNF)

<div style="width: 100%; overflow-x: auto;">
  <table width="100%">
    <thead>
      <tr>
        <th align="left">ID</th>
        <th align="left">Descrição</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>RNF-001</td>
        <td>A interface do usuário deve ser responsiva, adaptando-se a desktops, tablets e smartphones (Mobile-First).</td>
      </tr>
      <tr>
        <td>RNF-002</td>
        <td>As senhas dos usuários devem ser armazenadas de forma irreversível, utilizando hashing Bcrypt.</td>
      </tr>
      <tr>
        <td>RNF-003</td>
        <td>Todas as submissões de formulários devem ser protegidas contra ataques CSRF (Cross-Site Request Forgery).</td>
      </tr>
      <tr>
        <td>RNF-004</td>
        <td>O sistema deve ter alta disponibilidade, garantida por rotinas de backup automáticas (diárias ou semanais).</td>
      </tr>
      <tr>
        <td>RNF-005</td>
        <td>O código-fonte deve seguir os padrões PSR-12, ser modularizado (MVC) e utilizar validação em *Form Requests* para alta manutenibilidade.</td>
      </tr>
      <tr>
        <td>RNF-006</td>
        <td>O sistema deve ser acessível, fornecendo suporte ao VLibras em todas as páginas (logado ou não).</td>
      </tr>
      <tr>
        <td>RNF-007</td>
        <td>A aplicação deve criptografar dados sensíveis (como sessões e cookies) usando o padrão AES-256-CBC.</td>
      </tr>
    </tbody>
  </table>
</div>

---

## 🚀 Arquitetura e Justificativa Tecnológica

A seleção de tecnologias (o *stack*) do NREduTech foi deliberada para otimizar a performance, a segurança e a produtividade do desenvolvimento.

<div style="width: 100%; overflow-x: auto;">
  <table width="100%">
    <thead>
      <tr>
        <th align="left">Tecnologia</th>
        <th align="left">Por que foi escolhida? (Vantagens sobre concorrentes)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php" alt="PHP 8.2">
        </td>
        <td>
          <strong>Performance e Modernidade:</strong> O PHP 8.2 oferece melhorias drásticas de performance com o compilador <strong>JIT (Just-In-Time)</strong>. Seus recursos modernos (tipagem estrita, Enums, Readonly Properties) o tornam mais robusto e menos propenso a erros que o PHP 7.x.<br>
          <strong>Vantagem vs. Concorrentes (Python/Node.js):</strong> A facilidade de *deploy* (hospedagem) do PHP é incomparável, especialmente em ambientes de hospedagem compartilhada, comuns no setor público. Sua curva de aprendizado é mais rápida que a de frameworks como Django (Python), e seu modelo *multi-process* é mais simples de gerenciar para aplicações web tradicionais do que o *event-loop* do Node.js.
        </td>
      </tr>
      <tr>
        <td>
          <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 11">
        </td>
        <td>
          <strong>Ecossistema "Baterias Inclusas":</strong> Laravel é escolhido por seu ecossistema completo. O <strong>Eloquent ORM</strong> é considerado mais elegante e produtivo que o Doctrine (Symfony) ou o TypeORM (Node.js). O *template engine* <strong>Blade</strong> é mais simples e extensível que o Twig. Ferramentas integradas como `artisan`, agendamento de tarefas e filas abstraem complexidades que em *frameworks* mais "agnósticos" (como Express.js) exigiriam implementação manual.
        </td>
      </tr>
      <tr>
        <td>
          <img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql" alt="MySQL 8">
        </td>
        <td>
          <strong>Confiabilidade e Popularidade:</strong> SGBDR mais popular do mundo para aplicações web. O MySQL 8 introduziu recursos avançados como *Window Functions* e CTEs, aproximando-o do PostgreSQL.<br>
          <strong>Vantagem vs. PostgreSQL:</strong> Embora o PostgreSQL seja tecnicamente mais avançado em certos aspectos (ex: tipos de dados complexos), o MySQL é frequentemente escolhido por sua simplicidade de configuração, vasta documentação e enorme base de profissionais, sendo o padrão de fato para a maioria das aplicações Laravel.
        </td>
      </tr>
      <tr>
        <td>
          <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss" alt="Tailwind CSS">
        </td>
        <td>
          <strong>Produtividade e Customização:</strong> Tailwind (usado via diretivas `@tailwind`) é superior a *frameworks* baseados em componentes (como Bootstrap ou Foundation) para este projeto. Em vez de fornecer componentes prontos (ex: `.card`, `.btn`) que precisam ser sobrescritos, o Tailwind fornece classes utilitárias de baixo nível. Isso permite criar designs 100% customizados e responsivos sem "lutar" contra estilos pré-definidos, resultando em um CSS final menor e mais manutenível.
        </td>
      </tr>
      <tr>
        <td>
          <img src="https://img.shields.io/badge/Vite-5.x-646CFF?style=for-the-badge&logo=vite" alt="Vite">
        </td>
        <td>
          <strong>Velocidade de Desenvolvimento:</strong> Vite é o *bundler* de frontend padrão do Laravel 11, substituindo o Webpack/Mix. Sua principal vantagem é o <strong>Hot Module Replacement (HMR)</strong> quase instantâneo. Ele usa o ESBuild (escrito em Go) para pré-compilar dependências, tornando o *build* e a atualização do servidor de desenvolvimento ordens de magnitude mais rápidos que o Webpack, que precisa re-compilar todo o *bundle* a cada mudança.
        </td>
      </tr>
      <tr>
        <td>
          <img src="https://img.shields.io/badge/Alpine.js-3.x-77C1D2?style=for-the-badge&logo=alpine.js" alt="Alpine.js">
        </td>
        <td>
          <strong>Reatividade Leve:</strong> Para a interatividade da interface (como o menu *dropdown* de perfil), o Alpine.js é a escolha ideal. Ele oferece reatividade diretamente no HTML, similar ao Vue.js, mas sem a complexidade de um *framework* JavaScript completo.<br>
          <strong>Vantagem vs. jQuery/React:</strong> É drasticamente mais moderno e leve que o jQuery. É superior ao React ou Vue para este projeto, pois o NREduTech é uma aplicação *server-side rendered* (Blade), e o Alpine é projetado para "polvilhar" interatividade sobre o HTML existente, em vez de assumir o controle total da renderização (como o React faria).
        </td>
      </tr>
      <tr>
        <td>
          <img src="https://img.shields.io/badge/Maatwebsite-Excel-217346?style=for-the-badge&logo=microsoftexcel" alt="Maatwebsite/Excel">
        </td>
        <td>
          <strong>Padrão da Comunidade:</strong> É a biblioteca de fato no ecossistema Laravel para exportação e importação de planilhas. Ela abstrai a complexidade da biblioteca PHPOffice/PhpSpreadsheet, permitindo a exportação de *views* Blade ou coleções Eloquent diretamente para um XLSX ou PDF com poucas linhas de código.
        </td>
      </tr>
      <tr>
        <td>
          <img src="https://img.shields.io/badge/Spatie-laravel--backup-F55302?style=for-the-badge" alt="Spatie Laravel Backup">
        </td>
        <td>
          <strong>Confiabilidade:</strong> A Spatie é referência em pacotes Laravel. Esta biblioteca é superior a *scripts cron* manuais porque cuida de todo o ciclo de vida do backup: agendamento (via Console Kernel), execução do *dump* do banco de dados, compactação de arquivos, notificação por e-mail e limpeza de backups antigos.
        </td>
      </tr>
    </tbody>
  </table>
</div>

---

## 🔒 Segurança e Criptografia

A segurança é um requisito não-funcional crítico. O NREduTech implementa as seguintes medidas padrão do Laravel:

1.  **Hashing de Senhas (Bcrypt):**
    * **O quê:** Todas as senhas de usuários são processadas usando Bcrypt, um algoritmo de *hashing* adaptativo e lento.
    * **Por que é melhor:** Ao contrário de algoritmos rápidos como MD5 ou SHA1 (agora obsoletos para senhas), o Bcrypt é intencionalmente lento e inclui um "sal" (salt) aleatório. Isso torna ataques de *Brute Force* e *Rainbow Table* computacionalmente inviáveis, protegendo as credenciais dos usuários mesmo em caso de vazamento do banco de dados.

2.  **Criptografia Simétrica (AES-256-CBC):**
    * **O quê:** Os dados de sessão e cookies de "lembrar-me" são criptografados usando o padrão AES-256-CBC com uma chave de aplicação única (`APP_KEY`).
    * **Por que é melhor:** Isso impede que um invasor leia o conteúdo da sessão de um usuário ou falsifique um cookie, pois ele não possui a chave secreta (`APP_KEY`) para descriptografar os dados.

3.  **Proteção contra CSRF (Cross-Site Request Forgery):**
    * **O quê:** Todas as rotas `POST`, `PUT`, `PATCH` e `DELETE` são protegidas pelo *middleware* `VerifyCsrfToken`. O Blade (`@csrf`) insere um token oculto em todos os formulários.
    * **Por que é melhor:** Isso garante que uma requisição que altera dados (como excluir um usuário) só possa se originar de dentro da própria aplicação, e não de um site malicioso externo que tente enganar um administrador logado.

---

## 💡 Notas de Arquitetura e Curiosidades

* **Validação Desacoplada:** O projeto faz uso extensivo de *Form Requests* (ex: `StoreUserRequest`, `StoreAppointmentRequest`). Esta é uma *best practice* do Laravel que move toda a lógica de validação de dados para fora dos Controladores, tornando-os mais limpos, legíveis e fáceis de testar.
* **Consultas Eficientes:** A funcionalidade de Relatórios (`ReportController`) utiliza *Model Scopes* (ex: `scopeFiltroRecursos`, `scopeFiltroUsuarios`) definidos diretamente nos Modelos. Isso torna as consultas ao banco de dados dinâmicas, eficientes e reutilizáveis, evitando a necessidade de escrever *queries* SQL complexas no controlador.
* **Seeders Prontos para Produção:** O projeto inclui *seeders* como o `NreIratiSeeder`, que populam o banco com dados reais (municípios e escolas do NRE de Irati). Isso demonstra um foco na implantação prática e usabilidade imediata do sistema.
* **Tempo de Desenvolvimento:**
    * **Início:** 31/07/2025
    * **Conclusão (v1.0):** 26/11/2025
    * **Total de Horas (Aprox.):** 250 horas

---

## 👨‍💻 Autor

| Avatar | Nome | Contato |
| :--- | :--- | :--- |
| <img src="https://avatars.githubusercontent.com/u/142981329?v=4" width="75" style="border-radius: 50%;"> | **Victor Hugo Jesus Santiago** | `victorhjsantiago@gmail.com` <br> [LinkedIn](https://www.linkedin.com/in/victorhjsantiago/) <br> [GitHub](https://github.com/victorhjsantiago) |
