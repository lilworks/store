function handleClassDeclaration(insertKeyFunc, debug, { id }){
  if(!id) throw new Error('handleClassDeclaration:id was not passed')
  let { name } = id
  debug(`Class: ${name}`)
  insertKeyFunc(name)
}

module.exports = {
  handleClassDeclaration
}