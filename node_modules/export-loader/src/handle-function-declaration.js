function handleFunctionDeclaration(insertKeyFunc, debug, { id, declarations }){
  if(!id && !declarations) throw new Error('handleFunctionDeclaration:id or declarations were not passed')
  let name
  if(id){
    name = id.name
  }else if(declarations){
    name = declarations[0].init.callee.name
  }
  debug(`Function: ${name}`)
  insertKeyFunc(name)
}

module.exports = {
  handleFunctionDeclaration
}