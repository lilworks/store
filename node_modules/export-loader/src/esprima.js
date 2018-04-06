const debug                            = require('debug')('parser')
const { handleExportNamedDeclaration } = require('./handle-export-named-declaration')
const { handleClassDeclaration }       = require('./class-declaration')
const { handleExpressionStatement }    = require('./handle-expression-statement')
const { handleVariableDeclaration }    = require('./handle-variable-declaration')
const { handleFunctionDeclaration }    = require('./handle-function-declaration')
/*TODO
 * check various file types against func to match all possible cases for exporting
 * */
function generateExports(esprima) {
  return function (fileName, data){
    if(!data) throw new Error('No data was received!')
    let names = {}
    const insertName = hashMapToInject(names)
    const { body } = esprima.parse( data, { sourceType: 'module'} )
    for( let declaration of body ) {
      const { type } = declaration
      debug(type)
      switch ( type ) {
        case 'ExportNamedDeclaration':
          handleExportNamedDeclaration(insertName, debug, declaration)
          break
        case 'ClassDeclaration':
          handleClassDeclaration(insertName, debug, declaration)
          break
        case 'ExpressionStatement':
          handleExpressionStatement(insertName, debug, declaration)
          break
        case 'VariableDeclaration':
          handleVariableDeclaration(insertName, debug, declaration)
          break
        case 'FunctionDeclaration':
          handleFunctionDeclaration(insertName, debug, declaration)
          break
        case 'ExportDefaultDeclaration':
          debug(`Export default received, need to think what to do with it`)
          break;
        default:
          debug(`An unhandled declaration type received: ${type}`)
      }
    }
    debug(`names:${JSON.stringify(names)}`)
    let namesKeys = Object.keys(names)
    const finalExports = `export {\n${namesKeys}\n}`
    return { finalExports, fileName, names:namesKeys }

  }
}
function hashMapToInject(hashmap){
  return function insertName(name, isExport){
    if(typeof name !== 'string') debug(`name: ${JSON.stringify(name)} is not a string!!!`)
    if( name[0] === name[0].toUpperCase() || isExport ){
      if(hashmap[name]){
        d(`key:${name} already exists!`)
      }else{
        hashmap[name] = name
      }
    }
  }

}


module.exports = {
  generateExports,
  hashMapToInject
}
