function handleAssignmentExpression(insertKeyFunc, debug, expression){
  let name
  try{
    if(expression && expression.left && expression.left.name){
      name = expression.left.name
    }else if(expression && expression.left && expression.left.object && expression.left.object.name && expression.left.object.name==='exports'){
      // name = expression.left.property.name
      name = 'ignoring for now...'
      debug(`received exports object:${name}`)
      // return insertKeyFunc(name, true)
    }else{
      name = 'ignoring...'
    }
  }catch(e){
    name =  expression.callee.name
  }
  debug(`Expression: ${name}`)
  insertKeyFunc(name)
}

module.exports = {
  handleAssignmentExpression
}