const { handleAssignmentExpression } = require('./handle-assignment-expression')
const { handleDefaultExpression } = require('./handle-default-expression')

function handleExpressionStatement(insertKeyFunc, debug, { expression }){
  debug(`expression from handler is ${JSON.stringify(expression)}`)
  if(expression.callee && expression.callee.type === 'MemberExpression'){ void(0)}
  else{
    switch (expression.type){
      case 'AssignmentExpression':
        handleAssignmentExpression(insertKeyFunc, debug, expression)
        break
      default:
        handleDefaultExpression(insertKeyFunc, debug, expression)
        break
    }
  }

}

module.exports = {
  handleExpressionStatement
}
