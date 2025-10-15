

NREduTech - Sistema de Gestão de Recursos Pedagógicos
NREduTech é um sistema web robusto e completo, desenvolvido como parte de um Trabalho de Conclusão de Curso (TCC), com o objetivo de otimizar a gestão de recursos didáticos, agendamentos e componentes curriculares em um Núcleo Regional de Educação (NRE). A plataforma centraliza informações, automatiza processos e facilita a comunicação entre diretores, professores e administradores, promovendo um uso mais eficiente dos recursos educacionais disponíveis.

O sistema foi projetado com uma arquitetura escalável e segura, utilizando o framework Laravel, e incorpora funcionalidades avançadas como um sistema de agendamento em tempo real, geração de relatórios dinâmicos e um robusto mecanismo de backup e restauração.

📜 Índice
Introdução e Justificativa

Funcionalidades Principais

Perfis de Usuário e Permissões

Professor

Diretor

Administrador

Destaque: Módulo de Agendamentos

Tecnologias Utilizadas

Licença

📜 Introdução e Justificativa
A gestão de recursos educacionais em ambientes escolares frequentemente enfrenta desafios como a falta de visibilidade sobre a disponibilidade de materiais, conflitos de agendamento, comunicação descentralizada e a dificuldade na geração de dados consolidados para análise. O NREduTech surge como uma solução tecnológica para suprir essa lacuna, oferecendo uma plataforma intuitiva e poderosa para o gerenciamento integrado de todos os recursos pedagógicos.

A escolha do framework Laravel como base tecnológica garante segurança, escalabilidade e manutenibilidade, seguindo as melhores práticas de desenvolvimento de software para criar uma aplicação confiável e de alto desempenho.

✨ Funcionalidades Principais
Gestão de Usuários: Sistema completo com 3 níveis de acesso (Professor, Diretor e Administrador).

Catálogo de Recursos: Cadastro e gerenciamento de todos os recursos didáticos e laboratórios.

Sistema de Agendamento: Calendário interativo para agendar recursos de forma simples e visual.

Disponibilidade em Tempo Real: Verificação assíncrona que impede conflitos de agendamento.

Central de Relatórios: Geração de relatórios dinâmicos com filtros avançados.

Exportação Múltipla: Exporte relatórios em PDF, XLSX, CSV, ODS, HTML ou um arquivo .zip consolidado.

Gestão de Localidades: Cadastro de Municípios, Escolas e Turmas.

Moderação de Conteúdo: Administradores aprovam novas disciplinas sugeridas por professores.

Backup e Restauração: Ferramenta segura para criar e restaurar backups completos da aplicação.

Segurança: Autenticação robusta, validação de dados com Form Requests e hashing de senhas com Argon2id.

👤 Perfis de Usuário e Permissões
O NREduTech opera com três níveis de acesso principais para garantir a segurança e a organização dos dados.

Professor
Visualiza o catálogo de recursos e laboratórios.

Realiza agendamentos de recursos para suas turmas através de um calendário interativo.

Gerencia (visualiza e cancela) seus próprios agendamentos.

Recebe notificações sobre o status de seus agendamentos.

Diretor
Possui todas as permissões de um Professor.

Gerencia usuários (professores) vinculados à sua instituição.

Tem uma visão abrangente de todos os agendamentos realizados em sua escola.

Pode cancelar agendamentos de qualquer professor de sua escola.

Cadastra e gerencia as turmas de sua escola.

Gera relatórios específicos de sua instituição.

Administrador
Acesso irrestrito a todas as funcionalidades do sistema.

Realiza a gestão completa de cadastros essenciais (Usuários, Municípios, Escolas, Recursos, Disciplinas).

Aprova novos cadastros e gerencia o status de todos os usuários.

Acessa a Central de Relatórios com filtros avançados e visão global.

Gerencia as configurações do sistema, incluindo a rotina de backup e restauração.

📅 Módulo de Agendamentos (Destaque)
O coração do sistema é seu módulo de agendamentos, projetado para ser intuitivo e à prova de falhas:

Calendário Interativo: Construído com FullCalendar.js, exibe todos os agendamentos de forma clara. Ao clicar em uma data, o sistema dispara uma requisição assíncrona para o back-end.

Verificação em Tempo Real: A resposta da requisição atualiza a interface exibindo os recursos disponíveis e os já agendados para aquele dia, sem recarregar a página.

Prevenção de Conflitos: A lógica no back-end (utilizando Form Requests) impede a criação de agendamentos conflitantes para o mesmo recurso e horário.

Notificações Automáticas: Usuários relevantes são notificados sobre novos agendamentos e cancelamentos, mantendo todos informados.

🛠️ Tecnologias Utilizadas
Back-end
Framework: Laravel

Linguagem: PHP

Banco de Dados: SQLite (padrão), com suporte a MySQL, MariaDB, PostgreSQL.

Build Tool: Vite.js

Dependências Principais (Back-end)
spatie/laravel-backup: Solução robusta para a funcionalidade de backup e restauração.

maatwebsite/excel: Biblioteca para importação e exportação de planilhas (XLSX, CSV, ODS).

barryvdh/laravel-dompdf: Wrapper do DomPDF para a geração de relatórios em PDF.

Front-end
Estilização: CSS puro com arquitetura modular.

JavaScript:

Vanilla JS: Para a maior parte da interatividade.

Alpine.js: Para reatividade em componentes específicos.

Axios: Para requisições assíncronas (AJAX).

Bibliotecas Externas (Front-end)
FullCalendar.js: Para a criação do calendário de agendamentos.

Chart.js: Para a visualização de dados em gráficos.

SweetAlert2: Para a criação de alertas e modais interativos.

🚀 Instalação e Configuração
Siga os passos abaixo para configurar o ambiente de desenvolvimento local.

Pré-requisitos:

PHP >= 8.1

Composer

Node.js e NPM

Um banco de dados (SQLite, MySQL, etc.)


📄 Licença
Este projeto é distribuído sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.