// handlers
const { handleExportNamedDeclaration } = require('../src/handle-export-named-declaration')
const { handleClassDeclaration }       = require('../src/class-declaration')
const { handleExpressionStatement }    = require('../src/handle-expression-statement')
const { handleVariableDeclaration }    = require('../src/handle-variable-declaration')
const { handleFunctionDeclaration }    = require('../src/handle-function-declaration')
const { handleDefaultExpression }      = require('../src/handle-default-expression')
const { handleAssignmentExpression }   = require('../src/handle-assignment-expression')

// utils
const { variableDeclaration } = require('../test-utils/var-declaration.example')
const { functionDeclaration } = require('../test-utils/function-declaration.example')




//tests
describe('variable declaration tests',()=>{
  const insertNameFunction      = jest.fn()
  test('should handle variable declaration and call insert name', () => {
    expect(handleVariableDeclaration(insertNameFunction,console.log,variableDeclaration)).toBeFalsy()
    expect(insertNameFunction.mock.calls[0]).toEqual([variableDeclaration.declarations[0].id.name])
  });
})

describe('function declaration tests',()=>{
  const insertNameFunction      = jest.fn()
  test('should handle function declaration with id', () => {
    expect(handleFunctionDeclaration(insertNameFunction,console.log,functionDeclaration)).toBeFalsy()
    expect(insertNameFunction.mock.calls[0]).toEqual([functionDeclaration.id.name])
  });
  test('should handle function declaration with declarations array', () => {
    const functionCatchBlockDeclaration = {declarations:[{init:{callee:{name:"catchBlockFunctionName"}}}]}
    expect(handleFunctionDeclaration(insertNameFunction,console.log,functionCatchBlockDeclaration)).toBeFalsy()
    expect(insertNameFunction.mock.calls[1]).toEqual([functionCatchBlockDeclaration.declarations[0].init.callee.name])
  });
  test('should handle function declaration throw when no id or declarations passed', () => {
    expect(()=>handleFunctionDeclaration(insertNameFunction,console.log, {})).toThrowError('handleFunctionDeclaration:id or declarations were not passed')
    expect(insertNameFunction.mock.calls.length).toBe(2)
  });
})


describe('class declaration tests',()=>{
  test('should handle class declaration and call insert name', ()=>{
    const classDeclaration = {id:{name:'ClassName'}}
    const insertNameFunction = jest.fn()
    expect(handleClassDeclaration(insertNameFunction, console.log, classDeclaration)).toBeFalsy()
    expect(insertNameFunction.mock.calls[0]).toEqual([classDeclaration.id.name])
  })
  test('should handle class declaration and throw when id not passed', ()=>{
    const insertNameFunction = jest.fn()
    expect(()=>handleClassDeclaration(insertNameFunction, console.log, {})).toThrowError('handleClassDeclaration:id was not passed')
    expect(insertNameFunction.mock.calls.length).toBe(0)
  })
})
// function handleClassDeclaration(insertKeyFunc, debug, { id }){
//   if(!id) debug(`id was not passed in ${__filename}`)
//   let { name } = id
//   debug(`Class: ${name}`)
//   insertKeyFunc(name)
// }



// function handleAssignmentExpression(insertKeyFunc, debug, expression){
//   let name
//   try{
//     if(expression && expression.left && expression.left.name){
//       name = expression.left.name
//     }else if(expression && expression.left && expression.left.object && expression.left.object.name && expression.left.object.name==='exports'){
//       // name = expression.left.property.name
//       name = 'ignoring for now...'
//       debug(`received exports object:${name}`)
//       // return insertKeyFunc(name, true)
//     }else{
//       name = 'ignoring...'
//     }
//   }catch(e){
//     name =  expression.callee.name
//   }
//   debug(`Expression: ${name}`)
//   insertKeyFunc(name)
// }