function handleExportNamedDeclaration(insertKeyFunc, debug, { declaration, specifiers }) {
  let name
  let type
  let id
  debug(`DECLERATION FROM EXPROT NAMED IS :${JSON.stringify(declaration)}`)
  if(declaration){
    type = declaration.type
    id = declaration.id
  }
  if(declaration && declaration.declarations){
    return declaration.declarations.map(dec=>insertKeyFunc(dec.id.name, true))
  }
  if ( type && type === 'FunctionDeclaration' ) {
    name = id.name
    return insertKeyFunc(name, true)
  }else {
    try{
      specifiers.map(spec=>insertKeyFunc(spec.exported.name, true))
    }catch(e){
      debug(`error in handleExportNamed:${e}`)
    }
  }
}

module.exports = {
  handleExportNamedDeclaration
}