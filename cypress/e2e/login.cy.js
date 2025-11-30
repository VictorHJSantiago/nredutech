describe('Testes de Login', () => {

  beforeEach(() => {
    cy.visit('/login');
  });

  it('deve falhar ao tentar logar com credenciais inválidas', () => {
    cy.get('input[name="email"]').type('usuario@errado.com');
    cy.get('input[name="password"]').type('senhaerrada');
    cy.get('button[type="submit"]').click();
    cy.url().should('include', '/login');
    
    // Corrigido: Esta é a mensagem exata do arquivo lang/pt_BR/auth.php
    cy.contains('Estas credenciais não correspondem');
  });

  it('deve logar com sucesso com credenciais de admin', () => {
    
    // Corrigido: Usando as credenciais exatas do seeder
    const adminEmail = 'victorhenriquedejesussantiago@gmail.com';
    const adminPassword = 'password123'; // A senha que definimos no Passo 1

    cy.get('input[name="email"]').type(adminEmail);
    cy.get('input[name="password"]').type(adminPassword);
    cy.get('button[type="submit"]').click();

    // Corrigido: A URL base já inclui '127.0.0.1:8001'
    cy.url().should('eq', Cypress.config().baseUrl + '/'); 
    
    cy.contains('Dashboard'); //
  });

});