function handleVariableDeclaration(insertKeyFunc, debug, { declarations }){
  let name
  let dec
  if(declarations[0].init && declarations[0].init.type) dec = declarations[0].init.type
  else dec = declarations[0].type
  switch (dec){
    case 'NewExpression':
    case 'CallExpression':
    case 'VariableDeclarator':
    case 'VariableDeclaration':
      name = declarations[0].id.name
      break
    default:
      try{
        name = declarations[0].init.callee.name
      }catch(e){
        name = declarations[0].id.name
      }
      break
  }
  debug(`Variable: ${name}`)
  insertKeyFunc(name)
}

module.exports = {
  handleVariableDeclaration
}