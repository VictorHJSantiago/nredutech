// ***********************************************************
// This example support/e2e.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import './commands'

// cypress/support/e2e.js

Cypress.on('uncaught:exception', (err, runnable) => {
  // Impede o Cypress de falhar o teste por erros
  // da aplicação que não são críticos para o teste em si.
  
  // Ignora o erro do jQuery
  if (err.message.includes('$ is not defined')) {
    return false;
  }
  
  // Ignora o erro do Select2
  if (err.message.includes('$(...).select2 is not a function')) {
    return false;
  }
  
  // Deixa todos os outros erros falharem o teste
  return true;
});