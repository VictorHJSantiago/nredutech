describe('Testes de Login', () => {

  beforeEach(() => {
    cy.visit('/login');
  });

  it('deve falhar ao tentar logar com credenciais inválidas', () => {
    cy.get('input[name="email"]').type('usuario@errado.com');
    cy.get('input[name="password"]').type('senhaerrada');
    cy.get('button[type="submit"]').click();
    cy.url().should('include', '/login');
    
    cy.contains('Estas credenciais não correspondem');
  });

  it('deve logar com sucesso com credenciais de admin', () => {
    
    const adminEmail = 'victorhenriquedejesussantiago@gmail.com';
    const adminPassword = 'password123';

    cy.get('input[name="email"]').type(adminEmail);
    cy.get('input[name="password"]').type(adminPassword);
    cy.get('button[type="submit"]').click();

    cy.url().should('eq', Cypress.config().baseUrl + '/'); 
    
    cy.contains('Dashboard');
  });

});