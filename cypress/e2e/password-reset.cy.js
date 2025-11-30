describe('Testes de Redefinição de Senha', () => {

  it('deve falhar ao solicitar link para um e-mail inexistente', () => {
    cy.visit('/forgot-password');
    cy.get('input[name="email"]').type('naoexiste@cypress.io');
    cy.get('button[type="submit"]').click();

    // Corrigido: Mensagem exata do lang/pt_BR/passwords.php
    cy.contains('Não encontramos um usuário com este endereço de e-mail.');
  });

  it('deve solicitar o link de redefinição com sucesso', () => {
    cy.visit('/forgot-password');
    
    // Corrigido: Usando o email de admin correto do seeder
    const adminEmail = 'victorhenriquedejesussantiago@gmail.com';
    cy.get('input[name="email"]').type(adminEmail);
    cy.get('button[type="submit"]').click();

    // Corrigido: Mensagem exata (com '!') do lang/pt_BR/passwords.php
    cy.contains('Enviamos um link de redefinição de senha para o seu e-mail!');
  });

  it('deve mostrar erro de token inválido na página de redefinição', () => {
    // Visitamos a página com um token falso. A primeira
    // validação do Laravel será sobre o token.
    cy.visit('/reset-password/token-falso-para-teste');

    cy.get('input[name="email"]').type('admin@admin.com');
    cy.get('input[name="password"]').type('novasenha123');
    cy.get('input[name="password_confirmation"]').type('outrasenha456');
    cy.get('button[type="submit"]').click();

    // Corrigido: O erro esperado é sobre o token, não sobre a senha.
    //
    cy.contains('Este token de redefinição de senha é inválido.');
  });

});