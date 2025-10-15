<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NREduTech - Sistema de Gestão de Recursos Pedagógicos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* --- Reset Básico e Configurações Globais --- */
        :root {
            --color-primary: #2563eb; /* Azul para links e destaques */
            --color-dark: #111827;    /* Preto para títulos */
            --color-medium: #4b5563;  /* Cinza escuro para texto */
            --color-light: #f9fafb;   /* Cinza claro para fundo */
            --color-white: #ffffff;
            --color-border: #e5e7eb;  /* Cor da borda */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-light);
            color: var(--color-medium);
            line-height: 1.6;
        }

        /* --- Estrutura e Container Principal --- */
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* --- Header --- */
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 2.5rem;
            color: var(--color-dark);
            margin-bottom: 8px;
        }
        .header .subtitle {
            font-size: 1.25rem;
            font-weight: 500;
            margin-bottom: 16px;
        }
        .header .badges {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .header .badges img {
            height: 28px;
        }
        .header p {
            max-width: 700px;
            margin: 20px auto 0;
        }
        
        /* --- Hero Image --- */
        .hero-image {
            margin-bottom: 40px;
            text-align: center;
        }
        .hero-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--color-border);
        }

        /* --- Seções de Conteúdo --- */
        .section {
            background-color: var(--color-white);
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 8px;
            border: 1px solid var(--color-border);
            box-shadow: var(--shadow-sm);
        }
        .section-title {
            font-size: 1.8rem;
            color: var(--color-dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--color-primary);
            display: inline-block;
        }

        /* --- Lista de Funcionalidades --- */
        .features-list {
            list-style: none;
            padding-left: 0;
        }
        .features-list li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        .features-list li::before {
            content: '✅';
            margin-right: 12px;
            font-size: 1.2rem;
            margin-top: -2px;
        }

        /* --- Tabela de Tecnologias --- */
        .tech-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .tech-table th, .tech-table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid var(--color-border);
        }
        .tech-table th {
            background-color: var(--color-light);
            color: var(--color-dark);
            font-weight: 700;
        }
        .tech-table tr:last-child td {
            border-bottom: none;
        }
        .tech-table td:first-child {
            font-weight: 500;
            color: var(--color-dark);
        }
        
        /* --- Bloco de Código --- */
        .code-block {
            background-color: var(--color-dark);
            color: #d1d5db; /* Cinza claro para texto do código */
            padding: 20px;
            border-radius: 6px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
            white-space: pre-wrap; /* Quebra de linha */
            margin-top: 15px;
        }
        .code-block strong {
            color: var(--color-white);
        }
        
        /* --- Footer --- */
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
            color: #9ca3af; /* Cinza claro */
        }
        .footer a {
            color: var(--color-primary);
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <header class="header">
            <h1>NREduTech</h1>
            <p class="subtitle">Sistema de Gestão de Recursos Pedagógicos</p>
            <div class="badges">
                <img src="https://img.shields.io/badge/Status-Concluído-brightgreen?style=for-the-badge" alt="Status Concluído">
                <img src="https://img.shields.io/badge/Licença-MIT-blue?style=for-the-badge" alt="Licença MIT">
            </div>
            <p>Uma plataforma web integrada, desenvolvida com Laravel, para otimizar o agendamento, a alocação e a análise de recursos educacionais em Núcleos Regionais de Educação.</p>
        </header>

        <div class="hero-image">
            <img src="https://i.imgur.com/z4iQo7l.png" alt="Dashboard do sistema NREduTech">
        </div>

        <main>
            <section class="section">
                <h2 class="section-title">🎯 O Projeto</h2>
                <p>A gestão de recursos em instituições de ensino é frequentemente fragmentada, resultando em conflitos de agendamento, baixa utilização de materiais e falta de dados para decisões estratégicas. O NREduTech foi criado para resolver esses desafios, oferecendo uma solução centralizada que empodera administradores, diretores e professores.</p>
                <p style="margin-top: 10px;">O sistema transforma um processo manual e suscetível a erros em um fluxo de trabalho digital, eficiente e transparente, garantindo que os recursos pedagógicos sejam aproveitados ao máximo.</p>
            </section>

            <section class="section">
                <h2 class="section-title">✨ Funcionalidades em Destaque</h2>
                <ul class="features-list">
                    <li><strong>Controle de Acesso Granular (RBAC):</strong> Sistema de permissões com três níveis (Professor, Diretor, Administrador).</li>
                    <li><strong>Agendamento Inteligente:</strong> Calendário interativo com verificação de disponibilidade em tempo real via AJAX.</li>
                    <li><strong>Dashboard Analítico e Relatórios:</strong> Central de relatórios com filtros dinâmicos e visualização de dados em gráficos.</li>
                    <li><strong>Exportação de Dados Flexível:</strong> Geração de relatórios em múltiplos formatos (`PDF`, `XLSX`, `CSV`, `ODS`).</li>
                    <li><strong>Mecanismo de Backup e Restauração:</strong> Ferramenta crítica para criar e restaurar backups completos da aplicação.</li>
                    <li><strong>Segurança Robusta:</strong> Validação com `Form Requests`, proteção CSRF/XSS, e hashing de senhas com **Argon2id**.</li>
                </ul>
            </section>

            <section class="section">
                <h2 class="section-title">🛠️ Arquitetura e Decisões Técnicas</h2>
                <p>A pilha de tecnologias foi escolhida para garantir performance, segurança e manutenibilidade, seguindo as melhores práticas do mercado.</p>
                
                <h3>Back-end</h3>
                <table class="tech-table">
                    <thead>
                        <tr>
                            <th>Tecnologia</th>
                            <th>Finalidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>PHP 8+</td>
                            <td>Linguagem base, oferecendo tipagem forte e melhorias de performance.</td>
                        </tr>
                        <tr>
                            <td>Laravel</td>
                            <td>Framework principal, escolhido por seu ecossistema robusto e código limpo.</td>
                        </tr>
                        <tr>
                            <td>SQLite / MySQL</td>
                            <td>Banco de dados relacional para desenvolvimento e produção.</td>
                        </tr>
                        <tr>
                            <td>Vite.js</td>
                            <td>Build tool para compilação ágil e hot-reloading de assets.</td>
                        </tr>
                    </tbody>
                </table>

                <h3 style="margin-top: 30px;">Front-end</h3>
                <table class="tech-table">
                     <thead>
                        <tr>
                            <th>Tecnologia</th>
                            <th>Finalidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Vanilla JS</td>
                            <td>Garante leveza e controle total da interatividade, sem frameworks pesados.</td>
                        </tr>
                        <tr>
                            <td>Alpine.js</td>
                            <td>Adicionado para reatividade em componentes específicos, como modais.</td>
                        </tr>
                        <tr>
                            <td>Axios</td>
                            <td>Cliente HTTP para todas as requisições assíncronas com o back-end.</td>
                        </tr>
                        <tr>
                            <td>CSS Modular</td>
                            <td>Arquitetura de CSS puro para garantir manutenibilidade e escalabilidade.</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>

        <footer class="footer">
            <p>Este projeto está sob a licença MIT. Para mais detalhes, consulte o arquivo <a href="#">LICENSE</a>.</p>
        </footer>
    </div>

</body>
</html>