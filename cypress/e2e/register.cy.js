describe('Testes de Registro de Usuário', () => {

  beforeEach(() => {
    cy.visit('/register');
  });

  it('deve mostrar erros de validação para campos vazios', () => {
    cy.get('button[type="submit"]').click();

    cy.contains('O campo nome é obrigatório');
    cy.contains('O campo email é obrigatório');
    cy.contains('O campo senha é obrigatório');
  });

  it('deve mostrar erro de confirmação de senha', () => {
    cy.get('input[name="name"]').type('Usuário Teste');
    cy.get('input[name="email"]').type('teste@cypress.io');
    cy.get('input[name="password"]').type('senha123');
    
    cy.get('input[name="password_confirmation"]').type('senha456');
    
    cy.get('button[type="submit"]').click();

    cy.contains('O campo senha de confirmação não confere');
  });

  it('deve registrar um novo usuário com sucesso', () => {
    const email = `teste-${Date.now()}@cypress.io`;
    const senha = 'password123';

    cy.get('input[name="name"]').type('Usuário Cypress');
    cy.get('input[name="email"]').type(email);
    cy.get('input[name="password"]').type(senha);
    cy.get('input[name="password_confirmation"]').type(senha);
    cy.get('button[type="submit"]').click();
    
    cy.url().should('eq', Cypress.config().baseUrl + '/');
    
    cy.contains('Usuário Cypress');
  });

});