function handleDefaultExpression(insertKeyFunc, debug, expression){
  let name
  try{
    name = expression.callee.id.name
  }catch(e){
    name =  expression.callee.name
  }
  debug(`Expression: ${name}`)
  insertKeyFunc(name)
}

module.exports = {
  handleDefaultExpression
}