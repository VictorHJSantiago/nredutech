














import './commands'


Cypress.on('uncaught:exception', (err, runnable) => {
  


  if (err.message.includes('$ is not defined')) {
    return false;
  }
  
  if (err.message.includes('$(...).select2 is not a function')) {
    return false;
  }
  
  return true;
});