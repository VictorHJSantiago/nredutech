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

  <p>
    <a href="README.md">🇺🇸 English</a>
    ·
    <strong>🇧🇷 Português</strong>
    ·
    <a href="README.es.md">🇪🇸 Español</a>
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

### Regras de Negócio (RN)
<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #0169b4; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">ID</th>
        <th style="padding: 12px 15px; text-align: left;">Ator(es) afetado(s)</th>
        <th style="padding: 12px 15px; text-align: left;">Descrição da regra</th>
        <th style="padding: 12px 15px; text-align: left;">Justificativa/origem</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-001</td>
        <td style="padding: 12px 15px;">Usuário (todos)</td>
        <td style="padding: 12px 15px;">Ao atualizar o e-mail no perfil, a conta do usuário deve ser marcada como "não verificada", exigindo nova confirmação.</td>
        <td style="padding: 12px 15px;">Garantir a posse e validade do novo endereço de e-mail.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-002</td>
        <td style="padding: 12px 15px;">Usuário (Todos)</td>
        <td style="padding: 12px 15px;">Para excluir a própria conta, o usuário deve confirmar sua senha atual.</td>
        <td style="padding: 12px 15px;">Medida de segurança para evitar exclusão acidental ou maliciosa.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-003</td>
        <td style="padding: 12px 15px;">Usuário (novo)</td>
        <td style="padding: 12px 15px;">Campos de registro (username, e-mail, CPF, RG, etc.) devem ser únicos no sistema.</td>
        <td style="padding: 12px 15px;">Garantir a unicidade de cada usuário na base de dados.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-004</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Apenas administradores podem visualizar e gerenciar usuários de todas as escolas.</td>
        <td style="padding: 12px 15px;">Centralização do controle de acesso e gestão de contas no NRE.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-005</td>
        <td style="padding: 12px 15px;">Diretor, professor</td>
        <td style="padding: 12px 15px;">Diretores e professores só podem visualizar usuários da sua própria escola.</td>
        <td style="padding: 12px 15px;">Garantir o isolamento de dados (privacidade) entre instituições.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-006</td>
        <td style="padding: 12px 15px;">Diretor</td>
        <td style="padding: 12px 15px;">Diretores só podem criar usuários (ex: professores) para a sua própria escola.</td>
        <td style="padding: 12px 15px;">Delegação da gestão de pessoal no nível da escola.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-007</td>
        <td style="padding: 12px 15px;">Diretor</td>
        <td style="padding: 12px 15px;">Diretores não podem criar ou promover usuários ao nível de "administrador".</td>
        <td style="padding: 12px 15px;">Manter a hierarquia de permissões e a segurança do sistema.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-008</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Um usuário administrador (ou qualquer usuário) não pode excluir a si mesmo.</td>
        <td style="padding: 12px 15px;">Prevenir o bloqueio acidental do sistema.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-009</td>
        <td style="padding: 12px 15px;">Administrador, diretor</td>
        <td style="padding: 12px 15px;">O sistema deve impedir a exclusão de usuários que possuam dependências (recursos criados ou ofertas).</td>
        <td style="padding: 12px 15px;">Garantir a integridade referencial e o histórico de ações.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-010</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Apenas Administradores podem gerenciar (CRUD) municípios e escolas.</td>
        <td style="padding: 12px 15px;">Centralização da gestão da infraestrutura de unidades do NRE.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-011</td>
        <td style="padding: 12px 15px;">Administrador (ao criar escola)</td>
        <td style="padding: 12px 15px;">Uma Escola deve, obrigatoriamente, estar associada a um município.</td>
        <td style="padding: 12px 15px;">Requisito de organização estrutural do NRE.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-012</td>
        <td style="padding: 12px 15px;">Administrador (ao criar escola)</td>
        <td style="padding: 12px 15px;">Os campos nível ensino e tipo de uma escola devem ser valores pré-definidos (enum).</td>
        <td style="padding: 12px 15px;">Garantir a padronização e consistência dos dados para relatórios.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-013</td>
        <td style="padding: 12px 15px;">Diretor, professor</td>
        <td style="padding: 12px 15px;">Diretores e professores só podem gerenciar (visualizar, criar, editar) turmas da sua própria escola.</td>
        <td style="padding: 12px 15px;">Manter o escopo de gestão restrito à própria instituição.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-014</td>
        <td style="padding: 12px 15px;">Usuário (ao criar turma)</td>
        <td style="padding: 12px 15px;">O ano letivo deve ser um número inteiro dentro de um intervalo válido (ex: 2000-2100).</td>
        <td style="padding: 12px 15px;">Garantir a validade e consistência dos dados de ano letivo.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-015</td>
        <td style="padding: 12px 15px;">Usuário (ao excluir turma)</td>
        <td style="padding: 12px 15px;">O sistema deve impedir a exclusão de turmas que possuam ofertas de componentes.</td>
        <td style="padding: 12px 15px;">Proteger o histórico de alocação de disciplinas e professores.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-016</td>
        <td style="padding: 12px 15px;">Administrador, diretor, professor</td>
        <td style="padding: 12px 15px;">Disciplinas podem ser "globais" ou "específicas" (vinculadas a uma escola).</td>
        <td style="padding: 12px 15px;">Permitir componentes curriculares comuns a todas as escolas e componentes únicos.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-017</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Apenas administradores podem criar ou editar disciplinas globais.</td>
        <td style="padding: 12px 15px;">Controle centralizado sobre o currículo básico regional.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-018</td>
        <td style="padding: 12px 15px;">Diretor, professor</td>
        <td style="padding: 12px 15px;">Diretores e professores visualizam disciplinas globais e as específicas da sua escola.</td>
        <td style="padding: 12px 15px;">Fornecer acesso ao currículo relevante para a instituição.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-019</td>
        <td style="padding: 12px 15px;">Usuário (ao excluir disciplina)</td>
        <td style="padding: 12px 15px;">O sistema deve impedir a exclusão de disciplinas que possuam ofertas vinculadas.</td>
        <td style="padding: 12px 15px;">Garantir a integridade do histórico de turmas.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-020</td>
        <td style="padding: 12px 15px;">Professor</td>
        <td style="padding: 12px 15px;">Professores só podem criar ofertas de componentes para si mesmos (e não para outros professores).</td>
        <td style="padding: 12px 15px;">Garantir que o professor só gerencie suas próprias atribuições.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-021</td>
        <td style="padding: 12px 15px;">Usuário (ao excluir oferta)</td>
        <td style="padding: 12px 15px;">O sistema deve impedir a exclusão de ofertas que possuam agendamentos vinculados.</td>
        <td style="padding: 12px 15px;">Proteger o histórico de uso de recursos em agendamentos.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-022</td>
        <td style="padding: 12px 15px;">Usuário (ao criar recurso)</td>
        <td style="padding: 12px 15px;">A quantidade de um recurso deve ser um número inteiro igual ou maior que 1.</td>
        <td style="padding: 12px 15px;">Garantir que o inventário de recursos tenha valores válidos.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-023</td>
        <td style="padding: 12px 15px;">Usuário (ao excluir recurso)</td>
        <td style="padding: 12px 15px;">O sistema deve impedir a exclusão de recursos que possuam agendamentos vinculados.</td>
        <td style="padding: 12px 15px;">Garantir a integridade do histórico de agendamentos.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-024</td>
        <td style="padding: 12px 15px;">Usuário (ao criar agendamento)</td>
        <td style="padding: 12px 15px;">A data/hora de fim de um agendamento deve ser, obrigatoriamente, após a data/hora de início.</td>
        <td style="padding: 12px 15px;">Garantir a lógica temporal e a validade do período agendado.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-025</td>
        <td style="padding: 12px 15px;">Usuário (ao criar agendamento)</td>
        <td style="padding: 12px 15px;">A data/hora de início deve ser, no mínimo, 10 minutos no futuro em relação ao momento da criação.</td>
        <td style="padding: 12px 15px;">Evitar agendamentos retroativos ou instantâneos impossíveis de atender.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-026</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">O sistema não deve permitir agendar o mesmo recurso em horários sobrepostos (conflitantes).</td>
        <td style="padding: 12px 15px;">Prevenção de conflitos de alocação (dupla reserva).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-027</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">Não é permitido criar agendamentos em horários específicos (ex: madrugada, entre 23:00 e 06:00).</td>
        <td style="padding: 12px 15px;">Restrição de segurança e adequação ao horário de funcionamento.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-028</td>
        <td style="padding: 12px 15px;">Usuário (ao cancelar agendamento)</td>
        <td style="padding: 12px 15px;">Um agendamento não pode ser cancelado com menos de 10 minutos de antecedência do seu início.</td>
        <td style="padding: 12px 15px;">Evitar cancelamentos de última hora que prejudicam a alocação de recursos.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-029</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">A criação e cancelamento de agendamentos deve disparar notificações aos envolvidos.</td>
        <td style="padding: 12px 15px;">Manter os usuários informados sobre mudanças no calendário.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-030</td>
        <td style="padding: 12px 15px;">Diretor</td>
        <td style="padding: 12px 15px;">Relatórios gerados por diretores devem conter apenas dados da sua própria escola.</td>
        <td style="padding: 12px 15px;">Garantir o isolamento de dados e a privacidade entre instituições.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-031</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Apenas administradores podem acessar a área de configurações (backups, etc.).</td>
        <td style="padding: 12px 15px;">Restringir o acesso a funcionalidades críticas do sistema.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-032</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">O sistema deve notificar o administrador por e-mail quando um backup for concluído com sucesso.</td>
        <td style="padding: 12px 15px;">Fornecer confirmação e monitoramento de tarefas críticas.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-033</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">O sistema deve impedir a exclusão de municípios que possuam escolas vinculadas.</td>
        <td style="padding: 12px 15px;">Garantir a integridade referencial da localização das escolas.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-034</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">O sistema deve impedir a exclusão de escolas que possuam turmas ou usuários vinculados.</td>
        <td style="padding: 12px 15px;">Proteger dados associados (turmas, usuários) da instituição.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-035</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">O sistema deve impedir a criação de ofertas duplicadas (mesma disciplina, professor e turma).</td>
        <td style="padding: 12px 15px;">Evitar redundância e inconsistência nos dados pedagógicos.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-036</td>
        <td style="padding: 12px 15px;">Administrador, diretor, professor</td>
        <td style="padding: 12px 15px;">(A edição de uma disciplina é permitida apenas ao seu criador, ao diretor da escola ou a um administrador.</td>
        <td style="padding: 12px 15px;">Controle de quem pode alterar os dados de um componente curricular.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-037</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Apenas administradores podem alterar a escola associada a uma disciplina (ou torná-la global).</td>
        <td style="padding: 12px 15px;">Controle centralizado sobre a estrutura curricular regional.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-038</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">Novas disciplinas cadastradas por professores ou diretores iniciam com status "Pendente".</td>
        <td style="padding: 12px 15px;">Garantir o controle e a padronização do catálogo de componentes.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-039</td>
        <td style="padding: 12px 15px;">Usuário (ao criar recurso)</td>
        <td style="padding: 12px 15px;">Ao cadastrar um recurso com quantidade maior que 1, o sistema deve oferecer a opção de criar itens individuais ou um lote único.</td>
        <td style="padding: 12px 15px;">Facilitar o cadastro em massa de inventário (usabilidade).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-040</td>
        <td style="padding: 12px 15px;">Sistema</td>
        <td style="padding: 12px 15px;">Novos usuários cadastrados (Registro Público) iniciam com status "Pendente" e devem ser aprovados.</td>
        <td style="padding: 12px 15px;">Medida de segurança para validar novos usuários antes de conceder acesso.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-041</td>
        <td style="padding: 12px 15px;">Administrador, diretor</td>
        <td style="padding: 12px 15px;">Diretores só podem excluir usuários (que não sejam administradores) de sua própria escola.</td>
        <td style="padding: 12px 15px;">Manter a hierarquia de permissões e o escopo de gestão.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-042</td>
        <td style="padding: 12px 15px;">Administrador, diretor, professor</td>
        <td style="padding: 12px 15px;">Um agendamento só pode ser cancelado pelo seu criador (professor), pelo diretor da escola ou por um administrador.</td>
        <td style="padding: 12px 15px;">Definir responsabilidade sobre o cancelamento de reservas.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-043</td>
        <td style="padding: 12px 15px;">Administrador, diretor</td>
        <td style="padding: 12px 15px;">O acesso ao módulo de relatórios é restrito a administradores e diretores.</td>
        <td style="padding: 12px 15px;">Proteger o acesso a dados analíticos e consolidados.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RN-044</td>
        <td style="padding: 12px 15px;">Administrador</td>
        <td style="padding: 12px 15px;">Ações críticas (executar backup, baixar backup, restaurar) exigem que o administrador confirme sua senha atual.</td>
        <td style="padding: 12px 15px;">Medida de segurança (step-up authentication) para operações sensíveis.</td>
      </tr>
      <tr style="background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RN-045</td>
        <td style="padding: 12px 15px;">Usuário (novo ao alterar senha)</td>
        <td style="padding: 12px 15px;">A senha do usuário deve ter no mínimo 16 caracteres.</td>
        <td style="padding: 12px 15px;">Garantir um nível mínimo de complexidade e segurança para as senhas.</td>
      </tr>
    </tbody>
  </table>
</div>

### Requisitos Funcionais (RF)
<div style="width: 100%; overflow-x: auto;">
  <table width="100%" style="border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <thead style="background-color: #0169b4; color: white;">
      <tr>
        <th style="padding: 12px 15px; text-align: left;">ID</th>
        <th style="padding: 12px 15px; text-align: left;">Módulo</th>
        <th style="padding: 12px 15px; text-align: left;">Nome do requisito</th>
        <th style="padding: 12px 15px; text-align: left;">Descrição</th>
        <th style="padding: 12px 15px; text-align: left;">Prioridade</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-001</td>
        <td style="padding: 12px 15px;">Autenticação</td>
        <td style="padding: 12px 15px;">Cadastro de usuário (público)</td>
        <td style="padding: 12px 15px;">O sistema deve permitir que usuários (professores, diretores) se cadastrem através de um formulário público.</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-002</td>
        <td style="padding: 12px 15px;">Autenticação</td>
        <td style="padding: 12px 15px;">Login de usuário</td>
        <td style="padding: 12px 15px;">O sistema deve permitir que usuários autenticados façam login com e-mail e senha.</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-003</td>
        <td style="padding: 12px 15px;">Autenticação</td>
        <td style="padding: 12px 15px;">Recuperação de senha</td>
        <td style="padding: 12px 15px;">O sistema deve permitir que usuários recuperem suas senhas através de um fluxo de "Esqueci minha senha".</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-004</td>
        <td style="padding: 12px 15px;">Perfil</td>
        <td style="padding: 12px 15px;">Atualizar informações do perfil</td>
        <td style="padding: 12px 15px;">O usuário deve poder visualizar e atualizar suas informações de perfil (nome, e-mail, telefone).</td>
        <td style="padding: 12px 15px;">Média</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-005</td>
        <td style="padding: 12px 15px;">Perfil</td>
        <td style="padding: 12px 15px;">Atualizar senha</td>
        <td style="padding: 12px 15px;">O usuário deve poder atualizar sua senha, fornecendo a senha atual.</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-006</td>
        <td style="padding: 12px 15px;">Perfil</td>
        <td style="padding: 12px 15px;">Excluir conta</td>
        <td style="padding: 12px 15px;">O usuário pode excluir sua própria conta por um administrador/diretor.</td>
        <td style="padding: 12px 15px;">Média</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-007</td>
        <td style="padding: 12px 15px;">Gestão de usuários</td>
        <td style="padding: 12px 15px;">CRUD de usuários</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o CRUD de usuários.</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-008</td>
        <td style="padding: 12px 15px;">Gestão de usuários</td>
        <td style="padding: 12px 15px;">Filtrar usuários</td>
        <td style="padding: 12px 15px;">O sistema deve permitir filtrar a lista de usuários (por nome, e-mail, status, tipo, CPF, RG, formação, etc.).</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-009</td>
        <td style="padding: 12px 15px;">Gestão escolar</td>
        <td style="padding: 12px 15px;">CRUD de municípios</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o CRUD de municípios.</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-010</td>
        <td style="padding: 12px 15px;">Gestão escolar</td>
        <td style="padding: 12px 15px;">CRUD de escolas</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o CRUD de escolas.</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-011</td>
        <td style="padding: 12px 15px;">Gestão de turmas</td>
        <td style="padding: 12px 15px;">CRUD de turmas</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o CRUD de turmas.</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-012</td>
        <td style="padding: 12px 15px;">Gestão de turmas</td>
        <td style="padding: 12px 15px;">Detalhar turma (ofertas)</td>
        <td style="padding: 12px 15px;">O sistema deve permitir visualizar os detalhes de uma turma e suas ofertas.</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-013</td>
        <td style="padding: 12px 15px;">Disciplinas</td>
        <td style="padding: 12px 15px;">CRUD de componentes curriculares</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o CRUD de componentes curriculares (disciplinas).</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-014</td>
        <td style="padding: 12px 15px;">Disciplinas</td>
        <td style="padding: 12px 15px;">Filtrar componentes</td>
        <td style="padding: 12px 15px;">O sistema deve permitir a filtragem de componentes (nome/descrição, carga horária, status, escola).</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-015</td>
        <td style="padding: 12px 15px;">Oferta de componentes</td>
        <td style="padding: 12px 15px;">CRUD de ofertas</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o CRUD de ofertas.</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-016</td>
        <td style="padding: 12px 15px;">Recursos didáticos</td>
        <td style="padding: 12px 15px;">CRUD de recursos didáticos</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o CRUD de recursos didáticos e laboratórios.</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-017</td>
        <td style="padding: 12px 15px;">Agendamento</td>
        <td style="padding: 12px 15px;">Gerenciar agendamento</td>
        <td style="padding: 12px 15px;">O sistema deve permitir que usuários criem, visualizem e cancelem agendamentos de recursos didáticos.</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-018</td>
        <td style="padding: 12px 15px;">Agendamento</td>
        <td style="padding: 12px 15px;">Calendário de agendamento</td>
        <td style="padding: 12px 15px;">O sistema deve exibir os agendamentos em uma interface de calendário interativo (FullCalendar).</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-019</td>
        <td style="padding: 12px 15px;">Relatórios</td>
        <td style="padding: 12px 15px;">Visualizar relatórios</td>
        <td style="padding: 12px 15px;">O sistema deve permitir a visualização (preview) de relatórios analíticos com filtros avançados e gráficos.</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-020</td>
        <td style="padding: 12px 15px;">Relatórios</td>
        <td style="padding: 12px 15px;">Exportar relatórios</td>
        <td style="padding: 12px 15px;">O sistema deve permitir a exportação de relatórios em múltiplos formatos (PDF, XLSX, CSV, ODS, HTML).</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-021</td>
        <td style="padding: 12px 15px;">Configurações</td>
        <td style="padding: 12px 15px;">Gestão de backup</td>
        <td style="padding: 12px 15px;">O sistema deve permitir a gestão de backups (criar manualmente, baixar e excluir).</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-022</td>
        <td style="padding: 12px 15px;">Configurações</td>
        <td style="padding: 12px 15px;">Restauração de backup</td>
        <td style="padding: 12px 15px;">O sistema deve permitir a restauração de dados a partir de um arquivo de backup.</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-023</td>
        <td style="padding: 12px 15px;">Notificações</td>
        <td style="padding: 12px 15px;">Exibir notificações</td>
        <td style="padding: 12px 15px;">O sistema deve exibir notificações aos usuários (via interface e e-mail) sobre eventos relevantes.</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-024</td>
        <td style="padding: 12px 15px;">Notificações</td>
        <td style="padding: 12px 15px;">Marcar notificações como lidas</td>
        <td style="padding: 12px 15px;">O sistema deve marcar notificações como lidas (automaticamente ao visualizar a lista).</td>
        <td style="padding: 12px 15px;">Média</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-025</td>
        <td style="padding: 12px 15px;">Gestão de usuários</td>
        <td style="padding: 12px 15px;">Aprovação de usuários</td>
        <td style="padding: 12px 15px;">O sistema deve permitir que administradores e diretores aprovem ou rejeitem/bloqueiem novos cadastros pendentes.</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-026</td>
        <td style="padding: 12px 15px;">Disciplinas</td>
        <td style="padding: 12px 15px;">Aprovação de disciplinas</td>
        <td style="padding: 12px 15px;">O sistema deve permitir que usuários autorizados (administrador, diretor) aprovem ou reprovem componentes com status "Pendente".</td>
        <td style="padding: 12px 15px;">Alta</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-027</td>
        <td style="padding: 12px 15px;">Agendamento</td>
        <td style="padding: 12px 15px;">Consultar disponibilidade</td>
        <td style="padding: 12px 15px;">O sistema deve exibir a disponibilidade de recursos (disponíveis e agendados) para um dia específico.</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-028</td>
        <td style="padding: 12px 15px;">Recursos didáticos</td>
        <td style="padding: 12px 15px;">Cadastro em lote</td>
        <td style="padding: 12px 15px;">O sistema deve permitir o cadastro de múltiplos recursos individuais a partir de um único formulário (via checkbox de quantidade).</td>
        <td style="padding: 12px 15px;">Média</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-029</td>
        <td style="padding: 12px 15px;">Notificações</td>
        <td style="padding: 12px 15px;">Limpar notificações</td>
        <td style="padding: 12px 15px;">O sistema deve permitir ao usuário excluir notificações individualmente ou limpar todo o histórico.</td>
        <td style="padding: 12px 15px;">Média</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RF-030</td>
        <td style="padding: 12px 15px;">Agendamento</td>
        <td style="padding: 12px 15px;">Consultar disponibilidade diária</td>
        <td style="padding: 12px 15px;">O sistema deve exibir a disponibilidade de recursos (disponíveis e agendados) para um dia específico selecionado.</td>
        <td style="padding: 12px 15px;">Essencial</td>
      </tr>
      <tr style="background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RF-031</td>
        <td style="padding: 12px 15px;">Notificações</td>
        <td style="padding: 12px 15px;">Excluir notificações</td>
        <td style="padding: 12px 15px;">O sistema deve permitir ao usuário excluir notificações (individualmente ou "Limpar Todas").</td>
        <td style="padding: 12px 15px;">Média</td>
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
        <th style="padding: 12px 15px; text-align: left;">Atributo de qualidade</th>
        <th style="padding: 12px 15px; text-align: left;">Descrição do requisito</th>
        <th style="padding: 12px 15px; text-align: left;">Métrica de verificação</th>
      </tr>
    </thead>
    <tbody style="background-color: #fff; color: #333;">
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-001</td>
        <td style="padding: 12px 15px;">Segurança (controle de acesso)</td>
        <td style="padding: 12px 15px;">O sistema deve possuir um controle de acesso robusto baseado em papéis (administrador, diretor, professor).</td>
        <td style="padding: 12px 15px;">Testes de integração validando que cada perfil só acessa as rotas e dados permitidos (testes de status HTTP 403).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RNF-002</td>
        <td style="padding: 12px 15px;">Segurança (dados)</td>
        <td style="padding: 12px 15px;">Senhas de usuários devem ser armazenadas scandals hashing forte e moderno (Argon2id).</td>
        <td style="padding: 12px 15px;">Revisão de código e testes de unidade que verificam se o hash é gerado corretamente.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-003</td>
        <td style="padding: 12px 15px;">Segurança (dados)</td>
        <td style="padding: 12px 15px;">Dados pessoais sensíveis (como CPF e RG) devem ser armazenados de forma criptografada (ex: AES-256-CBC).</td>
        <td style="padding: 12px 15px;">Auditoria da implementação e verificação manual do banco de dados para confirmar que os dados não estão em texto plano.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RNF-004</td>
        <td style="padding: 12px 15px;">Segurança (web)</td>
        <td style="padding: 12px 15px;">O sistema deve ser protegido contra ataques comuns (CSRF, XSS, SQL Injection).</td>
        <td style="padding: 12px 15px;">Revisão de código (uso de Eloquent ORM, Blade, middleware, CRSF) e execução de testes de penetração básicos.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-005</td>
        <td style="padding: 12px 15px;">Integridade</td>
        <td style="padding: 12px 15px;">O sistema deve garantir a integridade referencial, impedindo a exclusão de dados “pais”/registros “filhos”.</td>
        <td style="padding: 12px 15px;">Testes de integração (Feature Tests) que tentam excluir registros com dependências e validam o recebimento de erro.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RNF-006</td>
        <td style="padding: 12px 15px;">Confiabilidade (backup)</td>
        <td style="padding: 12px 15px;">O sistema deve fornecer mecanismos para backup (manual) e restauração da base de dados.</td>
        <td style="padding: 12px 15px;">Testes funcionais da interface de "Backup e Restauração". Verificação da criação dos arquivos de backup no servidor.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-007</td>
        <td style="padding: 12px 15px;">Manutenibilidade (testabilidade)</td>
        <td style="padding: 12px 15px;">O código deve ser testável, padrões de testes unitários e de integração (PHPUnit).</td>
        <td style="padding: 12px 15px;">Execução da suíte de testes e verificação da cobertura de código.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RNF-008</td>
        <td style="padding: 12px 15px;">Localização</td>
        <td style="padding: 12px 15px;">O sistema deve ter seu idioma principal definido como Português (Brasil).</td>
        <td style="padding: 12px 15px;">Verificação dos arquivos de linguagem e da interface do usuário.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-009</td>
        <td style="padding: 12px 15px;">Plataforma (tecnologia)</td>
        <td style="padding: 12px 15px;">O sistema deve ser desenvolvido scandals o framework Laravel (PHP), MariaDB, e ferramentas frontend como Vite.js e Alpine.js.</td>
        <td style="padding: 12px 15px;">Verificação dos arquivos de configuração do projeto (exemplo: composer.json, package.json).</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd;">
        <td style="padding: 12px 15px;">RNF-010</td>
        <td style="padding: 12px 15px;">Desempenho (interface)</td>
        <td style="padding: 12px 15px;">O módulo de agendamentos deve usar AJAX (Axios) para carregar a disponibilidade de recursos sem recarregar a página.</td>
        <td style="padding: 12px 15px;">Teste funcional do calendário (clicar em um dia) e verificação de que uma requisição é feita.</td>
      </tr>
      <tr style="border-bottom: 1px solid #ddd; background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-011</td>
        <td style="padding: 12px 15px;">Usabilidade (visualização de dados)</td>
        <td style="padding: 12px 15px;">O módulo de relatórios deve usar gráficos (ex: Chart.js) para facilitar.</td>
        <td style="padding: 12px 15px;">Teste funcional da página de relatórios e verificação dos gráficos.</td>
      </tr>
      <tr style="background-color: #f9f9f9;">
        <td style="padding: 12px 15px;">RNF-012</td>
        <td style="padding: 12px 15px;">Usabilidade (interação)</td>
        <td style="padding: 12px 15px;">O sistema deve usar modais (SweetAlert2) para ações destrutivas.</td>
        <td style="padding: 12px 15px;">Teste funcional e verificação de que o modal de confirmação é exibido.</td>
      </tr>
    </tbody>
  </table>
</div>

---

## 💻 Ambiente de Desenvolvimento

O projeto foi desenvolvido scandals um conjunto de ferramentas moderno, focado em segurança e produtividade, em um ambiente híbrido.

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
        👔 <a href="https://www.linkedin.com/in/victor-henrique-de-jesus-santiago/" style="color: #0169b4; text-decoration: none;">LinkedIn/victorhjsantiago</a><br>
        🐙 <a href="https://github.com/victorhjsantiago" style="color: #0169b4; text-decoration: none;">GitHub/victorhjsantiago</a>
      </td>
    </tr>
  </table>
</div>
